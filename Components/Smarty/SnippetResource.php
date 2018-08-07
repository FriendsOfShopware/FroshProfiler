<?php

namespace FroshProfiler\Components\Smarty;

use Smarty;
use Smarty_Internal_Template;
use Smarty_Template_Source;

/**
 * Class SnippetResource
 *
 * @author Soner Sayakci <s.sayakci@gmail.com>
 */
class SnippetResource extends \Enlight_Components_Snippet_Resource
{
    /**
     * @var array
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    private $snippets = [];

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        if (!isset($source->smarty->registered_plugins[Smarty::PLUGIN_BLOCK]['snippet'])) {
            $source->smarty->registerPlugin(Smarty::PLUGIN_BLOCK, 'snippet', [__CLASS__, 'compileSnippetBlock']);
        }
        if (!isset($source->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['snippet'])) {
            $source->smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'snippet', [$this, 'compileSnippetModifier']);
        }
        $default_resource = $source->smarty->default_resource_type;
        $source->smarty->default_resource_type = 'file';
        parent::populate($source, $_template);
        $source->smarty->default_resource_type = $default_resource;
    }

    /**
     * @return array
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function getUsedSnippets()
    {
        return $this->snippets;
    }

    /**
     * @param $namespace
     * @param $name
     * @param $default
     * @param bool $force
     *
     * @return mixed
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    protected function getSnippet($namespace, $name, $default, $force = false)
    {
        $result = parent::getSnippet($namespace, $name, $default, $force);

        $this->snippets[$namespace . '|' . $name] = [
            'namespace' => $namespace,
            'name' => $name,
            'default' => $default,
            'force' => $force,
            'content' => $result,
        ];

        return $result;
    }
}
