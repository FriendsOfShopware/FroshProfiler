<?php
return array_merge($this->loadConfig($this->AppPath() . 'Configs/Default.php'), [
    'front' => [
        'throwExceptions' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ],
    'errorHandler' => [
        'throwOnRecoverableError' => true,
    ],
    'session' => [
        'unitTestEnabled' => true,
        'name' => 'SHOPWARESID',
        'cookie_lifetime' => 0,
        'use_trans_sid' => false,
        'gc_probability' => 1,
        'gc_divisor' => 100,
        'save_handler' => 'db',
    ],
    'mail' => [
        'type' => 'file',
        'path' => $this->getCacheDir(),
    ],
    'phpSettings' => [
        'error_reporting' => E_ALL,
        'display_errors' => 1,
        'date.timezone' => 'Europe/Berlin',
        'max_execution_time' => 0,
    ],
    'csrfProtection' => [
        'frontend' => false,
        'backend' => false,
    ],
]);
