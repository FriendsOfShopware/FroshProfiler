<?php
declare(strict_types=1);

namespace FroshProfiler\Components\Collectors;

class Response
{
    /**
     * @var \Enlight_Controller_Response_ResponseHttp
     */
    private $response;

    public function __construct(\Enlight_Controller_Response_ResponseHttp $response)
    {
        $this->response = $response;
    }

    /**
     * @param string $headerName
     * @return bool
     */
    public function hasHeader($headerName)
    {
        return array_key_exists($headerName, $this->response->getHeaders());
    }

    /**
     * @param string $headerName
     * @return string|array
     */
    public function getHeader($headerName)
    {
        return $this->response->getHeaders()[$headerName];
    }

    /**
     * @return bool
     */
    public function isRedirection()
    {
        return $this->response->getHttpResponseCode() >= 300 && $this->response->getHttpResponseCode() < 400;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->response->getBody();
    }
}
