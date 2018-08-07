<?php

namespace FroshProfiler\Components\Struct;

use Exception;
use JsonSerializable;

/**
 * Class Profile
 */
class Profile implements JsonSerializable
{
    /**
     * @var
     */
    private $id = null;

    /**
     * @var int
     */
    private $templateCalls = 0;

    /**
     * @var array
     */
    private $templatesRendered = [];

    /**
     * @var array
     */
    private $templateBlockCalls = 0;

    /**
     * @var float
     */
    private $templateRenderTime = 0;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $mails = [];

    /**
     * @var array
     */
    private $dbQueries = [];

    /**
     * @var array
     */
    private $events;

    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $php = [];

    /**
     * @var array
     */
    private $template = [];

    /**
     * @var array
     */
    private $user = [];

    /**
     * A collection of dumped data, each being an assoc array with keys 'data', 'name', 'file', 'line', 'fileLink', 'fileExcerpt'.
     *
     * @see \FroshProfiler\Components\Collectors\DumpCollector::dump
     *
     * @var array
     */
    private $dump = [];

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function increaseTemplateBlockCalls()
    {
        ++$this->templateBlockCalls;
    }

    /**
     * @param float $time
     */
    public function setRenderTime($time)
    {
        $this->templateRenderTime = $time;
    }

    /**
     * @param string $path
     */
    public function addTemplateCall($path)
    {
        ++$this->templateCalls;
        $path = $this->normalizePath($path);

        if (!isset($this->templatesRendered[$path])) {
            $this->templatesRendered[$path] = 1;
        } else {
            ++$this->templatesRendered[$path];
        }
    }

    /**
     * @param array $mail
     */
    public function addMail(array $mail)
    {
        $this->mails[] = $mail;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $dbQueries
     */
    public function setDbQueries(array $dbQueries)
    {
        $this->dbQueries = $dbQueries;
    }

    /**
     * @param array $dump
     */
    public function setDump(array $dump)
    {
        $this->dump = $dump;
    }

    /**
     * @param array $events
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
    }

    /**
     * @param Exception $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @param array $php
     */
    public function setPhp($php)
    {
        $this->php = $php;
    }

    /**
     * @param array $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        $this->template['renderedTemplates'] = $this->templatesRendered;
        $this->template['blockCalls'] = $this->templateBlockCalls;
        $this->template['templateCalls'] = $this->templateCalls;
        $this->template['renderTime'] = $this->templateRenderTime;
    }

    /**
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes + $this->attributes;
    }

    /**
     * @return array|mixed
     *
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function jsonSerialize()
    {
        $result = [
            'template' => $this->template,
            'config' => $this->config,
            'db' => $this->dbQueries,
            'dump' => $this->dump,
            'events' => $this->events,
            'php' => $this->php,
            'user' => $this->user,
        ];

        $result = array_merge($result, $this->attributes);

        return $result;
    }

    /**
     * @param string $path
     *
     * @return bool|string
     */
    private function normalizePath($path)
    {
        if (strpos($path, 'frontend') !== false) {
            $pos = strpos($path, 'frontend');

            return substr($path, $pos);
        }

        if (strpos($path, 'widgets') !== false) {
            $pos = strpos($path, 'widgets');

            return substr($path, $pos);
        }

        return $path;
    }
}
