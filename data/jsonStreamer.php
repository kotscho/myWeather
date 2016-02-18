<?php
/**
 * @author Konstantinos Doskas
 * 
 * Bulk insert OpenWeathermap json data to Mongo collection
 * Hint: Mongo joker search: db.allCities.find({"name": /somestring/})
 * 
 */
$mng = new Mongo();
$mngDb = $mng->selectDB('cities');
$allCities = $mngDb->selectCollection('allCities');

if( !$allCities instanceof MongoCollection ){
    throw new Exception('No MongoCollections available');
}
$jsonHandle = fopen('./city.list.json', 'r');
if( $jsonHandle == FALSE ){
    die('Invalid json file');
}

$records = 0;
while( ($line = fgets($jsonHandle)) == true ){
    $jsonObj = json_decode($line);
    $allCities->insert($jsonObj);
    //print 'Current line: ' . $jsonObj->name .',' .$jsonObj->country. PHP_EOL;
    $records++;
}
echo PHP_EOL . 'Inserted ' .$records. ' records';