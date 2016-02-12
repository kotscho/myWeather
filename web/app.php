<?php
require __DIR__  . '/../src/SplClassLoader.php';
require_once __DIR__ . '/../vendor/autoload.php';

$oClassLoader = new \SplClassLoader('MyWeather', __DIR__ . '/../src');
$oClassLoader->register();

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../src/MyWeather/Resources/views');
$twig = new Twig_Environment( $loader, array(
    'cache' => __DIR__ . '/../cache',
));

$url = new MyWeather\Component\URLManagement;
$twig->addGlobal('URL_ROOT', $url->getUrl()->urlNoScript);
$twig->addGlobal('SCRIPT_NAME', $url->getUrl()->scriptName);
MyWeather\Component\Route::router( $_SERVER['REQUEST_URI'], $twig, $_REQUEST );