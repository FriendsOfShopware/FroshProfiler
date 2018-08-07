<?php
declare(strict_types=1);

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
     * @return bool
     * @throws \Exception
     */
    public function hasHeader($header)
    {
        return $this->request->getHeader($header) !== false;
    }

    /**
     * @param $headerName
     * @return false|string
     * @throws \Exception
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
