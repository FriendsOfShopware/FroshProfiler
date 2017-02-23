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

    public function __construct(BlockSplitter $blockSplitter)
    {
        $this->blockSplitter = $blockSplitter;
    }

    /**
     * Do not append block info to blacklisted blocks (e.g. JS, CSS).
     *
     * @var array
     */
    protected $blacklist = [
        'frontend_index_header_title',
        'frontend_index_body_classes',
        'frontend_robots_txt',
        'frontend_robots_txt_user_agent',
        'frontend_robots_txt_disallows',
        'frontend_robots_txt_allows',
        'frontend_robots_txt_sitemap',
        'frontend_robots_txt_sitemap_mobile'
    ];

    /**
     * @param string $template
     *
     * @return string
     */
    public function annotate($template)
    {
        foreach ($this->blockSplitter->split($template) as $block) {
            if (in_array($block['name'], $this->blacklist)) {
                continue;
            }

            $info = $block['name'];
            $start = "<!-- BLOCK BEGIN {$info} -->";
            $end = "<!-- BLOCK END {$info} -->";

            $template = str_replace($block['content'], $block['beginBlock'] . $start . $block['contentOnly'] . $end . $block['endBlock'], $template);
        }

        return $template;
    }
}
