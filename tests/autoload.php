<?php
date_default_timezone_set('UCT');
require_once @realpath(__DIR__ . '/../vendor/autoload.php');
$loader = new Composer\Autoload\ClassLoader();
$loader->add('CK', realpath(__DIR__ . '/src'));
$loader->register();
