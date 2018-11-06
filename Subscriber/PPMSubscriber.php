<?php

namespace FroshProfiler\Subscriber;

use Doctrine\DBAL\Logging\DebugStack;
use Enlight\Event\SubscriberInterface;
use Zend_Db_Profiler;

class PPMSubscriber implements SubscriberInterface
{
    /**
     * @return array
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public static function getSubscribedEvents()
    {
        return [
            'PPM_Request_preHandle' => 'onPPMRequestPre',
            'PPM_Request_postHandle' => 'onPPMRequestPost',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function onPPMRequestPre(\Enlight_Event_EventArgs $args)
    {
        /** @var \AppKernel $app */
        $app = $args->get('app');
        $profile = $app->getContainer()->get('frosh_profiler.current.profile');
        $profile->setStartTime(microtime(true));
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @throws \Zend_Db_Profiler_Exception
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function onPPMRequestPost(\Enlight_Event_EventArgs $args)
    {
        /** @var \AppKernel $app */
        $app = $args->get('app');

        $profile = $app->getContainer()->get('frosh_profiler.current.profile');
        $profile->reset();

        // Reset Zend
        $app->getContainer()->get('db')->setProfiler(new Zend_Db_Profiler(true));

        // Reset ModelManager
        $logger = new DebugStack();
        $logger->enabled = true;
        $app->getContainer()->get('models')->getConfiguration()->setSQLLogger($logger);
    }
}