<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Input;
/** 
 * Factory reads the incoming module selection and returns the corresponding 
 * input module, after initializing it with incoming values. 
 * 
 * API 
 * 
 *     ({input plugin wrapper}) Factory::get(array $options = []) 
 *         -- Takes an associative array for input and returns the appropriate 
 *            input module
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
use Ices2\Configuration\Stream\Input;

class Factory
{  
    /**
     * __construct() 
     */
    private function __construct() {} 
    
    /**
     * __clone() 
     */
    private final function __clone() {} 
    
    /**
     * get()( 
     * 
     * Returns an input module to the caller
     * 
     * @param array $options
     * @return Input 
     */
    static public function get(array $options = []) 
    {
        $module = !empty($options['module']) 
            ? $options['module'] 
            : 'stdinpcm'; 
        
        switch ($module) {
            
            case 'alsa':                
                return new Alsa($options); 
                
            case 'opensound': 
                return new OpenSound($options); 
                
            case 'playlist': 
                return new Playlist($options); 
                
            case 'roaraudio': 
                return new RoarAudio($options);
                
            case 'sun':
                return new Sun($options);
                
            case 'stdinpcm':
            default:
                return new StdinPcm($options);
        }
    }
}