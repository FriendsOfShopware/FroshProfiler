<?php

namespace FroshProfiler\Components\BlockAnnotation;

/**
 * BlockAnnotator annotates smarty block with HTML comments, so you can tell which content belongs to which block.
 *
 * @author Daniel Nögel <d.noegel@shopware.com>
 */
class BlockAnnotator
{
    /**
     * Do not append block info to blacklisted blocks (e.g. JS, CSS).
     *
     * @var array
     */
    protected $blacklist = [
        'frontend_index_header_title',
        'frontend_robots_txt',
        'frontend_robots_txt_user_agent',
        'frontend_robots_txt_disallows',
        'frontend_robots_txt_allows',
        'frontend_robots_txt_sitemap',
        'frontend_robots_txt_sitemap_mobile',
        'frontend_index_body_attributes',
        'frontend_index_header_javascript_inline',
        'frontend_listing_actions_class',
        'frontend_detail_index_configurator_set_configuration',
    ];
    /**
     * @var BlockSplitter
     */
    private $blockSplitter;

    public function __construct(BlockSplitter $blockSplitter)
    {
        $this->blockSplitter = $blockSplitter;
    }

    public function annotate(string $source, \Smarty_Internal_Template $template, array $pluginConfig): string
    {
        foreach ($this->blockSplitter->split($source) as $block) {
            if (in_array($block['name'], $this->blacklist) ||
                $this->contains($block['name'], '/attributes') ||
                $this->contains($block['name'], '_attributes') ||
                $this->contains($block['name'], 'classes') ||
                $this->endsWith($block['name'], '_data') ||
                $this->startsWith($block['name'], 'frontend_index_search_similar_results_') ||
                $this->contains($block['name'], 'dreisc_seo_') && $this->contains($block['name'], '_frontend_index_header')
            ) {
                continue;
            }

            $file = '';

            if ($pluginConfig['frontendblocksTemplate']) {
                $templateResources = explode('|', $template->template_resource);

                $currentFile = $template->_current_file;

                // smarty eval
                if ($this->startsWith($templateResources[0], 'string:')) {
                    $templateResources = [];
                }

                if (count($templateResources) > 1 && strpos($currentFile, $templateResources[0]) === false) {
                    $currentFile = $templateResources[0];
                }

                $file = ', File: ' . $currentFile;
            }

            $info = $block['name'];
            $start = "<!-- BLOCK BEGIN {$info}{$file} -->";
            $end = "<!-- BLOCK END {$info} -->";

            $source = str_replace($block['content'], $block['beginBlock'] . $start . $block['contentOnly'] . $end . $block['endBlock'], $source);
        }

        return $source;
    }

    function startsWith(string $haystack, string $needle): bool
    {
        $length = \strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith(string $haystack, string $needle): bool
    {
        $length = \strlen($needle);
        if ($length === 0) {
            return true;
        }

        return (\substr($haystack, -$length) === $needle);
    }

    private function contains(string $haystack, string $needle): bool
    {
        return \strpos($haystack, $needle) !== false;
    }
}
