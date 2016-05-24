<?php
namespace ShopwarePlugins\Profiler\Components\Kernel;

use Shopware\Kernel as BaseKernel;
use Enlight_Controller_Response_ResponseHttp as EnlightResponse;

class Kernel extends BaseKernel
{
    public function transformEnlightResponseToSymfonyResponse(EnlightResponse $response) {
        $response = parent::transformEnlightResponseToSymfonyResponse($response);

        if ($this->getContainer()->has('front') && $this->getContainer()->has('profileId')) {
            $profileData = $this->getContainer()->get('profiler.collector')->collectInformation($this->getContainer()->get('profileController'));
            $profileData['template'] = array_merge($profileData['template'], $this->getContainer()->get('profileData.template'));

            Shopware()->Container()->get('profiler.collector')->saveCollectInformation(
                $this->getContainer()->get('profileId'),
                $profileData
            );

            $view = $this->getContainer()->get('template');

            $view->assign('sProfiler', $profileData);
            $view->assign('sProfilerCollectors', $this->container->get('profiler.collector')->getCollectors());
            $view->assign('sProfilerID', $this->getContainer()->get('profileId'));

            $view->addTemplateDir($this->container->getParameter('profiler.plugin_dir') . 'Views/');
            $profileTemplate = $view->fetch('@Profiler/index.tpl');

            $content = $response->getContent();

            $content = str_replace('</body>', $profileTemplate . '</body>', $content);
            $response->setContent($content);

            var_dump($this->container->get('events'));
            die();
        }

        return $response;
    }
}
