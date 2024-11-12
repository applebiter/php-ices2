<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration;
/** 
 * Writer writes the Ices2 configuration output to an XML file that is parsed by 
 * the ices2 binary itself. 
 * 
 * API 
 * 
 *     (boolean) write(Configuration $config, $destination, $format = 'xml', $overwrite = true) 
 *         -- Takes a Configuration object, a string destination path 
 *         -- Optionally takes a string representing the file format, the   
 *            default is 'xml' but 'json' is available 
 *         -- Optionally takes a boolean indicating whether to overwrite an 
 *            existing file having the same name
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

class Writer
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
     * write() 
     * 
     * Writes the contents of the Configuration object to a file. The format is 
     * XML by default, but JSON is also available. Returns a boolean true on 
     * success, false otherwise
     * 
     * @static
     * @param Configuration $config
     * @param string $destination
     * @param string $format
     * @param boolean $overwrite
     * @throws Exception
     * @return boolean
     */
    static public function write(Configuration $config, $destination, $format = 'xml', $overwrite = true)
    {
        try {
            
            switch ($format) {
                
                case 'json': 
                    
                    $contents = json_encode($config->export()); 
                    
                    if (!is_file($destination) || (is_file($destination) && $overwrite)) {
                        
                        if (!file_put_contents($destination, $contents)) {
                            
                            throw new \Exception('Could not write configuration contents to the destination file.');
                        }
                    }
                    
                    break;
                    
                case 'xml': 
                default: 
                    
                    $contents = $config->export(true);
                    
                    if (!is_file($destination) || (is_file($destination) && $overwrite)) {
                        
                        if (!file_put_contents($destination, $contents)) {
                            
                            throw new \Exception('Could not write configuration contents to the destination file.');
                        }
                    }
                    
                    break;
            } 
            
            return true;
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
     * @static
     * @return boolean
     */
    static public function hasErrors() 
    {
        return self::$_has_errors;
    } 
    
    /**
     * getErrors() 
     * 
     * Returns generated error messages, if any. Also flushes all error messages 
     * and resets the $_has_errors flag to "false".
     * 
     * @static
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