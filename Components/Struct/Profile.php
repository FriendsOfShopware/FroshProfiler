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
     * @var string|null
     */
    private $id;

    /**
     * @var int
     */
    private $templateCalls = 0;

    /**
     * @var array
     */
    private $templatesRendered = [];

    /**
     * @var int
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
     * @var \Throwable|null
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
     * @var int
     */
    private $startTime = 0;

    /**
     * A collection of dumped data, each being an assoc array with keys 'data', 'name', 'file', 'line', 'fileLink', 'fileExcerpt'.
     *
     * @see \FroshProfiler\Components\Collectors\DumpCollector::dump
     *
     * @var array
     */
    private $dump = [];

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function increaseTemplateBlockCalls(): void
    {
        ++$this->templateBlockCalls;
    }

    public function setRenderTime(float $time): void
    {
        $this->templateRenderTime = $time;
    }

    public function addTemplateCall(string $path): void
    {
        ++$this->templateCalls;
        $path = $this->normalizePath($path);

        if (!isset($this->templatesRendered[$path])) {
            $this->templatesRendered[$path] = 1;
        } else {
            ++$this->templatesRendered[$path];
        }
    }

    public function addMail(array $mail): void
    {
        $this->mails[] = $mail;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function setDbQueries(array $dbQueries): void
    {
        $this->dbQueries = $dbQueries;
    }

    public function setDump(array $dump): void
    {
        $this->dump = $dump;
    }

    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function setException(\Throwable $exception): void
    {
        $this->exception = $exception;
    }

    public function setPhp(array $php): void
    {
        $this->php = $php;
    }

    public function setTemplate(array $template): void
    {
        $this->template = $template;
        $this->template['renderedTemplates'] = $this->templatesRendered;
        $this->template['blockCalls'] = $this->templateBlockCalls;
        $this->template['templateCalls'] = $this->templateCalls;
        $this->template['renderTime'] = $this->templateRenderTime;
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes + $this->attributes;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function jsonSerialize(): array
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
     * Resets the Profile
     */
    public function reset(): void
    {
        $this->id = null;
        $this->templateCalls = 0;
        $this->templatesRendered = [];
        $this->templateBlockCalls = 0;
        $this->templateRenderTime = 0;
        $this->config = [];
        $this->mails = [];
        $this->dbQueries = [];
        $this->events = [];
        $this->exception = null;
        $this->attributes = [];
        $this->php = [];
        $this->template = [];
        $this->user = [];
        $this->dump = [];
        $this->startTime = 0;
    }

    private function normalizePath(string $path): string
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
