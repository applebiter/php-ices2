<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Input;
/** 
 * Alsa encapsulates the parameters used by the ALSA input module for IceS.
 * 
 * The Advanced Linux Sound Architecture (ALSA) is a completely different sound 
 * system on linux but provides OSS compatability so the OSS driver should work 
 * with it as well. To use ALSA natively a separate module is used. The 
 * parameters to ALSA are mostly the same for OSS, as it performs the same task, 
 * ie captures audio from the DSP. 
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

class Alsa extends Input
{  
    /**
     * __construct() 
     * 
     * @param array $options
     */
    public function __construct(array $options = []) 
    {
        $this->_module = 'alsa';
        
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
        
        /* device 
         * 
         * This is the device name as used in ALSA. This can be a physical 
         * device as in the case of "hw:0,0" or a virtual device like one with 
         * dsnoop.
         */
        $this->_params['device'] = !empty($options['param']['device']) 
            ? $options['param']['device'] 
            : '/dev/dsp';
        
        /* periods
         *
         * This specifies how many interrupts will be generated (default: 2)
         */
        $this->_params['periods'] = !empty($options['param']['periods']) 
            ? $options['param']['periods'] 
            : 2;
        
        /* buffer-time
         *
         * The size of the buffer measured in mS (default 500)
         */
        $this->_params['buffer-time'] = !empty($options['param']['buffer-time']) 
            ? $options['param']['buffer-time'] 
            : 500;
        
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
                      "    <module>alsa</module>\n" . 
                      "    <param name=\"rate\">{$this->_params['rate']}</param>\n" . 
                      "    <param name=\"channels\">{$this->_params['channels']}</param>\n" .
                      "    <param name=\"device\">{$this->_params['device']}</param>\n" .
                      "    <param name=\"metadata\">{$this->_params['metadata']}</param>\n";
            
            if (!empty($this->_params['periods'])) {
                
                $output .= "    <param name=\"periods\">{$this->_params['periods']}</param>\n";
            }
            
            if (!empty($this->_params['buffer-time'])) {
                
                $output .= "    <param name=\"buffer-time\">{$this->_params['buffer-time']}</param>\n";
            }
            
            if (!empty($this->_params['metadata'])) {
                
                $output .= "    <param name=\"metadatafilename\">{$this->_params['metadatafilename']}</param>\n";
            } 
            
            $output .= "</input>\n";
        } 
        else {
            
            $output = [
                'module' => 'alsa',
                'param' => [
                    'rate'     => $this->_params['rate'],
                    'channels' => $this->_params['channels'],
                    'device'   => $this->_params['device'],
                    'metadata' => $this->_params['metadata']
                ]
            ];
            
            if (!empty($this->_params['periods'])) {
                
                $output['param']['periods'] = $this->_params['periods'];
            }
            
            if (!empty($this->_params['buffer-time'])) {
                
                $output['param']['buffer-time'] = $this->_params['buffer-time'];
            }
            
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