<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream;  
/** 
 * Instance encapsulates and contains the <instance> subportion of the ices 
 * configuration file. There must be at least one instance per stream, but more 
 * than one instance per stream is supported. 
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
use Ices2\Configuration\Stream\Instance\Encode; 
use Ices2\Configuration\Stream\Instance\Resample; 

class Instance 
{  
    /**
     * _hostname 
     * 
     * State the hostname of the icecast to contact, this can be a name or IP 
     * address and can be IPv4 or IPv6 on systems that support IPv6. The default 
     * is localhost.
     * 
     * @access protected 
     * @var string
     */
    protected $_hostname; 
    
    /**
     * _port
     *
     * State the port to connect to, this will be the port icecast is listening 
     * on, typically 8000 but can be any.
     *
     * @access protected
     * @var string
     */
    protected $_port; 
    
    /**
     * _password
     *
     * For providing a stream, a username/password has to be provided, and must 
     * match what icecast expects.
     *
     * @access protected
     * @var string
     */
    protected $_password; 
    
    /**
     * _mount
     *
     * Mountpoints are used to identify a particular stream on a icecast server, 
     * they must begin with / and for the sake of certain listening clients 
     * should end with the .ogg extension.
     *
     * @access protected
     * @var string
     */
    protected $_mount; 
    
    /**
     * _yp
     *
     * By default streams will not be advertised on a YP server unless this is 
     * set, and only then if the icecast if configured to talk to YP servers.
     *
     * @access protected
     * @var integer
     */
    protected $_yp; 
    
    /**
     * _reconnectdelay
     *
     * If the connection to the server is lost ices2 will wait a given time 
     * before reconnecting. This setting controls how long ices2 will wait 
     * before reconnecting. The value is the time in seconds.
     *
     * @access protected
     * @var integer
     */
    protected $_reconnectdelay; 
    
    /**
     * _reconnectattempts
     *
     * This setting controls the number of reconnet attempts ices2 will do 
     * before considering the server to be down and stopping the instance.
     *
     * @access protected
     * @var integer
     */
    protected $_reconnectattempts; 
    
    /**
     * _retryinitial
     *
     * This setting controls if being unabled to connect to the server at 
     * startup is considered a fatal error. The default is to consider this a 
     * fatal error and quit making debugging more easy.
     *
     * @access protected
     * @var integer
     */
    protected $_retryinitial; 
    
    /** 
     * _maxqueuelength 
     * 
     * This describes how long the internal data queues may be. This basically 
     * lets you control how much data gets buffered before ices decides it can't 
     * send to the server fast enough, and either shuts down or flushes the 
     * queue (dropping the data) and continues. 
     * For advanced users only.
     * 
     * @access protected
     * @var integer
     */
    protected $_maxqueuelength;
    
    /**
     * _resample
     *
     * When encoding or re-encoding, there is a point where you take PCM audio 
     * and encode to Ogg Vorbis. In some situations a particular encoded stream 
     * may require a lower samplerate to achieve a lower bitrate. The resample 
     * will modify the audio data before it enters the encoder, but does not 
     * affect other instances. 
     * 
     * The most common values used are 48000, 44100, 22050 and 11025, and is 
     * really only used to resample to a lower samplerate, going to a higher 
     * rate serves no purpose within IceS.
     *
     * @access protected
     * @var Resample
     */
    protected $_resample; 
    
    /**
     * _downmix 
     * 
     * Some streams want to reduce the bitrate further, reducing the number of 
     * channels used to just 1. Converting stereo to mono is fairly common and 
     * when this is set to 1 the number of channels encoded is just 1. Like 
     * resample, this only affects the one instance it's enabled in.
     * 
     * @access protected
     * @var integer
     */
    protected $_downmix; 
    
    /**
     * _savefile 
     * 
     * Sometimes the stream transmitted wants to be saved to disk. This can be 
     * useful for live recordings.
     * 
     * @access protected
     * @var string
     */
    protected $_savefile; 
    
    /**
     * _encode  
     * 
     * Remove this section if you don't want your files reencoded when using 
     * playback or RoarAudio input module.
     * 
     * @access protected
     * @var Encode
     */
    protected $_encode; 
    
    /**
     * _metadata 
     * 
     * Metadata added here will override any metadata values entered in the 
     * main configuration for this instance.
     * 
     * @access protected
     * @var Metadata
     */
    protected $_metadata;
    
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
     * _inititalize()
     *
     * Populate property values
     *
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        $this->_hostname = !empty($options['hostname']) 
            ? $options['hostname'] 
            : 'localhost'; 
        
        $this->_port = !empty($options['port']) 
            ? $options['port'] 
            : 8000; 
        
        $this->_password = !empty($options['password']) 
            ? $options['password'] 
            : 'P@55word'; 
        
        $this->_mount = !empty($options['mount']) 
            ? $options['mount'] 
            : '/example.ogg'; 
        
        $this->_yp = !empty($options['yp']) 
            ? 1 
            : 0; 
        
        $this->_reconnectdelay = !empty($options['reconnectdelay']) 
            ? $options['reconnectdelay'] 
            : 3;
            
        $this->_reconnectattempts = !empty($options['reconnectattempts'])
            ? $options['reconnectattempts']
            : 3;
            
        $this->_retryinitial = !empty($options['retry-initial'])
            ? 1
            : 0; 
        
        $this->_maxqueuelength = !empty($options['maxqueuelength']) 
            ? $options['maxqueuelength'] 
            : null;
        
        $this->_resample = !empty($options['resample']) 
            ? new Resample($options['resample']) 
            : new Resample(); 
        
        $this->_downmix = !empty($options['downmix'])  
            ? 1 
            : 0; 
        
        $this->_savefile = !empty($options['savefile']) 
            ? $options['savefile'] 
            : null;
            
        $this->_encode = !empty($options['encode'])
            ? new Encode($options['encode'])
            : new Encode(); 
    }
    
    /**
     * export()
     *
     * Takes an optional boolean true indicating that the results should be 
     * returned in the form of a string representing an XML node. Returns an 
     * array containing class property values
     * 
     * @param boolean optional default false
     * @return string[]|mixed[][]
     */
    public function export($as_xml = false)
    {
        if ($as_xml) {
            
            $output = "<instance>\n" . 
                      "    <hostname>{$this->_hostname}</hostname>\n" . 
                      "    <port>{$this->_port}</port>\n" . 
                      "    <password>{$this->_password}</password>\n" . 
                      "    <mount>{$this->_mount}</mount>\n" . 
                      "    <reconnectdelay>{$this->_reconnectdelay}</reconnectdelay>\n" . 
                      "    <reconnectattempts>{$this->_reconnectattempts}</reconnectattempts>\n" . 
                      "    <retry-initial>{$this->_retryinitial}</retry-initial>\n"; 
            
            if ($this->_yp) {
                
                $output .= "    <yp>1</yp>\n";
            } 
            
            if (null !== $this->_maxqueuelength) { 
                
                $output .= "    <maxqueuelength>{$this->_maxqueuelength}</maxqueuelength>\n";
            }
            
            if ($this->_resample->isUsed()) {
                
                $output .= $this->_resample->export(true);
            }
            
            if ($this->_downmix) {
                
                $output .= "    <downmix>1</downmix>\n";
            }
            
            if ($this->_savefile) {
                
                $output .= "    <savefile>{$this->_savefile}<savefile>\n"; 
            }
            
            if ($this->_encode->isUsed()) {
                
                $output .= $this->_encode->export(true);
            } 
            
            $output .= "</instance>\n";
        } 
        else { 
            
            $output = [
                'hostname'          => $this->_hostname,
                'port'              => $this->_port,
                'password'          => $this->_password,
                'mount'             => $this->_mount, 
                'reconnectdelay'    => $this->_reconnectdelay, 
                'reconnectattempts' => $this->_reconnectattempts, 
                'retry-initial'    => $this->_retryinitial
            ];
            
            if ($this->_yp) {
                
                $output['yp'] = $this->_yp;
            }
            
            if (null !== $this->_maxqueuelength) {
                
                $output['maxqueuelength'] = $this->_maxqueuelength;
            }
            
            if ($this->_resample->isUsed()) {
                
                $output['resample'] = $this->_resample->export();
            }
            
            if ($this->_downmix) {
                
                $output['downmix'] = 1;
            }
            
            if ($this->_savefile) {
                
                $output['savefile'] = $this->_savefile;
            }
            
            if ($this->_encode->isUsed()) {
                
                $output['encode'] = $this->_encode->export();
            } 
        }
        
        return $output;
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
}