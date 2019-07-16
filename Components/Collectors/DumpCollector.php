<?php

/*
 * This file originally was part of the Symfony package
 * and has been adjusted to work with the Frosh profiler.
 * Its original location is https://github.com/symfony/http-kernel/blob/8a4248b2/DataCollector/DumpDataCollector.php.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code; i.e.
 * https://github.com/symfony/http-kernel/blob/8a4248b2/LICENSE (MIT)
 */

namespace FroshProfiler\Components\Collectors;

use FroshProfiler\Components\Struct\Profile;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 * Adjustments by @author Adrian Föder <adrian.foeder@netformic.de>
 */
class DumpCollector implements CollectorInterface, DataDumperInterface, \Serializable
{
    private $data = [];
    private $stopwatch;
    private $fileLinkFormat;
    private $dataCount = 0;
    private $isCollected = true;
    private $clonesCount = 0;
    private $clonesIndex = 0;
    private $rootRefs;
    private $charset;
    private $dumper;
    private $dumperIsInjected;

    public function __construct(Stopwatch $stopwatch = null, $fileLinkFormat = null, $charset = null, DataDumperInterface $dumper = null)
    {
        $this->stopwatch = $stopwatch;
        $this->fileLinkFormat = $fileLinkFormat ?: ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        $this->charset = $charset ?: ini_get('php.output_encoding') ?: ini_get('default_charset') ?: 'UTF-8';
        $this->dumper = $dumper;
        $this->dumperIsInjected = $dumper !== null;

        // All clones share these properties by reference:
        $this->rootRefs = [
            &$this->data,
            &$this->dataCount,
            &$this->isCollected,
            &$this->clonesCount,
        ];
    }

    public function __destruct()
    {
        if ($this->clonesCount-- === 0 && !$this->isCollected && $this->data) {
            $this->clonesCount = 0;
            $this->isCollected = true;

            $h = headers_list();
            $i = count($h);
            array_unshift($h, 'Content-Type: ' . ini_get('default_mimetype'));
            while (stripos($h[$i], 'Content-Type:') !== 0) {
                --$i;
            }

            if (!\in_array(PHP_SAPI, ['cli', 'phpdbg'], true) && stripos($h[$i], 'html')) {
                $this->dumper = new HtmlDumper('php://output', $this->charset);
            } else {
                $this->dumper = new CliDumper('php://output', $this->charset);
            }

            foreach ($this->data as $i => $dump) {
                $this->data[$i] = null;
                $this->doDump($dump['data'], $dump['name'], $dump['file'], $dump['line']);
            }

            $this->data = [];
            $this->dataCount = 0;
        }
    }

    public function __clone()
    {
        $this->clonesIndex = ++$this->clonesCount;
    }

