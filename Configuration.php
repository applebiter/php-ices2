<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2; 
/** 
 * Configuration encapsulates the Ices2 configuration file, its parameters, and 
 * their values.
 * 
 * .  
 * 
 * API:
 * 
 *     (string|array|mixed[][]) export($as_xml = false) 
 *         -- Returns the entire configuration as a nested, associative array by 
 *            default 
 *         -- Takes an optional boolean indicating that the object should be 
 *            exported as a string representing the XML document. 
 *            
 *     (array) getErrors() 
 *         -- Returns an array of error messages if any, empty array otherwise
 *            
 *     (Presets) getPresetsManager($dir) 
 *         -- Takes a string path to the directory for containing configuration 
 *            presets 
 *         -- Return the Ices2\Configuration\Presets object 
 *         
 *     (boolean) hasErrors() 
 *         -- Indicates whether any errors have been generated
 *         
 *     (Configuration) import(array $options = []) 
 *         -- Takes an optional, multi-dimensional array of configuration 
 *            parameter => value pairs, and uses them to populate Configuration 
 *            property values 
 *         -- Returns itself to allow for fluent notation  
 *           
 *     (Configuration) read($source, $format = 'xml') 
 *         -- Takes a string representinig either the full path to a 
 *            configuration file, or the contents of a configuration file 
 *         -- Optionally takes a string argument specifying the file format. By 
 *            default the format is 'xml', but 'json' is available. 
 *         -- Returns a Configuration object
 *            
 *     (Configuration|boolean) write($destination, $format = 'xml', $overwrite = true) 
 *         -- Takes a path representing the destination file 
 *         -- Optionally takes a string representing the file format to write. 
 *            By default the format is 'xml', but 'json' is available. 
 *         -- Optionally takes a boolean indicating whether to overwrite an 
 *            existing configuration file by the same name
 *         -- Returns itself to allow for fluent notation 
 *     
 * Examples: 
 * 
 *     Get a raw Configuration object... 
 *     
 *         $config = new Configuration(); 
 *         
 *     ...then initialize it with values in a configuration file... 
 *     
 *         $config = $config->read('/path/to/config.xml'); 
 *         
 *     Alternately, initialize a Configuration object and provide initial values 
 *     in a nested, associative array:
 *     
 *         $config = new Configuration([
 *             'background' => 1, 
 *             'logpath' => '/path/to/log/directory', 
 *             'logfile' => 'ices2.log', 
 *             'logsize' => 2048, 
 *             'loglevel' => 3, 
 *             'consolelog' => 0, 
 *             'pidfile' => '/path/to/ices2.pid', 
 *             'stream' => [ 
 *                 'metadata' => [ 
 *                 'name' => 'Total Collection Randomized', 
 *                 'description' => 'The entire music collection has been randomized...', 
 *                 'genre' => 'All Genres', 
 *                 'url' => 'https://your.website/' 
 *             ], 
 *             'input' => [ 
 *                 'module' => 'playlist', 
 *                 'param' => [ 
 *                     'type' => 'script', 
 *                     'program' => '/path/to/randomogg' 
 *                 ] 
 *             ], 
 *             'instance' => [ 
 *                 0 => [ 
 *                     'hostname' => 'icecast2.hostname', 
 *                     'port' => 8000, 
 *                     'password' => 'P@55word', 
 *                     'mount' => '/mount.ogg', 
 *                     'reconnectdelay' => 3, 
 *                     'reconnectattempts' => 3, 
 *                     'retry-initial' => 0 
 *                 ] 
 *             ]
 *         ]); 
 *         
 *     Once you have a configuration object, you can write it to file: 
 *     
 *         $config = $config->write('/path/to/destination'); 
 *         
 *     You can also save your current configuration as a "preset", allowing you 
 *     to have several, ready-to-run configurations for different uses. 
 *     
 *     First, fetch an instance of the presets manager: 
 *     
 *         $presets = $config->getPresetsManager('path/to/presets/directory'); 
 *        
 *     ...then save the preset with a descriptive name:
 *        
 *         if (!$presets->save('Random Playlist', $config)) {
 *             echo "The preset could not be saved.";
 *         } 
 *         
 *     Now, at some future time, you can get a blank configuration object and 
 *     use it at will: 
 *     
 *         $config = new Configuration(); 
 *         
 *         $presets = $config->getPresetsManager('/path/to/presets'); 
 *         
 *         $config = $presets->fetch('Random Playlist'); 
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
use Ices2\Configuration\Presets; 
use Ices2\Configuration\Reader; 
use Ices2\Configuration\Stream; 
use Ices2\Configuration\Writer; 

class Configuration 
{  
    /**
     * _errors 
     * 
     * @access protected
     * @var array
     */
    protected $_errors = []; 
    
    /**
     * _has_errors 
     * 
     * @access protected
     * @var string
     */
    protected $_has_errors = false;

    /**
     * _background 
     * 
     * Set to 1 if you want IceS to put itself into the background.
     * 
     * @access protected 
     * @var integer
     */
    protected $_background;
    
    /**
     * _logpath 
     * 
     * A directory that can be written to by the user that IceS runs as. This 
     * can be anywhere you want but as log files are created, write access to 
     * the stated must be given.
     * 
     * @access protected 
     * @var string
     */
    protected $_logpath; 
    
    /**
     * _logsize 
     * 
     * When the log file reaches this size (in kilobytes) then the log file will 
     * be cycled (the default is 2Meg)
     * 
     * @access protected
     * @var integer
     */
    protected $_logsize;
    
    /**
     * _logfile
     *
     * The name of the logfile created. On log re-opening the existing logfile
     * is renamed to <logfile>.1
     *
     * @access protected
     * @var string
     */
    protected $_logfile; 
    
    /** 
     * _loglevel 
     * 
     * A number that represents the amount of logging performed.
     *     1 - Only error messages are logged
     *     1 - 2 - The above and warning messages are logged
     *     1 - 3 - The above and information messages are logged
     *     1 - 4 - The above and debug messages are logged 
     *     
     * @access protected 
     * @var integer
     */
    protected $_loglevel; 
    
    /** 
     * _consolelog 
     * 
     * A value of 1 will cause the log messages to appear on the console instead 
     * of the log files. Setting this to 1 is generally discouraged as logs are 
     * cycled and writing to screen can cause stalls in the application, which 
     * is a problem for timing critical applications. 
     * 
     * @access protected
     * @var integer
     */
    protected $_consolelog; 
    
    /** 
     * _pidfile 
     * 
     * State a filename with path to be created at start time. This file will 
     * then contain a single number which represents the process id of the 
     * running IceS. This process id can then be used to signal the application 
     * of certain events. 
     * 
     * @access protected
     * @var string
     */
    protected $_pidfile; 
    
    /** 
     * _stream 
     * 
     * The Stream object contains and encapsulates the various subcomponents of 
     * <stream> subportion of the ices configuration file.
     * 
     * @access protected 
     * @var Stream
     */
    protected $_stream; 
    
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
     * Takes an optional array of values for populating object properties
     * 
     * @access protected 
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        $this->_background = !empty($options['background']) 
            ? 1 
            : 0; 
        
        $this->_logpath = !empty($options['logpath']) 
            ? $options['logpath'] 
            : '/var/log/ices2'; 
        
        $this->_logfile = !empty($options['logfile']) 
            ? $options['logfile'] 
            : 'ices2.log';
            
        $this->_logsize = !empty($options['logsize'])
            ? $options['logsize']
            : 2048; 
        
        $this->_loglevel = isset($options['loglevel']) 
            ? $options['loglevel'] 
            : 2;
            
        $this->_consolelog = !empty($options['consolelog'])
            ? 1
            : 0;
            
        $this->_pidfile = !empty($options['pidfile'])
            ? $options['pidfile']
            : "/tmp/ices2.pid"; 
        
        $this->_stream = isset($options['stream']) 
            ? new Stream($options['stream']) 
            : new Stream();
    } 
    
    /**
     * export() 
     * 
     * Takes an optional boolean true indicating that the results should be 
     * returned in the form of a string representing an XML node. Returns the 
     * properties and values in a nested, associative-array form, otherwise
     * 
     * @param boolean optional default false
     * @return string|array|mixed[][]
     */
    public function export($as_xml = false) 
    {
        if ($as_xml) {
            
            $output = "<?xml version=\"1.0\"?>\n" . 
                      "<ices>\n" . 
                      "    <background>1</background>\n" . 
                      "    <logpath>{$this->_logpath}</logpath>\n" . 
                      "    <logfile>{$this->_logfile}</logfile>\n" . 
                      "    <logsize>{$this->_logsize}</logsize>\n" . 
                      "    <loglevel>{$this->_loglevel}</loglevel>\n" . 
                      "    <pidfile>{$this->_pidfile}</pidfile>\n";
            
            $output .= $this->_stream->export(true);
            
            $output .= "</ices>\n"; 
        } 
        else { 
            
            $output = [
                'background' => (integer) $this->_background,
                'logpath'    => $this->_logpath,
                'logfile'    => $this->_logfile,
                'logsize'    => $this->_logsize,
                'loglevel'   => $this->_loglevel,
                'consolelog' => (integer) $this->_consolelog,
                'pidfile'    => $this->_pidfile,
                'stream'     => $this->_stream->export()
            ];
        }
        
        return $output;
    } 
    
    /**
     * getErrors() 
     * 
     * Returns the array of error messsages, if any
     * 
     * @return array 
     */
    public function getErrors() 
    {
        return $this->_errors;
    }
    
    /**
     * getPresetsManager() 
     * 
     * Takes a string path to the writeable presets directory, returns an 
     * instance of Presets, a singleton.
     * 
     * @param string $dir
     * @return Presets
     */
    public function getPresetsManager($dir) 
    {
        return Presets::get($dir);
    } 
    
    /**
     * hasErrors() 
     * 
     * Indicates whether any errors have been generated
     * 
     * @return string
     */
    public function hasErrors() 
    {
        return $this->_has_errors;
    }
    
    /**
     * import() 
     * 
     * Populate property values using input array
     * 
     * @param array $options
     */
    public function import(array $options = []) 
    {
        $this->_initialize($options); 
        
        return $this;
    } 
    /**
     * read() 
     * 
     * Takes a string representing a filename containing source content, or else 
     * the source content, itself. Expects XML content by default, but can parse 
     * JSON, as well.
     * 
     * @param string $source
     * @param string $format optional
     * @return Configuration
     */
    public function read($source, $format = 'xml') 
    {
        $this->_initialize(Reader::read($source, $format)); 
        
        if (Reader::hasErrors()) {
            
            foreach (Reader::getErrors() as $error) {
                
                $this->_errors[] = $error; 
                $this->_has_errors = true;
            }
        }
        
        return $this;
    }
    
    /**
     * write() 
     * 
     * Takes a string filename path, expects to write XML by default, but JSON 
     * output is available.
     * 
     * @param string $destination
     * @param string $format optional
     * @param boolean $overwrite optional
     * @return Configuration|boolean
     */
    public function write($destination, $format = 'xml', $overwrite = true) 
    {
        if (Writer::write($this, $destination, $format, $overwrite)) { 
            
            return $this;
        } 
        
        if (Writer::hasErrors()) {
            
            foreach (Writer::getErrors() as $error) {
                
                $this->_errors[] = $error;
                $this->_has_errors = true;
            }
        }
        
        return false;
    } 
}