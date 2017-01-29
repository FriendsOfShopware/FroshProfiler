<?php

function smarty_modifier_convertMemory($size)
{
    $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    return @round($size / pow(1024, $i = floor(log($size, 1024))), 2) . ' ' . $unit[$i];
}
