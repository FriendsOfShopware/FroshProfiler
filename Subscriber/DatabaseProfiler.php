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
    public static function getSubscribedEvents(): array
    {
        return [
           'Enlight_Bootstrap_AfterInitResource_models' => 'onInitDoctrineModels',
           'Enlight_Bootstrap_AfterInitResource_db' => 'onInitZendConnection',
        ];
    }

    public function onInitZendConnection(\Enlight_Event_EventArgs $args): void
    {
        /** @var Container $subject */
        $subject = $args->get('subject');

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
        $subject = $args->get('subject');

        if (!defined('TESTS_RUNNING') && PHP_SAPI === 'cli') {
            return;
        }

        $subject->get('frosh_profiler.current.profile')->setStartTime(microtime(true));

        $logger = new DebugStack();
        $logger->enabled = true;
        $subject->get('models')->getConfiguration()->setSQLLogger($logger);
    }
}
