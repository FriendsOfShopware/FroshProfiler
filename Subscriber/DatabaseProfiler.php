<?php

namespace FroshProfiler\Subscriber;

use Doctrine\DBAL\Logging\DebugStack;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Shopware\Components\DependencyInjection\Container;
use Zend_Db_Profiler;

/**
 * Class DatabaseProfiler
 */
class DatabaseProfiler implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
           'Enlight_Bootstrap_AfterInitResource_models' => 'onInitDoctrineModels',
           'Enlight_Bootstrap_AfterInitResource_db' => 'onInitZendConnection',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onInitZendConnection(\Enlight_Event_EventArgs $args)
    {
        /** @var Container $subject */
        $subject = $args->getSubject();

        if (!defined('TESTS_RUNNING') && PHP_SAPI === 'cli') {
            return;
        }

        $subject->get('db')->setProfiler(new Zend_Db_Profiler(true));
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onInitDoctrineModels(Enlight_Event_EventArgs $args)
    {
        /** @var Container $subject */
        $subject = $args->getSubject();

        if (!defined('TESTS_RUNNING') && PHP_SAPI === 'cli') {
            return;
        }

        define('STARTTIME', microtime(true));

        $logger = new DebugStack();
        $logger->enabled = true;
        $subject->get('models')->getConfiguration()->setSQLLogger($logger);
    }
}
