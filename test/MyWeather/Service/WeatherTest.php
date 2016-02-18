<?php
namespace MyWeather\Service;

class MyWeatherTest extends \PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var string $city test value 
     */
    private $city = 'Athens';
     /**
     *
     * @var string $mode test value 
     */
    private $mode = 'xml';
     /**
     *
     * @var string $search test value 
     */
    private $search = 'Athens';
   /**
    * 
    * Call protected/private method of a class.
    *
    * @param object &$object    Instantiated object that we will run method on.
    * @param string $methodName Method name to call
    * @param array  $parameters Array of parameters to pass into method.
    *
    * @return mixed Method return.
    */
    public function invokeNonPublicMethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
    
    /**
     * @covers \MyWeather\Service\Weather::readConfigFile
     * @uses \MyWeather\Service\Weather::__construct
     * @throws \Exception
     */
    public function testReadConfigFile(){
        $test = new Weather($this->city, $this->mode);
        $a = $this->invokeNonPublicMethod( $test, 'readConfigFile' );
        $this->assertInstanceOf('stdClass', $a);
        
        return $a;
    }
    
    /**
     * @covers \MyWeather\Service\Weather::initMongDB
     * @uses \MyWeather\Service\Weather::__construct
     */
    public function testInitMongDB(){
        $test = new Weather($this->city, $this->mode);
        $a = $this->invokeNonPublicMethod( $test, 'initMongDB' );
        $this->assertInstanceOf('\MongoCollection', $a);
        
        return $a;
    }
    
    /**
     * @covers \MyWeather\Service\Weather::getCitiesInstantly
     * @uses \MyWeather\Service\Weather::__construct
     */
    public function testGetCitiesInstantly(){
        $test = new Weather($this->city, $this->mode);
        $a = $this->invokeNonPublicMethod( $test, 'initMongDB' );
        $test->searchString = $this->search;
        $a = $test->getCitiesInstantly();
        
        $this->assertNotNull($a);

        return $a;
    }
}