<?php

use \Weather;

class WeatherController {
        
    private $template = null;
    
    private $request = null;


    public function __construct( Twig_Environment $twig, $request ){
        $this->template = $twig->loadTemplate('index.html.twig');
        if( !empty( $request ) ){
            $this->request = $request;
        }
    }


    public function indexAction(){
        $weather = new Weather('Krefeld', 'xml');
        echo $this->template->render( 
                             array('city' => $weather->getCityName(), 
                                   'temperature' => $weather->getTemperatureCurrent(),
                                   'image' => $weather->descriptionToIcon(),
                                  )
                              );
    }
         
    public function searchAction(){
            
        $weather = new Weather( $this->request['cityname'], 'xml');
        echo $this->template->render( 
                             array('city' => $weather->getCityName(), 
                                   'temperature' => $weather->getTemperatureCurrent(),
                                   'image' => $weather->descriptionToIcon(),
                                  )
                              );
    }   
}