<?php

namespace ShyimProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SmartySecurityCompilerPass
 * @package ShyimProfiler\Components\CompilerPass
 */
class SmartySecurityCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('shopware.template_security')) {
            $security = $container->getParameter('shopware.template_security');

            $security['php_modifiers'][] = 'var_dump';

            $container->setParameter('shopware.template_security', $security);
        }
    }
}