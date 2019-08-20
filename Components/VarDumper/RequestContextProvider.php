<?php

namespace FroshProfiler\Components\VarDumper;

use Enlight_Controller_Front;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

/**
 * Class RequestContextProvider
 *
 * @author Soner Sayakci <shyim@posteo.de>
 */
class RequestContextProvider implements ContextProviderInterface
{
    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * RequestContextProvider constructor.
     *
     * @param Enlight_Controller_Front $front
     *
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function __construct(Enlight_Controller_Front $front)
    {
        $this->front = $front;
    }

    /**
     * @return array|null Context data or null if unable to provide any context
     */
    public function getContext(): ?array
    {
        $request = $this->front->Request();

        if ($request === null) {
            return null;
        }

        return [
            'uri' => $request->getScheme() . '//' . $request->getHttpHost() . $request->getRequestUri(),
            'method' => $request->getMethod(),
            'controllerName' => $request->getControllerName(),
            'identifier' => spl_object_hash($request),
        ];
    }
}
