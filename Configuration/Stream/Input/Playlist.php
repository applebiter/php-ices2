<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Input;
/** 
 * Playlist encapsulates the parameters used by the PLaylist input module for 
 * IceS.
 * 
 * The playlist module is used to get audio from some pre-encoded Ogg Vorbis 
 * files. IceS currently checks to see if the same file gets played in 
 * succession and skips it, this means that having a playlist repeat with only 
 * one Ogg file listed won't work. The method of file selection is determined by 
 * the playlist type. The current types are basic and script. 
 * 
 * API 
 * 
 *     (array|string) export($as_xml = false) 
 *         -- Returns the object properties in an associative array by default 
 *         -- If given a boolean true, will return the object properties in the 
 *            form of a string representing an XML node. 
 *            
 *     (void) import(array $options = []) 
 *         -- Takes an associative array as input and initializes property 
 *            values
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

class Playlist extends Input
{  
    /**
     * __construct() 
     * 
     * @param array $options
     */
    public function __construct(array $options = []) 
    {
        $this->_module = 'playlist';
        
        $this->_initialize($options);
    } 
    
    /**
     * _initialize 
     * 
     * Provide initial values for the module if none are supplied
     * 
     * @access protected
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        /* type
         * 
         * This is the file format of the playlist. Currently "basic", "m3u" and 
         * "vclt" are supported.
         */
        $this->_params['type'] = !empty($options['param']['type']) 
            ? $options['param']['type'] 
            : 'basic';
        
        /* file 
         * 
         * State a path to a file which will contain a list of Ogg Vorbis 
         * filenames to play. The format of this file depends on the setting of 
         * "type". For type "basic" the format is one filename per line with 
         * lines beginning with '#' being treated as comments. If a line has a 
         * single '-' then standard input is read, which provides a way of 
         * getting some external Ogg Vorbis stream into ices.
         */
        $this->_params['file'] = !empty($options['param']['file']) 
            ? $options['param']['file'] 
            : null;
        
        /* random 
         * 
         * When set to 1, the playlist will be randomised when the playlist is 
         * loaded. By default random is off
         */
        $this->_params['random'] = !empty($options['param']['random']) 
            ? $options['param']['random'] 
            : 0;
        
        /* once 
         * 
         * When set to 1, the playlist is gone through once and then ends, this 
         * will cause ices to exit. By default once is off.
         */
        $this->_params['once'] = !empty($options['param']['once']) 
            ? $options['param']['once'] 
            : 0;
            
        /* restart-after-reread
         *
         * If the playlist is re-read mid way through, which may occur if the 
         * playlist was updated then this will restart at the beginning of the 
         * playlist. By default it's off.
         */
        $this->_params['restart-after-reread'] = !empty($options['param']['restart-after-reread'])
            ? $options['param']['restart-after-reread']
            : 0;
            
        /* program
         *
         * State a path to a program which when run will write to it's standard 
         * output a path to an Ogg Vorbis file. The program can be anything from 
         * an executable to a shell script as long as it starts, writes the 
         * filename to it's standard output and then exits. 
         * 
         * This is only used if the $_params['type'] value is 'script'.
         */
        $this->_params['program'] = !empty($options['param']['program'])
            ? $options['param']['program']
            : null;
    }
    
    /**
     * export()
     *
     * Returns an multidimensional array containing class property values
     *
     * @return mixed[][]
     */
    public function export($as_xml = false)
    {
        if ($as_xml) { 
            
            $output = "<input>\n" .
                      "    <module>playlist</module>\n" .
                      "    <param name=\"type\">{$this->_params['type']}</param>\n"; 
            
            if ('script' === $this->_params['type']) { 
                
                $output .= "    <param name=\"program\">{$this->_params['program']}</param>\n"; 
            } 
            else { 
                
                $output .= "    <param name=\"file\">{$this->_params['file']}</param>\n" . 
                           "    <param name=\"random\">{$this->_params['random']}</param>\n" .
                           "    <param name=\"once\">{$this->_params['once']}</param>\n" . 
                           "    <param name=\"restart-after-reread\">{$this->_params['restart-after-reread']}</param>\n";
            }
            
            $output .= "</input>\n";
        } 
        else { 
            
            $output = ['module' => 'playlist'];
            
            if ('script' === $this->_params['type']) {
                
                $output['param']['type'] = 'script';
                $output['param']['program'] = $this->_params['program'];
            }
            else {
                
                $output['param']['type']   = $this->_params['type'];
                $output['param']['file']   = $this->_params['file'];
                $output['param']['random'] = $this->_params['random'];
                $output['param']['once']   = $this->_params['once'];
                $output['param']['restart-after-reread'] = $this->_params['restart-after-reread'];
            } 
        }
        
        return $output;
    } 
    
    /**
     * import() 
     * 
     * Populate property values from input array
     * 
     * @param array $options
     */
    public function import(array $options = []) 
    {
        $this->_initialize($options);
    }
}