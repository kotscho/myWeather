<?php

namespace MyWeather\Controller;

use MyWeather\Service\Weather;
use MyWeather\Component\Route;
use \Twig_Environment;

class WeatherController extends AbstractController implements ControllerInterface {
        
    private $template = null;
    
    private $request = null;

    private $controllerBaseName = 'Weather';
    
    public function __construct( Twig_Environment $twig, $request = '' ){
        
        $this->template = $twig->loadTemplate('index.html.twig');
        if( !empty( $request ) ){
            $this->request = $request;
        }
    }
    
    public function getControllerBaseName() {
        return $this->controllerBaseName;
    }

    public function indexAction( $request = null ){
        if ( is_null( $request ) ){
            $weather = new Weather('New York', 'xml');
        } else {
            $weather = new Weather($request, 'xml');
        }
        echo $this->template->render( 
                             array('city' => $weather->getCityName(), 
                                   'temperature' => $weather->getTemperatureCurrent(),
                                   'image' => $weather->descriptionToIcon(),
                                  )
                              );
    }
         
    public function searchAction(){
        /**
         * Basically act as post proxy
         * maybe i should use \nategood\HTTPful here... 
         */    
        return Route::redirecToRoute( $this, 'indexAction', $this->request['cityname']) ;
    }   
}