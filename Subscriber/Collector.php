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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Widgets' => 'onPostDispatch',
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onDispatchLoopShutdown',
            'Enlight_Components_Mail_Send' => 'onSendMails',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $controllerLower = strtolower($controller->Request()->getControllerName());

        if (
            $controllerLower === 'profiler' ||
            $controllerLower === 'media' ||
            $controllerLower === 'csrftoken' ||
            strpos($controller->Request()->getRequestUri(), 'profiler') !== false ||
            $this->profile->getId() ||
            $controller->Request()->getCookie('disableProfile', false)
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

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args)
    {
        if ($this->profile->getId() === null || !$this->container->has('front')) {
            return;
        }

        /** @var Enlight_Controller_Response_ResponseHttp $response */
        $response = $args->get('response');

        if ($response instanceof Enlight_Controller_Response_ResponseHttp) {
            /** @var Response $symfonyResponse */
            $symfonyResponse = $this->container->get('kernel')->transformEnlightResponseToSymfonyResponse($response);
        } else {
            $symfonyResponse = new Response();
        }

        $profileData = $this->container->get('frosh_profiler.collector')->collectInformation($this->profileController);
        $profileData['response']['headers'] = $symfonyResponse->headers->all();
        $profileData['profileTime'] = round(microtime(true) - STARTTIME, 3);

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

            $content = $response->getBody();

            $content = str_replace('</body>', $profileTemplate . '</body>', $content);
            $response->setBody($content);
        }
    }

    /**
     * Collect mails.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onSendMails(Enlight_Event_EventArgs $args)
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
