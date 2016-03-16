<?php

namespace MyWeather\Service;
/**
 * 
 * OpenWheatherMap wheather forecast client 
 * api.openweathermap.org/data/2.5/forecast/city?id=524901&APPID=1111111111 
 * @author Konstantinos Doskas
 * 
 */


class Weather {
     
    const PROVIDER_URL = 'api.openweathermap.org/data/2.5/forecast/';
    
    const IMAGE_URL = 'https://openweathermap.org/img/w/';
    
    const IMAGE_FORMAT = 'png';
    
    const INSTANT_LIMIT = 10;
    
    private $weatherIcons = array( 'clearsky' => '01',
                                    'fewclouds' => '02',
                                    'scatteredclouds' => '03',
                                    'brokenclouds' => '04',
                                    'overcastclouds' => '04',
                                    'showerrain' => '09',
                                    'rain' => '10',
                                    'thunderstorm' => '11',
                                    'snow' => '13',
                                    'mist' => '50',
                            );
    
    public static $apiKey;
    
    public static $mongoDB;
    
    public static $mongoCollection;

    public $searchString = null;

    public $weatherResponse = null;
    
    
    /**
     * @param string $city
     * @param string $mode - requested response mode
     * @constructor
     */
    public function __construct( $city, $mode ) {
       $jsonObj = $this->readConfigFile();
       self::$apiKey = $jsonObj->apikey;
       self::$mongoDB = $jsonObj->mongoDB;
       self::$mongoCollection = $jsonObj->mongoCollection;
       $this->getWeatherByName( $city, self::$apiKey, $mode );
    }
    /**
     * @param string $city  - city to be requested
     * @param string $apiKey - OpenWheatherMap API key
     * @param string $mode - respond type
     * @return mixed $wheather, depending on requested mode
     */
    public function getWeatherByName( $city, $apiKey, $mode = 'xml' ){
        if(preg_match('/\s+/', $city) == (int)1){
            $cityPieces = explode(' ', $city);
            for( $x=0; $x <  count($cityPieces); $x++ ){
               $cityString .= $cityPieces[$x] . ( !end($cityPieces) ? '+' : ''  );
            }
            $city = $cityString;
        }
        $uri = 'http://' . self::PROVIDER_URL . 'find?q=' . $city .'&mode='. $mode . '&APPID=' . $apiKey;
        $response = \Httpful\Request::get($uri)
        ->{"expects". ucfirst( $mode ) }()
        ->sendIt();
        //Kint::dump($response);
        $this->weatherResponse = $response;
        
        return $this;
    }
    
    /**
     * @return \Httpful\Request
     */
    public function renderFullResponse(){
        //Kint::dump($this->weatherResponse->body->forecast->time->temperature);
        return $this->weatherResponse;
    }
    
    public function getCityName(){
        //print $this->weatherResponse;
        return $this->weatherResponse->body->location->name;
    }
    
    public function getTemperatureMin(){
        //Kint::dump($this->weatherResponse->body->time);
        return $this->weatherResponse->body->forecast->time->temperature['min'];
    }
    
    public function getTemperatureMax(){
        return $this->weatherResponse->body->forecast->time->temperature['max'];
    }
    
    public function getTemperatureCurrent(){
        return $this->weatherResponse->body->forecast->time->temperature['value'];
    }
    
    public function getTemperatureUnit(){
        return $this->weatherResponse->body->forecast->time->temperature['unit'];
    }

    public function getHumidity(){}
    
    /**
     * 
     * @return string imageurl
     */
    public function descriptionToIcon() {
        //if( $this->weatherResponse->body->forecast->time[] ){}
        $trimmedDescription = preg_replace('/\s+/', '', $this->weatherResponse
                                            ->body
                                            ->forecast
                                            ->time
                                            ->clouds['value']);
        return self::IMAGE_URL . $this->weatherIcons[$trimmedDescription] . 'd.' . self::IMAGE_FORMAT;
    }
    /**
     * read the json config file
     * @return Object of stdClass
     */
    private function readConfigFile(){
        try { 
            $jsonString =  file_get_contents( __DIR__ . '/../../../config/config.json');
            $config = json_decode($jsonString);
            
            return $config;
        }
        catch (Exception $e){
            $e->getMessage();
        }
    }
    /**
     * Search Mongo DB using trailing joker
     * @param type $string 
     * @return array
     */
    public function getCitiesInstantly( $string = '' ){

       if ( empty( $string ) ){ 
           return array();
       }
       $this->searchString = $string;
       $mngCollection = $this->initMongDB();
       $cursor = $mngCollection->find( array('name' => new \MongoRegex("/^".$this->searchString."/") ) );
       $resultSet = $cursor->limit(self::INSTANT_LIMIT);
       while( ($record = $resultSet->getNext()) != false ){
           $records[] = $record;
       }
       return $records;
    }
    
    /**
     * Initialize Mongo database for instant search
     * @return Object of MongoCollection
     */
    private function initMongDB(){
        $mng = new \Mongo();
        $mngDb = $mng->selectDB(self::$mongoDB);
        $mngCollection = $mngDb->selectCollection(self::$mongoCollection);
        
        return $mngCollection;
    }
}