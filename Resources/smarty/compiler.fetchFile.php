<?php

function smarty_compiler_fetchFile($params, Smarty $smarty)
{
    $params['file'] = str_replace('"', '', $params['file']);
    foreach ($smarty->getTemplateDir() as $folder) {
        if (file_exists($folder . $params['file'])) {
            return file_get_contents($folder . $params['file']);
        }
    }

    return '';
}
