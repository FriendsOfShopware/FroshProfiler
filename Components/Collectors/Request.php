<?php

namespace FroshProfiler\Components\Collectors;

class Request
{
    /**
     * @var \Enlight_Controller_Request_Request
     */
    private $request;

    public function __construct(\Enlight_Controller_Request_Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $header
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function hasHeader($header)
    {
        return $this->request->getHeader($header) !== false;
    }

    /**
     * @param string $headerName
     *
     * @throws \Exception
     *
     * @return false|string
     */
    public function getHeader($headerName)
    {
        return $this->request->getHeader($headerName);
    }

    /**
     * @return string
     */
    public function getRequestFormat()
    {
        return 'html';
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return $this->request->isXmlHttpRequest();
    }
}
