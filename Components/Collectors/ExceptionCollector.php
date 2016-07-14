<?php
namespace ShyimProfiler\Components\Collectors;

class ExceptionCollector implements CollectorInterface
{
    public function getName()
    {
        return 'exception';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $error = $controller->Request()->getParam('error_handler');

        if ($error && isset($error->exception)) {
            return [
                'exception' => $error->exception
            ];
        }

        return [];
    }

    public function getToolbarTemplate()
    {
        return null;
    }
}