    public function dump(Data $data)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('dump');
        }
        if ($this->isCollected && !$this->dumper) {
            $this->isCollected = false;
        }

        $trace = DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS;
        if (\PHP_VERSION_ID >= 50400) {
            $trace = debug_backtrace($trace, 7);
        } else {
            $trace = debug_backtrace($trace);
        }

        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        $name = false;
        $fileExcerpt = false;

        for ($i = 1; $i < 7; ++$i) {
            if (isset($trace[$i]['class'], $trace[$i]['function'])
                && $trace[$i]['function'] === 'dump'
                && $trace[$i]['class'] === 'Symfony\Component\VarDumper\VarDumper'
            ) {
                $file = $trace[$i]['file'];
                $line = $trace[$i]['line'];

                while (++$i < 7) {
                    if (isset($trace[$i]['function'], $trace[$i]['file']) && empty($trace[$i]['class']) && strpos($trace[$i]['function'], 'call_user_func') !== 0) {
                        $file = $trace[$i]['file'];
                        $line = $trace[$i]['line'];

                        break;
                    }
                    if (isset($trace[$i]['object'])) {
                        // todo: support dump() invocations from template context
                        break;
                    }
                }
                break;
            }
        }

        if ($name === false) {
            $name = str_replace('\\', '/', $file);
            $name = substr($name, strrpos($name, '/') + 1);
        }

        if (isset($_COOKIE['disableProfile']) && $_COOKIE['disableProfile'] === '1') {
            // when profiling is disabled these dump() invocation will get intercepted
            // and swallowed, therefore instantiatiate an HTML dumper for doDump being invoked.
            $this->dumper = new HtmlDumper('php://output', $this->charset);
        }
        if ($this->dumper) {
            $this->doDump($data, $name, $file, $line);
        }

        $fileLink =
            $this->fileLinkFormat && is_file($file)
                ? strtr($this->fileLinkFormat, ['%f' => $file, '%l' => $line])
                : false;

        $this->data[] = compact('data', 'name', 'file', 'line', 'fileLink', 'fileExcerpt');

        ++$this->dataCount;

        if ($this->stopwatch) {
            $this->stopwatch->stop('dump');
        }
    }

    public function collect(\Enlight_Controller_Action $controller, Profile $profile): void
    {
        $request = $controller->Request();
        $response = $controller->Response();

        // Sub-requests and programmatic calls stay in the collected profile.
        if ($this->dumper || $request->isXmlHttpRequest() || $request->headers->has('Origin')) {
            return;
        }

        // In all other conditions that remove the web debug toolbar, dumps are written on the output.
        if ($response->isRedirection()
            || $request->getRequestFormat() !== 'html'
            || strripos($response->getContent(), '</body>') === false
            || $controller->Request()->getCookie('disableProfile', false)
            || ($response->headers->has('Content-Type') && strpos($response->headers->get('Content-Type'), 'html') === false)
        ) {
            if ($response->headers->has('Content-Type') && strpos($response->headers->get('Content-Type'), 'html') !== false) {
                $this->dumper = new HtmlDumper('php://output', $this->charset);
            } else {
                $this->dumper = new CliDumper('php://output', $this->charset);
            }

            foreach ($this->data as $dump) {
                $this->doDump($dump['data'], $dump['name'], $dump['file'], $dump['line']);
            }
        }

        $profile->setDump([
            'count' => $this->getDumpsCount(),
            'html' => $this->getDumps('html'),
        ]);
    }

    public function serialize()
    {
        if ($this->clonesCount !== $this->clonesIndex) {
            return 'a:0:{}';
        }

        $this->data[] = $this->fileLinkFormat;
        $this->data[] = $this->charset;
        $ser = serialize($this->data);
        $this->data = [];
        $this->dataCount = 0;
        $this->isCollected = true;
        if (!$this->dumperIsInjected) {
            $this->dumper = null;
        }

        return $ser;
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
        $charset = array_pop($this->data);
        $fileLinkFormat = array_pop($this->data);
        $this->dataCount = count($this->data);
        self::__construct($this->stopwatch, $fileLinkFormat, $charset);
    }

    public function getDumpsCount()
    {
        return $this->dataCount;
    }

    public function getDumps($format, $maxDepthLimit = -1, $maxItemsPerDepth = -1)
    {
        $data = fopen('php://memory', 'r+b');

        if ($format === 'html') {
            $dumper = new HtmlDumper($data, $this->charset);
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid dump format: %s', $format));
        }
        $dumps = [];

        foreach ($this->data as $dump) {
            if (method_exists($dump['data'], 'withMaxDepth')) {
                $dumper->dump($dump['data']->withMaxDepth($maxDepthLimit)->withMaxItemsPerDepth($maxItemsPerDepth));
            } else {
                // getLimitedClone is @deprecated, to be removed in 3.0
                $dumper->dump($dump['data']->getLimitedClone($maxDepthLimit, $maxItemsPerDepth));
            }
            $dump['data'] = stream_get_contents($data, -1, 0);
            ftruncate($data, 0);
            rewind($data);
            $dumps[] = $dump;
        }

        return $dumps;
    }

    public function getName(): string
    {
        return 'dump';
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/dump.tpl';
    }

    private function doDump($data, $name, $file, $line)
    {
        if (\PHP_VERSION_ID >= 50400 && $this->dumper instanceof CliDumper) {
            $contextDumper = function ($name, $file, $line, $fileLinkFormat) {
                if ($this instanceof HtmlDumper) {
                    if ($file) {
                        $s = $this->style('meta', '%s');
                        $name = strip_tags($this->style('', $name));
                        $file = strip_tags($this->style('', $file));
                        if ($fileLinkFormat) {
                            $link = strtr(strip_tags($this->style('', $fileLinkFormat)), ['%f' => $file, '%l' => (int) $line]);
                            $name = sprintf('<a href="%s" title="%s">' . $s . '</a>', $link, $file, $name);
                        } else {
                            $name = sprintf('<abbr title="%s">' . $s . '</abbr>', $file, $name);
                        }
                    } else {
                        $name = $this->style('meta', $name);
                    }
                    $this->line = $name . ' on line ' . $this->style('meta', $line) . ':';
                } else {
                    $this->line = $this->style('meta', $name) . ' on line ' . $this->style('meta', $line) . ':';
                }
                $this->dumpLine(0);
            };
            $contextDumper = $contextDumper->bindTo($this->dumper, $this->dumper);
            $contextDumper($name, $file, $line, $this->fileLinkFormat);
        } else {
            $cloner = new VarCloner();
            $this->dumper->dump($cloner->cloneVar($name . ' on line ' . $line . ':'));
        }
        $this->dumper->dump($data);
    }

    private function htmlEncode($s)
    {
        $html = '';

        $dumper =
            new HtmlDumper(
                function ($line) use (&$html) {
                    $html .= $line;
                },
                $this->charset
            );
        $dumper->setDumpHeader('');
        $dumper->setDumpBoundaries('', '');

        $cloner = new VarCloner();
        $dumper->dump($cloner->cloneVar($s));

        return substr(strip_tags($html), 1, -1);
    }
}
