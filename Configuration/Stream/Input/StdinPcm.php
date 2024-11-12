<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Input;
/** 
 * StdinPcm encapsulates the parameters used by the StdinPCM input module for 
 * IceS.
 * 
 * This module should always be available, and as you can see the parameters are 
 * almost the same except for the device. The PCM audio comes from the standard 
 * input so it can be generated from some external application feeding into a 
 * pipe. 
 * 
 * As it's raw PCM being fed in, it's impossible to determine the samplerate and 
 * channels so make sure the stated parameters match the incoming PCM or the 
 * audio will be encoded wrongly. 
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

class StdinPcm extends Input
{  
    /**
     * __construct() 
     * 
     * @param array $options
     */
    public function __construct(array $options = []) 
    {
        $this->_module = 'stdinpcm';
        
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
        /* rate
         * 
         * The value is in hertz, 44100 is the samplerate used on CD's, but some 
         * drivers may prefer 48000 (DAT) or you may want to use something 
         * lower.
         */
        $this->_params['rate'] = !empty($options['param']['rate']) 
            ? $options['param']['rate'] 
            : 44100;
        
        /* channels 
         * 
         * The number of channels to record. This is typically 2 for stereo or 1 
         * for mono
         */
        $this->_params['channels'] = !empty($options['param']['channels']) 
            ? $options['param']['channels'] 
            : 2;
        
        /* metadata 
         * 
         * Check for metadata arriving, if any are present then the data is 
         * marked for an update. The metadata is in the form of tag=value, and 
         * while Ogg Vorbis can handle any supplied tags, most players will only 
         * do anything with artist and title.
         */
        $this->_params['metadata'] = !empty($options['param']['metadata']) 
            ? $options['param']['metadata'] 
            : 0;
        
        /* metadatafilename 
         * 
         * The name of the file to open and read the metadata tags from, with 
         * this parameter missing standard input is read. Using a file is often 
         * the better approach. When using the file access the procedure is 
         * usually to populate the file contents then send a SIGUSR1 to the IceS 
         * process. 
         * 
         * The format of the file itself is a simple one comment per line 
         * format, below is a trivial example of the file, other tags can be 
         * used but players tend to only look for artist and title for 
         * displaying. The data must be in UTF-8 (this is not checked by ices, 
         * however).
         */
        $this->_params['metadatafilename'] = !empty($options['param']['metadatafilename']) 
            ? $options['param']['metadatafilename'] 
            : null;
    }
    
    /**
     * export()
     *
     * Takes an optional boolean true indicating the results should be returned
     * as a string representing an XML node.
     *
     * @param boolean optional default false
     * @return string|array|mixed[][]
     */
    public function export($as_xml = false)
    {
        if ($as_xml) {
            
            $output = "<input>\n" .
                      "    <module>stdinpcm</module>\n" .
                      "    <param name=\"rate\">{$this->_params['rate']}</param>\n" .
                      "    <param name=\"channels\">{$this->_params['channels']}</param>\n" .
                      "    <param name=\"metadata\">{$this->_params['metadata']}</param>\n";
            
            if (!empty($this->_params['metadata'])) {
                
                $output .= "    <param name=\"metadatafilename\">{$this->_params['metadatafilename']}</param>\n";
            }
            
            $output .= "</input>\n";
        }
        else {
            
            $output = [
                'module' => 'stdinpcm',
                'param' => [
                    'rate'     => $this->_params['rate'],
                    'channels' => $this->_params['channels'],
                    'metadata' => $this->_params['metadata']
                ]
            ];
            
            if (!empty($this->_params['metadata'])) {
                
                $output['param']['metadatafilename'] = $this->_params['metadatafilename'];
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