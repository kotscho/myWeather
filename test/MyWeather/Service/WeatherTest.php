<?php
namespace MyWeather\Service;

class MyWeatherTest extends \PHPUnit_Framework_TestCase {
    
    private $city = 'Athens';
    
    private $mode = 'xml';
    
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
    public function invokeNonePublicMethod(&$object, $methodName, array $parameters = array()) {
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
        $a = $this->invokeNonePublicMethod( $test, 'readConfigFile' );
        $this->assertNotEmpty($a);
        
        return $a;
    }
}