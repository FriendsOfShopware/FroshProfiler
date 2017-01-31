<?php

namespace ShyimProfiler\Components\BlockAnnotation;

/**
 * BlockAnnotator annotates smarty block with HTML comments, so you can tell which content belongs to which block.
 *
 * @author Daniel Nögel <d.noegel@shopware.com>
 */
class BlockAnnotator
{
    /**
     * @var BlockSplitter
     */
    private $blockSplitter;

    /**
     * BlockAnnotator constructor.
     *
     * @param BlockSplitter $blockSplitter
     */
    public function __construct(BlockSplitter $blockSplitter)
    {
        $this->blockSplitter = $blockSplitter;
    }

    /**
     * Do not append block info to blacklisted blocks (e.g. JS, CSS).
     *
     * @var array
     */
    protected $blacklist = array(
        'frontend_index_header_title',
        'frontend_index_body_classes',
    );

    /**
     * @param $source
     * @param $template
     * @param $pluginConfig
     * @return string
     */
    public function annotate($source, $template, $pluginConfig)
    {
        foreach ($this->blockSplitter->split($source) as $block) {
            if (in_array($block['name'], $this->blacklist)) {
                continue;
            }

            $file = '';

            if ($pluginConfig['frontendblocksTemplate']) {
                $file = ', File: ' . $template->template_resource;
            }

            $info = $block['name'];
            $start = "<!-- BLOCK BEGIN {$info}{$file} -->";
            $end = "<!-- BLOCK END {$info} -->";

            $source = str_replace($block['content'], $block['beginBlock'] . $start . $block['contentOnly'] . $end . $block['endBlock'], $source);
        }

        return $source;
    }
}
