<?php

namespace ShyimProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

class Collector implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    private $renderedTemplates = [];
    private $mails = [];
    private $templateCalls = 0;
    private $blockCalls = 0;
    private $renderTime = 0;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Widgets' => 'onPostDispatch',
            'Profiler_Smarty_Render' => 'onRender',
            'Profiler_Smarty_Render_Block' => 'onRenderBlock',
            'Profiler_Smarty_RenderTime' => 'onRenderTime',
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onDispatchLoopShutdown',
            'Enlight_Components_Mail_Send' => 'onSendMails'
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        if (strtolower($controller->Request()->getControllerName()) == 'profiler' || $this->container->has('profileId')) {
            return;
        }

        if ($controller->Request()->getModuleName() == 'frontend') {
            $profileId = uniqid();
        } else {
            $profileId = $controller->Request()->getHeader('X-Profiler');
        }

        $view = $controller->View();
        $view->addTemplateDir($this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views');
        $view->assign('sProfilerID', $profileId);

        $this->container->get('shyim_profiler.smarty_extensions')->addPlugins($view->Engine());
        $this->container->set('profileId', $profileId);
        $this->container->set('profileController', $controller);
    }

    public function onRender(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->templateCalls++;
        $name = $this->normalizePath($eventArgs->get('name'));

        if (!isset($this->renderedTemplates[$name])) {
            $this->renderedTemplates[$name] = 1;
        } else {
            $this->renderedTemplates[$name]++;
        }
    }

    public function onRenderBlock()
    {
        $this->blockCalls++;
    }

    public function onRenderTime(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->renderTime = $eventArgs->get('time');
    }

    public function onDispatchLoopShutdown(\Enlight_Event_EventArgs $args)
    {
        if (!$this->container->has('profileId')) {
            return;
        }

        if (!$this->container->has('front')) {
            return;
        }

        /** @var \Enlight_Controller_Response_ResponseHttp $response */
        $response = $args->get('response');

        /** @var \Enlight_Controller_Action $controller */
        $controller = $this->container->get('profileController');

        $profileTemplate = [];
        $profileTemplate['renderedTemplates'] = $this->renderedTemplates;
        $profileTemplate['blockCalls'] = $this->blockCalls;
        $profileTemplate['templateCalls'] = $this->templateCalls;
        $profileTemplate['renderTime'] = $this->renderTime;

        $profileData = $this->container->get('shyim_profiler.collector')->collectInformation($controller);
        $profileData['template'] = array_merge($profileData['template'], $profileTemplate);
        $profileData['mails'] = $this->mails;

        $this->container->get('shyim_profiler.collector')->saveCollectInformation(
            $this->container->get('profileId'),
            $profileData,
            $controller->Request()->getModuleName() == 'widgets'
        );

        if ($controller->Request()->getModuleName() == 'frontend') {
            $view = $this->container->get('template');
            $view->assign('sProfiler', $profileData);
            $view->assign('sProfilerCollectors', $this->container->get('shyim_profiler.collector')->getCollectors());
            $view->assign('sProfilerID', $this->container->get('profileId'));
            $view->assign('sProfilerTime', round(microtime(true) - STARTTIME, 3));

            $view->addTemplateDir($this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views/');
            $profileTemplate = $view->fetch('@Profiler/index.tpl');

            $content = $response->getBody();

            $content = str_replace('</body>', $profileTemplate . '</body>', $content);
            $response->setBody($content);
        }
    }

    private function normalizePath($path)
    {
        if (strstr($path, 'frontend')) {
            $pos = strpos($path, 'frontend');
            $path = substr($path, $pos);
        }

        if (strstr($path, 'widgets')) {
            $pos = strpos($path, 'widgets');
            $path = substr($path, $pos);
        }

        return $path;
    }

    /**
     * Collect mails
     * @param \Enlight_Event_EventArgs $args
     */
    public function onSendMails(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Components_Mail $mail */
        $mail = $args->get('mail');
        $context = $this->container->get('templatemail')->getStringCompiler()->getContext();

        /**
         * Remove some objects
         */
        unset($context['sConfig']);

        $this->mails[] = [
            'from' => $mail->getFrom(),
            'fromName' => $mail->getFromName(),
            'to' => $mail->getTo(),
            'subject' => $mail->getSubject(),
            'bodyPlain' => $mail->getPlainBodyText(),
            'bodyHtml' => $mail->getPlainBody(),
            'context' => $context
        ];
    }
}
