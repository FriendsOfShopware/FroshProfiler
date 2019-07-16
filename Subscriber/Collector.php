<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Controller_Response_ResponseHttp;
use Enlight_Event_EventArgs;
use FroshProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Collector
 */
class Collector implements SubscriberInterface
{
    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var Enlight_Controller_Action
     */
    private $profileController;

    /**
     * @param ContainerInterface $container
     * @param Profile            $profile
     * @param array              $pluginConfig
     */
    public function __construct(ContainerInterface $container, Profile $profile, array $pluginConfig)
    {
        $this->container = $container;
        $this->profile = $profile;
        $this->pluginConfig = $pluginConfig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PostDispatch' => 'onPostDispatch',
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onDispatchLoopShutdown',
            'Enlight_Components_Mail_Send' => 'onSendMails',
        ];
    }

    public function onPostDispatch(Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $controllerLower = strtolower($controller->Request()->getControllerName());
        $actionLower = strtolower($controller->Request()->getActionName());
        $module = strtolower($controller->Request()->getModuleName());

        if (
            $controllerLower === 'profiler' ||
            $controllerLower === 'media' ||
            $controllerLower === 'csrftoken' ||
            $actionLower === 'getloginstatus' ||
            strpos($controller->Request()->getRequestUri(), 'profiler') !== false ||
            $this->profile->getId() ||
            $controller->Request()->getCookie('disableProfile', false) ||
            !$this->pluginConfig['profilingBackend'] && $module === 'backend'
        ) {
            return;
        }

        if ($controller->Request()->getHeader('X-Profiler')) {
            $profileId = $controller->Request()->getHeader('X-Profiler');
        } else {
            $profileId = uniqid();
        }

        $this->profile->setId($profileId);
        $this->profileController = $controller;
    }

    public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args): void
    {
        if ($this->profile->getId() === null || !$this->container->has('front')) {
            return;
        }

        /** @var Response $response */
        $response = $args->get('response');

        $profileData = $this->container->get('frosh_profiler.collector')->collectInformation($this->profileController);
        $profileData['response']['headers'] = $response->headers->all();
        $profileData['profileTime'] = round(microtime(true) - $this->container->get('frosh_profiler.current.profile')->getStartTime(), 3);

        $isIPWhitelisted = in_array($this->container->get('front')->Request()->getClientIp(), explode("\n", $this->pluginConfig['whitelistIP']));

        if (empty(trim($this->pluginConfig['whitelistIP'])) || $this->pluginConfig['whitelistIPProfile'] == 1 || $isIPWhitelisted) {
            $this->container->get('frosh_profiler.collector')->saveCollectInformation(
                $this->profile->getId(),
                $profileData,
                $this->profileController->Request()->getHeader('X-Profiler')
            );
        }

        if ($this->profileController->Request()->getModuleName() == 'frontend' && (empty(trim($this->pluginConfig['whitelistIP'])) || $isIPWhitelisted)) {
            $view = $this->container->get('template');
            $view->assign('sProfiler', $profileData);
            $view->assign('sProfilerCollectors', $this->container->get('frosh_profiler.collector')->getCollectors());
            $view->assign('sProfilerID', $this->profile->getId());
            $view->assign('sProfilerTime', $profileData['profileTime']);
            $view->assign('sUsedSnippets', $this->container->get('snippet_resource')->getUsedSnippets());

            $view->addTemplateDir($this->container->getParameter('frosh_profiler.plugin_dir') . '/Resources/views/');
            $profileTemplate = $view->fetch('@Toolbar/index.tpl');

            $content = $response->getContent();

            $content = str_replace('</body>', $profileTemplate . '</body>', $content);
            $response->setContent($content);
        }

        if ($this->profileController->Request()->getModuleName() === 'backend') {
            $response->headers->set('x-profiler-url', $this->profileController->get('router')->assemble([
                'module' => 'frontend',
                'controller' => 'profiler',
                'action' => 'detail',
                'id' => $this->profile->getId(),
                'fullPath' => true,
            ]));
        }
    }

    public function onSendMails(Enlight_Event_EventArgs $args): void
    {
        /** @var \Enlight_Components_Mail $mail */
        $mail = $args->get('mail');
        $context = $this->container->get('templatemail')->getStringCompiler()->getContext();

        $this->profile->addMail([
            'from' => $mail->getFrom(),
            'fromName' => $mail->getFromName(),
            'to' => $mail->getTo(),
            'subject' => $mail->getSubject(),
            'bodyPlain' => $mail->getPlainBodyText(),
            'bodyHtml' => $mail->getPlainBody(),
            'context' => $context,
        ]);
    }
}
