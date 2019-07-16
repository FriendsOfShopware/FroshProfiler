<?php

namespace FroshProfiler\Components\BlockAnnotation;

/**
 * BlockSplitter splits a given template string into smarty blocks.
 *
 * @author Daniel Nögel <d.noegel@shopware.com>
 */
class BlockSplitter
{
    const BLOCK_START = '(?P<open>{\s*block\s*name\s*=\s*["\']?(?P<name>.+?)["\']?\s*})';
    const BLOCK_END = '(?P<close>{/block})';

    public function split(string $template): array
    {
        $matches = [];
        preg_match_all('#' . self::BLOCK_START . '|' . self::BLOCK_END . '#', $template, $matches, PREG_OFFSET_CAPTURE);

        $openStack = [];
        $closedStack = [];

        // Iterate all matches and build the result array
        foreach ($matches[0] as $key => $match) {
            $value = $match[0];
            $offset = $match[1];
            $name = $matches['name'][$key][0];
            $isStart = $matches['name'][$key][1] > -1;

            if ($isStart) {
                // iterate all currently open blocks and increase the child counter
                foreach ($openStack as &$parentElement) {
                    ++$parentElement['children'];
                }
                // push the open block to the stack
                $openStack[] = ['name' => $name, 'start' => $offset, 'startContent' => $offset + strlen($value), 'children' => 0];
            } else {
                // remove the closed block from `open` stack, collect some info and push it to `close` stack
                $element = array_pop($openStack);
                $element['endContent'] = $offset;
                $element['end'] = $offset + strlen($value);
                $element['content'] = BlockSplitter::sliceString($template, $element['start'], $element['end']);
                $element['contentOnly'] = BlockSplitter::sliceString($template, $element['startContent'], $element['endContent']);
                $element['beginBlock'] = BlockSplitter::sliceString($template, $element['start'], $element['startContent']);
                $element['endBlock'] = BlockSplitter::sliceString($template, $element['endContent'], $element['end']);

                $closedStack[] = $element;
            }
        }

        // sort by children - the replaccement later will go from the deepest child towards the main parent
        usort($closedStack, [$this, 'sortByChildren']);

        return $closedStack;
    }

    private static function sliceString(string $string, int $start, int $end): string
    {
        return substr($string, $start, $end - $start);
    }

    private static function sortByChildren(array $a, array $b): int
    {
        $a = $a['children'];
        $b = $b['children'];

        if ($a == $b) {
            return 0;
        }

        return $a < $b ? 1 : -1;
    }
}
