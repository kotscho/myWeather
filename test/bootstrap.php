<?php
require __DIR__  . '/../src/SplClassLoader.php';
require_once __DIR__ . '/../vendor/autoload.php';

$oClassLoader = new \SplClassLoader('MyWeather', __DIR__ . '/../src');
$oClassLoader->register();