<?php
require __DIR__ . '/../../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('FroshProfiler\\', __DIR__ . '/../../');

$loader->register();
