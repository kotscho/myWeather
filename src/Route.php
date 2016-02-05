<?php
/**
 * 
 * Super mini routing class, implementing the MVC paradigm to small projects
 * @author Konstantinos Doskas
 */

class Route{
    
    const DEFAULT_ACTION = 'indexAction';  
    /**
     * @todo relocate mapping table to seperate config file - json - preferably
     * @var array $mappingTable
     */
    private static $mappingTable = array(
                                "weather/search"  => "WeatherController.searchAction",
                                "weather"       => "WeatherController.indexAction",
                                 
                            );
    
    private function __construct() {
        
    } 
        
    public static function router( $url, Twig_Environment $twig, $request = null ) {
        $match = array();
        preg_match('/^.+\.php\/?(.*)$/', $url, $match);
        //kint::dump($match[1]);
        //kint::dump($request);
        foreach( self::$mappingTable as $reg => $controller ) {
            if( preg_match( "~^$reg$~", $match[1], $args ) ) {
                //kint::dump($args);
                list( $class, $method ) = explode( ".", $controller );
                $class = ucfirst( $class );
                return call_user_func_array(
                    array( new $class( $twig, $request ), (empty($method) ? self::DEFAULT_ACTION : $method) ),
                      array_slice($args, 1));
            }
        }
        trigger_error('Controller for URL ' .$url . ' does not exist');
        header("HTTP/1.0 404 Not Found");
    }
}