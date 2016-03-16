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
                                "weather/instant" => "WeatherController.instantAction",
                                "weather([\/][\w\+]+){0,1}" => "WeatherController.indexAction",
                                "" => "WeatherController.indexAction"//like app.php - on startup
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
                if ( !class_exists( $class ) ){
                    throw new Exception(sprintf("Class with full qualified classname [%s] does not exist"), $class);
                }
                return call_user_func_array(
                    array( new $class( $twig, $request ), (empty($method) ? self::DEFAULT_ACTION : $method) ),
                      array_slice($args, 1));
            }
        }
        throw new \Exception(sprintf("Controller for URL [%s] does not exist"), $url);
    }
    
    /**
     * 
     * @param \MyWeather\Component\ControllerInterface $controllerObj
     * @param string $action
     * @param string $param
     * @return boolean true on success false on failure
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