<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2;
/** 
 * Ices2 is a wrapper around the ices2 binary program (ices2 on Ubuntu and 
 * variants, but named ices on other systems).
 * 
 * The IceS 2 source server is a binary program distributed by the good folks at
 * Xiph.org, as companion software for their Icecast 2 streaming server. IceS 2 
 * provides a variety of ways to get sound into the Icecast 2 streaming server. 
 * IceS 2 parses an XML configuration file on startup, and if it is able to run 
 * without error using the parameters found in the configuration, there is 
 * little left to do with the program but let it run until you want to stop it. 
 * If the Playlist input module is used, a signal can be sent to the IceS 2 
 * program to skip to the next track. If the input module uses a metadata file, 
 * a signal can be sent to the IceS 2 source server, causing it to relaod the 
 * data from the file. 
 * 
 * This, top-level class is mostly a proxy for methods belonging to subordinate 
 * classes, which may be used independently or directly. 
 * 
 * API:
 * 
 *     (void) clearLog() 
 *         -- Clears the log file of entries 
 *         
 *     (void) configure(array $options) 
 *         -- Takes a multidimensional, associative array as input 
 *         -- Re-initializes the configuration with the supplied values
 *         
 *     (Ices2) Ices2::get() 
 *         -- Static method returns a singleton instance of the Ices2 object 
 *         
 *     (array) getErrors() 
 *         -- Returns an array of error messages, if any
 *         
 *     (array) getHistory($reverse = true) 
 *         -- Takes an optional boolean indicating the direction to sort the 
 *            list. By default, the list is returned in reverse order, such that 
 *            the currently playing song is the first element in the array. By 
 *            passing a boolean false to the method, you can the list in the 
 *            order that they were played, with the currently playing song as 
 *            the last element in the array. 
 *         -- Returns an array of absolute paths to all song files that were 
 *            played 
 *         
 *     (array) getLog() 
 *         -- Returns the parsed log as an array of objects, one for each entry 
 *            in the log. The entries are returned in reverse order, such that 
 *            the most recent entry is the first element in the array
 *         
 *     (integer) getPid() 
 *         -- Returns the process ID of the running IceS 2 binary program, if it 
 *            is in fact running
 *         
 *     (boolean) hasErrors() 
 *         -- Indicates whether any errors have been generated
 *         
 *     (boolean) isRunning() 
 *         -- Indicates whether the IceS 2 binary program is running
 *         
 *     (boolean) loadPreset($name) 
 *         -- Takes a string naming a stored configuration preset to load values 
 *            from the filesystem into the current configuration 
 *         -- Returns a boolean indicating success or failure
 *         
 *     (void) next() 
 *         -- If the Playlist input module is used, calling this method will 
 *            signal the IceS 2 binary program to skip to the next track in the 
 *            list.
 *         
 *     (boolean) savePreset($name) 
 *         -- Takes a string naming the current configuration, and writing it in 
 *            the /data/presets subdirectory for later retrieval 
 *         -- Returns a boolean indicating success or failure 
 *         
 *     (integer|boolean) start() 
 *         -- Attempts to start the IceS 2 binary program 
 *         -- Returns an integer process ID on success, boolean false otherwise 
 *         
 *     (void) stop()  
 *         -- Attempts to stop the IceS 2 binary program
 *         
 *     (void) updateMetadata() 
 *         -- If a metadata file is used by the input module, this method 
 *            signals the IceS 2 binary program to reload the file data 
 *            
 *     (string) version()  
 *         --  Returns the version information from the IceS 2 binary program
 *     
 * Examples: 
 * 
 *     . 
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
class Ices2 
{ 
    /**
     * _binary_path 
     * 
     * The full path to the IceS 2 binary program
     * 
     * @access protected 
     * @var string
     */
    protected $_binary_path; 
    
    /**
     * _playlist_program 
     * 
     * @access protected 
     * @var string
     */
    protected $_playlist_program;

    /**
     * _errors
     *
     * Holds error messages if any are generated
     *
     * @var array
     */
    protected $_errors = [];
    
    /**
     * _has_errors
     *
     * Indicates whether an error has been generated
     *
     * @var boolean
     */
    protected $_has_errors = false; 
    
    /**
     * _instance 
     * 
     * @static
     * @access protected 
     * @var Ices2
     */
    static protected $_instance = null; 
    
    /**
     * _configuration
     * 
     * @access protected 
     * @var Configuration
     */
    protected $_configuration; 
    
    /**
     * _log 
     * 
     * @access protected
     * @var Log
     */
    protected $_log; 
    
    /**
     * _process 
     * 
     * @access protected
     * @var Process
     */
    protected $_process;
    
    /**
     * _root 
     * 
     * @access protected 
     * @var string
     */
    protected $_root;
    
    /**
     * __construct()
     *
     * @return Ices2
     */
    protected function __construct() 
    { 
        $this->_initialize();
    } 
    
    /**
     * _initialize() 
     * 
     * This object will be the single interface to the various objects in this 
     * package. The _initialize() method is where the various supporting objects 
     * are initialized and saved in this instance.
     * 
     * @access protected 
     */
    protected function _initialize()
    {
        // get the absolute path to the directory containing the PHP app
        $this->_root = dirname(__FILE__); 
        
        // get and initialize the configuration object
        $this->_configuration = new Configuration(); 
        $this->_configuration = $this->_configuration->read("{$this->_root}/data/default-config.xml");
        
        // gather up errors, if any 
        if ($this->_configuration->hasErrors()) {
            
            $this->_errors['configuration'] = $this->_configuration->getErrors(); 
            $this->_has_errors = true;
        }
        
        // export the config to an array
        $config = $this->_configuration->export();
        
        // get the full path to the log file from the exported config
        $this->_log = new Log($config['logpath'] . '/' . $config['logfile']); 
        
        // gather up errors, if any 
        if ($this->_log->hasErrors()) {
            
            $this->_errors['log'] = $this->_log->getErrors(); 
            $this->_has_errors = true;
        }
        
        // get a process object using the config object
        $this->_process = new Process($this->_configuration); 
        
        $this->_playlist_program = "{$this->_root}/data/scripts/randomogg";
    }
    
    /**
     * clearLog()
     *
     * Clears the log file
     */
    public function clearLog()
    {
        $this->_log->clear();
    }
    
    /**
     * configure()
     *
     * Allows programmatic access to the configuration through the wrapper
     *
     * @param array $options
     */
    public function configure(array $options)
    {
        $this->_configuration->import($options);
        
        if (!$this->_configuration->write("{$this->_root}/data/default-config.xml")) {
            
            $this->_errors['configuration'][] = 'Unable to write configuration data to file';
            $this->_has_errors = true;
        }
        
        $this->_binary_path = !empty($options['binary_path']) 
            ? $options['binary_path'] 
            : null; 
        
        $this->_playlist_program = !empty($options['playlist_program']) 
            ? $options['playlist_program'] 
            : "{$this->_root}/data/scripts/randomogg";
    }
    
    /**
     * get() 
     * 
     * @static
     * @return Ices2
     */
    public static function get() 
    {
        if (!self::$_instance) {
            
            self::$_instance = new self; 
        } 
        
        return self::$_instance;
    } 
    
    /**
     * getConfiguration() 
     * 
     * Returns the Configuration object
     * 
     * @return Configuration
     */
    public function getConfiguration() 
    {
        return $this->_configuration;
    }
    
    /**
     * getErrors()
     *
     * Return the error, if any exists, and boolean false otherwise
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * getHistory()
     * 
     * @param boolean $reverse default true
     * @return array
     */
    public function getHistory($reverse = true)
    {
        $log = $this->getLog();
        $trim = 'Currently playing ';
        $history = [];
        
        if (count($log)) {
            
            foreach ($log as $entry) {
                
                if ('INFO' === $entry->level
                    && $trim === substr($entry->message, 0, 18)) {
                        
                        $history[] = ltrim($entry->message, 'Currently playing ');
                    }
            }
        }
        
        return !$reverse ? array_reverse($history) : $history;
    }
    
    /**
     * getLog()
     *
     * Returns the parsed and formatted IceS log
     *
     * @return array|boolean
     */
    public function getLog()
    {
        return $this->_log->parse();
    }
    
    /**
     * getPid()
     *
     * @return integer
     */
    public function getPid()
    {
        if ($this->isRunning()) {
            
            return $this->_process->getPid();
        }
    } 
    
    /**
     * getProcess() 
     * 
     * Return the Process object
     * 
     * @return Process
     */
    public function getProcess() 
    {
        return $this->_process;
    }
    
    /**
     * hasErrors()
     *
     * Indicates whether an error has occurred
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->_has_errors;
    }
    
    /**
     * isRunning() 
     * 
     * Indicates whether the IceS source server is running
     * 
     * @return boolean
     */
    public function isRunning()
    {
        return $this->_process->isRunning();
    }
    
    /**
     * next()
     *
     * If the Playlist input module is used, this method will signal IceS to
     * skip to the next track
     */
    public function next()
    {
        if ($this->isRunning()) {
            
            $this->_process->playlistNext();
        }
    } 
    
    /**
     * savePreset() 
     * 
     * 
     * 
     * @param string $name
     * @return boolean
     */
    public function savePreset($name) 
    {
        $dir = $this->_root . '/data/presets';
        
        $presets = $this->_configuration->getPresetsManager($dir); 
        
        return $presets->save($name, $this->_configuration);
    } 
    
    /**
     * loadPreset() 
     * 
     * Takes a string representing a named preset stored as an XML configuration 
     * file in the presets directory
     * 
     * @param string $name
     * @return boolean|Configuration
     */
    public function loadPreset($name) 
    {
        $dir = $this->_root . '/data/presets';
        
        $presets = $this->_configuration->getPresetsManager($dir); 
        
        $config = $presets->fetch($name); 
        
        if ($config) {
            
            $this->_configuration = $config; 
            
            if ($this->_configuration->hasErrors()) {
                
                $this->_errors['configuration'] = $this->_configuration->getErrors(); 
                $this->_has_errors = true; 
                
                return false;
            } 
            else { 
                
                $result = $this->_configuration->write("{$this->_root}/data/default-config.xml"); 
                
                return $result ? true : false;
            }
        } 
        else { 
            
            return false;
        }
    }
    
    /**
     * start() 
     * 
     * Attempts to start the IceS source server. Returns the integer process ID 
     * if successful, a boolean false otherwise
     * 
     * @return integer|boolean
     */
    public function start()
    {
        if (!$this->isRunning()) 
        {
            $this->_process->start("{$this->_root}/data/default-config.xml");
                
            return $this->_process->getPid();
        } 
        else { 
            
            return $this->_process->getPid();
        }
    } 
    
    /**
     * stop() 
     * 
     * Attempts to stop the running process
     */
    public function stop()
    {
        if ($this->isRunning()) {
            
            $this->_process->stop();
        }
    }
    
    /**
     * updateMetadata()
     *
     * If a metadata file is used with the input module, this method will signal
     * IceS to reload the file data.
     */
    public function updateMetadata()
    {
        if ($this->isRunning()) {
            
            $this->_process->reloadMetadata();
        }
    }
    
    /**
     * version()
     *
     * Returns the version information from the flac binary program
     * 
     * @param string $binary_path optional default '/usr/bin/ices2' Ubuntu-style
     * @return string
     */
    public function version($binary_path = '/usr/bin/ices2')
    {
        $command = "{$binary_path} 2>&1"; 
        $command = escapeshellcmd($command); 
        $result = shell_exec($command);
        
        return $result;
    } 
} 
