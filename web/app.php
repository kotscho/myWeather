<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Weather.php';
require_once __DIR__ . '/../src/Services/URLManagement.php';
require_once __DIR__ . '/../src/Route.php';
require_once __DIR__ . '/../src/Controller/WeatherController.php';

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/views');
$twig = new Twig_Environment( $loader, array(
    'cache' => __DIR__ . '/../cache',
));
$url = new URLManagement;
$twig->addGlobal('URL_ROOT', $url->getUrl()->urlNoScript);
$twig->addGlobal('SCRIPT_NAME', $url->getUrl()->scriptName);
Route::router( $_SERVER['REQUEST_URI'], $twig, $_REQUEST );