<?php

namespace MyWeather\Component;
/**
 * Standalone URL Management and manipulation class
 * 
 * @author Konstantinos Doskas
 * 
 */
class URLManagement{
    
    public $url = null;
    
    public $host = null;
    
    public $protocol = null;
    
    public $port = null;
    
    public $requestUri = null;
    
    public $scriptName = null;
    /**
     * @var String url part before actual script
     */
    public $urlNoScript = null;
    
    public function __construct(){
        
    }
    /**
     * Extract information from URL
     * @param type $forwarded
     * @return \URLManagement
     */
    public function getUrl( $forwarded = false ){
    
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $this->protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $this->port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($forwarded && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
        $this->host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->url = $this->protocol . '://' . $this->host . $_SERVER['REQUEST_URI'];
        $this->extractCurrentScriptname();
        
        return $this;
    }
    /**
     * @param type $elements additonalurl parts
     * @return String Host url with optional additional elements
     */ 
    public function buildHostUrl( $elements = '' ){
        return $this->protocol . '://' . $this->host . (empty($elements) ? '' : '/'.$elements);  
    }
    /**
     * Extract current scriptname like f.e app.php
     * @return \URLManagement
     */
    private function extractCurrentScriptname(){
        $reg = '/^(http|https):\/\/(www\.)?(.*\/)*(?<script>[\w]*.php|html|htm).*$/';
        preg_match( $reg, $this->url, $match );
        $this->scriptName = $match['script'];
        $this->urlNoScript = $this->protocol . '://' . $match[3];
        //kint::dump($match);
        return $this;
    }
}