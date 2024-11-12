<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Instance;
/** 
 * Encode encapsulates the <encode> subportion of the ices configuration 
 * file. 
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
 *     (boolean) isUsed() 
 *         -- Indicates whether any object values were initialized, and thus 
 *            whether to attempt to export any values
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
class Encode 
{  
    /**
     * _quality
     *
     * State a quality measure for the encoder. The range goes from -1 to 10
     * where -1 is the lowest bitrate selection (default 3), and decimals can
     * also be stated, so for example 1.5 is valid. The actual bitrate used will
     * depend on the tuning in the vorbis libs, the samplerate, channels and the
     * audio to encode. A quality of 0 at 44100hz and 2 channels is typically
     * around the 64kbps mark.
     *
     * @access protected
     * @var float
     */
    protected $_quality;
    
    /**
     * _nominal_bitrate
     *
     * State a bitrate that the encoder should try to keep to. This can be used
     * as an alternative to selecting quality.
     *
     * @access protected
     * @var float
     */
    protected $_nominal_bitrate;
    
    /**
     * _managed
     *
     * State 1 to enable full bitrate management in the encoder. This is used
     * with nominal-bitrate, maximum-bitrate and minimum-bitrate to produce a
     * stream with more strict bitrate requirements. Enabling this currently
     * leads to higher CPU usage.
     *
     * @access protected
     * @var integer
     */
    protected $_managed;
    
    /**
     * _maximum_bitrate
     *
     * State bitrate in bits per second to limit max bandwidth used on a stream.
     * Only applies if managed is enabled.
     *
     * @access protectted
     * @var float
     */
    protected $_maximum_bitrate;
    
    /**
     * _minimum_bitrate
     *
     * State bitrate in bits per second to limit minimum bandwidth used on a
     * stream. Only applies if managed is enabled, this option has very little
     * use so should not really be needed.
     *
     * @access protected
     * @var float
     */
    protected $_minimum_bitrate;
    
    /**
     * _samplerate
     *
     * State the samplerate used for the encoding, this should be either the
     * same as the input or the result of the resample. Getting the samplerate
     * wrong will cause the audio to be represented wrong and therefore sound
     * like it's running too fast or too slow.
     *
     * @access protected
     * @var integer
     */
    protected $_samplerate;
    
    /**
     * _channels
     *
     * State the number of channels to use in the encoding. This will either be
     * the number of channels from the input or 1 if downmix is enabled.
     *
     * @access protected
     * @var integer
     */
    protected $_channels;
    
    /**
     * _flush_samples
     *
     * This is the trigger level at which Ogg pages are created for sending to
     * the server. Depending on the bitrate and compression achieved a single
     * Ogg page can contain many seconds of audio which may not be wanted as
     * that can trigger timeouts.
     *
     * Setting this to the same value as the encode samplerate will mean that a
     * page per second is sent, if a value that is half of the encoded
     * samplerate is specified then 2 Ogg pages per second are sent.
     *
     * @access protected
     * @var integer
     */
    protected $_flush_samples; 
    
    /**
     * _is_used 
     * 
     * Indicates whether this object ought export any values at all
     * 
     * @access protected 
     * @var boolean
     */
    protected $_is_used = false;
    
    /**
     * __construct() 
     * 
     * The class constructor
     * 
     * @param array $options
     */
    public function __construct(array $options = []) 
    {
        $this->_initialize($options);
    } 
    
    /**
     * _initialize() 
     * 
     * Populate property values
     * 
     * @access protected 
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        if (!empty($options)) {
            
            $this->_is_used = true;
        }
        
        $this->_quality = !empty($options['quality']) 
            ? $options['quality'] 
            : null; 
        
        $this->_nominal_bitrate = !empty($options['nominal-bitrate']) 
            ? $options['nominal-bitrate'] 
            : null; 
        
        $this->_managed = (!empty($options['managed'])) 
            ? 1 
            : 0; 
        
        $this->_maximum_bitrate = !empty($options['maximum-bitrate']) 
            ? $options['maximum-bitrate'] 
            : null; 
        
        $this->_minimum_bitrate = !empty($options['minimum-bitrate']) 
            ? $options['minimum-bitrate'] 
            : null; 
        
        $this->_samplerate = !empty($options['samplerate']) 
            ? $options['samplerate'] 
            : null; 
        
        $this->_channels = !empty($options['channels']) 
            ? $options['channels'] 
            : null; 
        
        $this->_flush_samples = !empty($options['flush-samples']) 
            ? $options['flush-samples'] 
            : null;
    } 
    
    /**
     * export() 
     * 
     * Takes an optional boolean true indicating that the results are to be 
     * returned in the form of a string representing an XML node. Returns 
     * property values in an array if any values were supplied to it during 
     * initialization or after importing values
     * 
     * @param boolean optional default false
     * @return string|array|mixed[][]
     */
    public function export($as_xml = false) 
    {
        if ($this->_is_used) { 
            
            if ($as_xml) {
                
                $output = "<encode>\n"; 
                          
                if (!empty($this->_nominal_bitrate) && !empty($this->_managed)) {
                    
                    $output .= "    <managed>1</managed>\n" . 
                               "    <nominal-bitrate>{$this->_nominal_bitrate}</nominal-bitrate>\n" .
                               "    <maximum-bitrate>{$this->_maximum_bitrate}</maximum-bitrate>\n" .
                               "    <minimum-bitrate>{$this->_minimum_bitrate}</minimum-bitrate>\n";
                } 
                else { 
                    
                    $output .= "    <quality>{$this->_quality}</quality>\n";
                } 
                
                if (!empty($this->_samplerate)) {
                    
                    $output .= "    <samplerate>{$this->_samplerate}</samplerate>\n";
                }
                
                if (!empty($this->_channels)) {
                    
                    $output .= "    <channels>{$this->_channels}</channels>\n";
                }
                
                if (!empty($this->_flush_samples)) {
                    
                    $output .= "    <flush-samples>{$this->_flush_samples}</flush-samples>\n";
                }
                
                $output .= "</encode>\n";
            } 
            else {
                
                $output = []; 
                
                if (!empty($this->_managed) && !empty($this->_nominal_bitrate)) { 
                    
                    $output['managed'] = 1;
                    $output['nominal-bitrate'] = $this->_nominal_bitrate; 
                    $output['maximum-bitrate'] = $this->_maximum_bitrate; 
                    $output['minimum-bitrate'] = $this->_minimum_bitrate;
                } 
                else { 
                    
                    $output['quality'] = $this->_quality;
                } 
                
                if (!empty($this->_samplerate)) {
                    
                    $output['samplerate'] = $this->_samplerate;
                }
                
                if (!empty($this->_channels)) {
                    
                    $output['channels'] = $this->_channels;
                }
                
                if (!empty($this->_flush_samples)) {
                    
                    $output['flush-samples'] = $this->_flush_samples;
                }
            }
            
            return $output;
        } 
        
        return null;
    } 
    
    /**
     * import() 
     * 
     * Import property values in an array form
     * 
     * @param array $options
     */
    public function import(array $options = [])
    {
        $this->_initialize($options);
    }
    
    /**
     * isUsed()
     *
     * Indicates whether any values were supplied by the caller
     *
     * @return boolean
     */
    public function isUsed()
    {
        return $this->_is_used;
    }
}