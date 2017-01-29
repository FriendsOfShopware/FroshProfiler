<?php

namespace ShyimProfiler\Models;

use DateTime;
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_profiler")
 * @ORM\Entity
 */
class Profile extends ModelEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", nullable=false)
     * @ORM\Id
     */
    private $token;

    /**
     * @var int
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var int
     * @ORM\Column(name="ip", type="string", nullable=false)
     */
    private $ip;

    /**
     * @var int
     * @ORM\Column(name="method", type="string", nullable=false)
     */
    private $method;

    /**
     * @var int
     * @ORM\Column(name="url", type="string", nullable=false)
     */
    private $url;

    /**
     * @var DateTime
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param int $ip
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param int $method
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return int
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param int $url
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function setTime($time)
    {
        $this->time = $time;
    }
}
