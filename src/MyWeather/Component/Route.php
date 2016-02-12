<?php
namespace MyWeather\Component;
use MyWeather\Component\URLManagement;
use MyWeather\Controller\ControllerInterface;
use \Twig_Environment;
use \Kint;

/**
 * Super mini routing class, implementing the MVC paradigm to small projects
 * @author Konstantinos Doskas
 */

class Route {
    
    const DEFAULT_ACTION = 'indexAction';  
    /**
     * @todo relocate mapping table to seperate config file - json - preferably
     * @var array $mappingTable
     */
    private static $mappingTable = array(
                                "weather/search"  => "WeatherController.searchAction",
                                "weather([\/][\w\+]+){0,1}" => "WeatherController.indexAction",
                            );
    
    private function __construct() {
        
    } 
        
    public static function router( $url, \Twig_Environment $twig, $request = '' ) {
        $match = array();
        preg_match('/^.+\.php\/?(.*)$/', $url, $match);
        foreach ( self::$mappingTable as $reg => $controller ) {
            if ( preg_match( "~^$reg$~", $match[1], $args ) ) {
                //kint::dump($args);
                list( $class, $method ) = explode( ".", $controller );
                $class = '\\MyWeather\\Controller\\'.ucfirst( $class );
                
                return call_user_func_array(
                    array( new $class( $twig, $request ), (empty($method) ? self::DEFAULT_ACTION : $method) ),
                      array_slice($args, 1));
            }
        }
        trigger_error('Controller for URL ' .$url . ' does not exist');
        header("HTTP/1.0 404 Not Found");
    }
    
    /**
     * 
     * @param \MyWeather\Component\ControllerInterface $controllerObj
     * @param string $action
     * @param string $param
     * @return boolean true on success faalse on failure
     * 
     */
    public static function redirecToRoute( ControllerInterface $controllerObj, $action, $param = '' ){
        if( !empty( $param ) ){
            $param = '/' . str_replace(' ', '+',$param);
        }
        foreach ( self::$mappingTable as $reg => $controller ){
            if ( $controller == ( $controllerObj->getControllerBasename() .'Controller.'.$action ) ){
                if ( preg_match("~^$reg~", strtolower( $controllerObj->getControllerBasename().$param ), $match) ){
                    $urlManager = new URLManagement();
                    $urlManager->getUrl();
                    header('location:' . $urlManager->urlNoScript . $urlManager->scriptName .'/'. $match[0]);
                    return true;
                }
            }     
        }
        trigger_error($controllerObj->getControllerBasename() .'Controller.'.$action .' not found');
        return false;
    }
}