<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Input;
/** 
 * RoarAudio encapsulates the parameters used by the RoarAudio input module for 
 * IceS.
 * 
 * The RoarAudio module is used to get audio from a RoarAudio Sound Server. This 
 * module supports getting both already encoded and raw audio from the sound 
 * server. It also allows meta data to be read from the sound server or an file 
 * the same way the Open Sound module does. 
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

class RoarAudio extends Input
{  
    /**
     * __construct() 
     * 
     * @param array $options
     */
    public function __construct(array $options = []) 
    {
        $this->_module = 'roaraudio';
        
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
         * lower. This can also be a symbolic name as supported by libroar. 
         * Examples include "cd" and "dat".
         */
        $this->_params['rate'] = !empty($options['param']['rate']) 
            ? $options['param']['rate'] 
            : 44100;
        
        /* channels 
         * 
         * The number of channels to record. This is typically 2 for stereo or 1 
         * for mono. This can also be a symbolic name as supported by libroar. 
         * Examples include "stereo" and "mono".
         */
        $this->_params['channels'] = !empty($options['param']['channels']) 
            ? $options['param']['channels'] 
            : 'stereo';
            
        /* codec
         *
         * The codec to read the audio data in from the sound server. Currently 
         * the values "default", "pcm_s", "pcm_s_le", "pcm_s_be", "ogg_vorbis" 
         * and "ogg_general" are supported. "default" is an alias for "pcm_s" 
         * which itself is an alias to "pcm_s_le" or "pcm_s_be" depending on the 
         * native byte order of the system. "ogg_general" should not be used.
         */
        $this->_params['codec'] = !empty($options['param']['codec'])
            ? $options['param']['codec']
            : 'default';
            
        /* aiprofile
         *
         * Use the given audio info profile from libroar. By default the profile 
         * "default" is used. If mixed with rate, channels or codec options the 
         * last one set wins.
         */
        $this->_params['aiprofile'] = !empty($options['param']['aiprofile'])
            ? $options['param']['aiprofile']
            : null;
            
        /* dir
         *
         * This is the stream direction. Currently "monitor" and "record" are 
         * supported. If set to "monitor" ices2 will stream a copy of the audio 
         * played back by sound server to icecast. If set to "record" it will 
         * read from soundcard input (e.g. mic). Defaults to "monitor".
         */
        $this->_params['dir'] = !empty($options['param']['dir'])
            ? $options['param']['dir']
            : 'monitor';
        
        /* device 
         * 
         * This sets the location of the sound server. The location can be in 
         * any form libroar understands. Common values are server addresses in 
         * form "/path/to/sock" for UNIX sockets, "hostname" for IPv4 and IPv6 
         * hosts and "node::" for DECnet nodes. A full description of this can 
         * be found in the RoarAudio documentation. Defaults to the list of 
         * default locations (auto detection) libroar has.
         */
        $this->_params['device'] = !empty($options['param']['device']) 
            ? $options['param']['device'] 
            : null;
        
        /* metadata 
         * 
         * This is the meta data source. Currently supported values are "none", 
         * "file" and "stream". The values "none" and "file" are the same as for 
         * the Open Sound module the values "0" and "1". The value "stream" 
         * requests meta data from the sound server. Defaults to "stream".
         */
        $this->_params['metadata'] = !empty($options['param']['metadata']) 
            ? $options['param']['metadata'] 
            : 'stream';
        
        /* metadatafilename 
         * 
         * This setting is the same as "metadatafilename" for the Open Sound 
         * module. It is only active when "metadata" is set to "file".
         */
        $this->_params['metadatafilename'] = !empty($options['param']['metadatafilename']) 
            ? $options['param']['metadatafilename'] 
            : null;
            
        /* plugin
         *
         * This setting loads a plugin. The plugin name my be followed by 
         * parameters to be passed to the plugin. Between the plugin name and 
         * the parameters needs to be a space. Normal libroar plugin parameter 
         * parsing rules apply (keys and values separated by "=", 
         * key-value-pairs separated by ",").
         */
        $this->_params['plugin'] = !empty($options['param']['plugin'])
            ? $options['param']['plugin']
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
                      "    <module>roaraudio</module>\n" .
                      "    <param name=\"rate\">{$this->_params['rate']}</param>\n" .
                      "    <param name=\"channels\">{$this->_params['channels']}</param>\n" .
                      "    <param name=\"codec\">{$this->_params['codec']}</param>\n" .
                      "    <param name=\"metadata\">{$this->_params['metadata']}</param>\n" . 
                      "    <param name=\"dir\">{$this->_params['dir']}</param>\n";
            
            if (!empty($this->_params['aiprofile'])) {
                
                $output .= "    <param name=\"aiprofile\">{$this->_params['aiprofile']}</param>\n";
            }
            
            if (!empty($this->_params['device'])) {
                
                $output .= "    <param name=\"device\">{$this->_params['device']}</param>\n";
            }
            
            if (!empty($this->_params['metadata'])) {
                
                $output .= "    <param name=\"metadatafilename\">{$this->_params['metadatafilename']}</param>\n";
            }
            
            if (!empty($this->_params['plugin'])) {
                
                $output .= "    <param name=\"plugin\">{$this->_params['plugin']}</param>\n";
            }
            
            $output .= "</input>\n";
        }
        else {
            
            $output = [
                'module' => 'roaraudio',
                'param' => [
                    'rate'     => $this->_params['rate'],
                    'channels' => $this->_params['channels'],
                    'codec'    => $this->_params['codec'],
                    'metadata' => $this->_params['metadata'],
                    'dir'      => $this->_params['dir']
                ]
            ];
            
            if (!empty($this->_params['aiprofile'])) {
                
                $output['param']['aiprofile'] = $this->_params['aiprofile'];
            }
            
            if (!empty($this->_params['device'])) {
                
                $output['param']['device'] = $this->_params['device'];
            }
            
            if (!empty($this->_params['metadata'])) {
                
                $output['param']['metadatafilename'] = $this->_params['metadatafilename'];
            }
            
            if (!empty($this->_params['plugin'])) {
                
                $output['param']['plugin'] = $this->_params['plugin'];
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