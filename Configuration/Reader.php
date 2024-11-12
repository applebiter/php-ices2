<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration;
/** 
 * Reader reads the Ices2 configuration output from XML file that is parsed by 
 * the ices2 binary itself. 
 * 
 * API 
 * 
 *     (Configuration|boolean) Reader::read($source, $format = 'xml') 
 *         -- Takes a string representing either the path to an XML document, or 
 *            else the sring contents of an XML document 
 *         -- Takes an optional string indicating the source format. Default is 
 *            'xml' but 'json' is also available 
 *         -- Returns a Configuration object on success, boolean false otherwise
 *         
 *     (boolean) hasErrors() 
 *         -- Indicates whether any errors have been generated 
 *         
 *     (array) getErrors() 
 *         -- Returns the errors array, which contains error messages, if any
 *         
 *            
 * PHP version 7 
 * 
 * @category Command-line, Ices2 
 * @package applebiter/collusion 
 * @author Richard Lucas <webmaster@applebiter.com> 
 * @link https://bitbucket.org/applebiter/collusion
 * @license MIT License 
 * 
 * The MIT License (MIT)
 *
 * Copyright (c) 2018
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */ 
use Exception;
use Ices2\Configuration;
use SimpleXMLElement;

class Reader
{  
    /**
     * _errors 
     * 
     * @static 
     * @var array
     */
    protected static $_errors = []; 
    
    /**
     * _has_errors 
     * 
     * @static 
     * @var boolean
     */
    protected static $_has_errors = false;

    /**
     * __construct() 
     */
    private function __construct() {} 
    
    /**
     * __clone() 
     */
    private final function __clone() {} 
    
    /**
     * _xmlObjectToArray() 
     * 
     * Takes an XML document in the form of a top-level SimpleXMLElement object, 
     * returns a multi-dimensional, associative array representing the structure 
     * of the XML document.
     * 
     * @param SimpleXMLElement $xml
     * @return string|number
     */
    static protected function _xmlObjectToArray(SimpleXMLElement $xml) 
    {
        $output = []; 
        
        $output['background'] = (integer) $xml->background;
        $output['logpath']    = (string) $xml->logpath; 
        $output['logsize']    = (integer) $xml->logsize;
        $output['logfile']    = (string) $xml->logfile; 
        $output['loglevel']   = (integer) $xml->loglevel; 
        $output['consolelog'] = (integer) $xml->consolelog; 
        $output['pidfile']    = (string) $xml->pidfile; 
        
        $output['stream']['metadata']['name'] = (string) $xml->stream->metadata->name; 
        
        if (!empty($xml->stream->metadata->description)) {
            
            $output['stream']['metadata']['description'] = (string) $xml->stream->metadata->description;
        }
        
        if (!empty($xml->stream->metadata->genre)) {
            
            $output['stream']['metadata']['genre'] = (string) $xml->stream->metadata->genre;
        }   
        
        if (!empty($xml->stream->metadata->url)) {
            
            $output['stream']['metadata']['url'] = (string) $xml->stream->metadata->url;
        } 
        
        $output['stream']['input']['module'] = (string) $xml->stream->input->module; 
        
        if ($xml->stream->input->param) {
            
            foreach ($xml->stream->input->param as $param) {
                
                $output['stream']['input']['param'][(string) $param['name']] = (string) $param;
            }
        }  
        
        $idx = 0;
        
        foreach ($xml->stream->instance as $instance) {
            
            $output['stream']['instance'][$idx]['hostname'] = (string) $instance->hostname; 
            $output['stream']['instance'][$idx]['port'] = (integer) $instance->port;
            $output['stream']['instance'][$idx]['password'] = (string) $instance->password;
            $output['stream']['instance'][$idx]['mount'] = (string) $instance->mount;
            $output['stream']['instance'][$idx]['reconnectdelay'] = (integer) $instance->reconnectdelay;
            $output['stream']['instance'][$idx]['reconnectattempts'] = (integer) $instance->reconnectattempts;
            $output['stream']['instance'][$idx]['retry-initial'] = (integer) $instance->{'retry-initial'}; 
            
            if ($instance->yp) {
                
                $output['stream']['instance'][$idx]['yp'] = 1;
            }
            
            if ($instance->maxqueuelength) {
                
                $output['stream']['instance'][$idx]['maxqueuelength'] = (integer) $instance->maxqueuelength;
            }
            
            if ($instance->resample) {
                
                $output['stream']['instance'][$idx]['resample']['in-rate'] = (integer) $instance->resample->{'in-rate'}; 
                $output['stream']['instance'][$idx]['resample']['out-rate'] = (integer) $instance->resample->{'out-rate'};
            } 
            
            if ($instance->downmix) {
                
                $output['stream']['instance'][$idx]['downmix'] = 1;
            }
            
            if ($instance->savefile) {
                
                $output['stream']['instance'][$idx]['savefile'] = $instance->savefile;
            } 
            
            if ($instance->encode) {
                
                if (!empty($instance->encode->{'nominal-bitrate'}) && $instance->encode->managed) { 
                    
                    $output['stream']['instance'][$idx]['managed'] = 1; 
                    $output['stream']['instance'][$idx]['nominal-bitrate'] = $instance->encode->{'nominal-bitrate'}; 
                    $output['stream']['instance'][$idx]['maximum-bitrate'] = $instance->encode->{'maximum-bitrate'};
                    $output['stream']['instance'][$idx]['minimum-bitrate'] = $instance->encode->{'minimum-bitrate'};
                } 
                else {
                    
                    $output['stream']['instance'][$idx]['quality'] = $instance->encode->quality;
                } 
                
                if (!empty($instance->encode->samplerate)) {
                    
                    $output['stream']['instance'][$idx]['samplerate'] = $instance->encode->samplerate;
                }
                
                if (!empty($instance->encode->channels)) {
                    
                    $output['stream']['instance'][$idx]['channels'] = $instance->encode->channels;
                }
                
                if (!empty($instance->encode->{'flush-samples'})) {
                    
                    $output['stream']['instance'][$idx]['flush-samples'] = $instance->encode->{'flush-samples'};
                }
            }
            
            $idx++;
        }
        
        return $output;
    }
    
    /**
     * read() 
     * 
     * Reads the contents of the file and returns a Configuration object. 
     * 
     * @static
     * @param string $source 
     * @param string $format
     * @throws Exception
     * @return Configuration|boolean
     */
    static public function read($source, $format = 'xml')
    {
        try {
            
            if (is_file($source)) {
                
                $contents = file_get_contents($source);
            } 
            else {
                
                $contents = $source;
            }
            
            switch ($format) {
                
                case 'json': 
                    
                    $contents = json_decode($contents); 
                    
                    break;
                    
                case 'xml': 
                default: 
                    
                    $xml = new SimpleXMLElement($contents);
                    
                    $contents = self::_xmlObjectToArray($xml);
                    
                    break;
            } 
            
            return $contents;
        } 
        catch (\Exception $e) {
            
            self::$_has_errors = true; 
            self::$_errors[] = $e->getMessage(); 
            
            return false;
        }
    } 
    
    /**
     * hasErrors() 
     * 
     * Indicates whether an error has occurred
     * 
     * @return boolean
     */
    static public function hasErrors() 
    {
        return self::$_has_errors;
    } 
    
    /**
     * getErors() 
     * 
     * Returns generated error messages, if any. Also flushes all error messages 
     * and resets the $_has_errors flag to "false".
     * 
     * @return array
     */
    static public function getErrors() 
    {
        self::$_has_errors = false; 
        $output = self::$_errors; 
        self::$_errors = []; 
        
        return $output;
    }
}