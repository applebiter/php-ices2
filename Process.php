<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2;
/**
 * Process offers a way to stop, start, and manage the IceS program on Linux 
 * systems.
 *
 * API:
 *
 *     (boolean) isRunning()
 *         -- Indicates whether the ices2 source server is running 
 *         
 *     (boolean) start($configfile) 
 *         -- Takes a string path to the configuration file to use 
 *         -- Returns a boolean true on success, false otherwise 
 *         
 *     (void) stop() 
 *         -- Stops the running ices2 source server 
 *         
 *     (void) playlistNext() 
 *         -- Sends a signal to the ices2 program to skip to the next track if 
 *            the playlist input module is used 
 *            
 *     (void) reloadMetadata() 
 *         -- Sends a signal to the ices2 program to reload the external 
 *            metadata file, if one is being used
 *
 * Examples:
 *
 *     To start the ices2 program, you'll need a working Configuration object... 
 *     
 *         $config = new Configuration(); 
 *         $config = $config->read('/path/to/config.xml'); 
 *         $ices2  = new Process($config); 
 *         
 *         $ices2->start(); 
 *         
 *     To stop the program: 
 *     
 *         $ices2->stop();  
 *         
 *     If the playlist input module is used, ices2 can be told to skip to the 
 *     next song in the list: 
 *     
 *         $ices2->playlistNext(); 
 *         
 *     If a metadata file is used with your chosen input plugin, ices2 can be 
 *     told to reload the metadata: 
 *     
 *         $ices2->reloadMetadata();
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
class Process
{
    /**
     * _commands 
     * 
     * @access protected
     * @var array
     */
    protected $_commands = [];
    
    /**
     * _is_running 
     * 
     * Indicates whether a process is running
     * 
     * @access protected
     * @var boolean
     */
    protected $_is_running = false; 
    
    /**
     * _pid
     * 
     * The integer process identifier for the running ices2 source server
     *
     * @access protected
     * @var integer
     */
    protected $_pid; 
    
    /**
     * _pidfile 
     * 
     * The full path to the file which will contain the process ID
     * 
     * @access protected 
     * @var string
     */
    protected $_pidfile; 
    
    /**
     * _program 
     * 
     * The path to the ices2 binary program. On Ubuntu, it is: /usr/bin/ices2
     * 
     * @access protected 
     * @var string
     */
    protected $_program; 
    
    /**
     * __construct() 
     * 
     * The class constructor
     * 
     * @param Configuration $config
     */
    public function __construct(Configuration $config) 
    {
        $this->_initialize($config);
    } 
    
    /**
     * _initialize() 
     * 
     * Populate property values from a Configuration object and an optional path 
     * to the ices2 binary program
     * 
     * @access protected 
     * @param Configuration $config
     */
    protected function _initialize(Configuration $config, $program = '/usr/bin/ices2') 
    {
        $this->_program = $program;
            
        $config = $config->export();
        
        $this->_pidfile = !empty($config['pidfile'])
            ? $config['pidfile']
            : '/tmp/ices2.pid'; 
        
        if (is_file($this->_pidfile)) {
        
            $this->_pid = (integer) trim(file_get_contents($this->_pidfile)); 
        }
        
        if ($this->_pid) {
            
            $this->_is_running = true;
        }
        
        $this->_commands['stop'] = "kill -INT {$this->_pid}"; 
        $this->_commands['input']['playlist-next'] = "kill -HUP {$this->_pid}";
        $this->_commands['input']['reload-metadata'] = "kill -USR1 {$this->_pid}"; 
    } 
    
    /** 
     * getPid() 
     * 
     * Returns the process ID of the running process, if there is one
     * 
     * @return integer
     */
    public function getPid()
    {
        return (integer) $this->_pid;
    }
    
    /**
     * isRunning() 
     * 
     * Indicates whether or not an instance of ices2 is running
     * 
     * @return boolean
     */
    public function isRunning() 
    {
        return $this->_is_running;
    } 
    
    /**
     * start() 
     * 
     * Takes the full path to a configuration file and starts the ices2 program 
     * with it
     * 
     * @param string $configfile 
     * @return boolean true on success, false on failure
     */
    public function start($configfile)
    {
        $command = "{$this->_program} {$configfile} > /dev/null &"; 
        
        shell_exec($command); 
        
        sleep(1); 
        
        if (is_file($this->_pidfile)) { 
            
            $this->_pid = (integer) trim(file_get_contents($this->_pidfile)); 
        }
        
        if ($this->_pid) {
            
            $this->_is_running = true; 
            
            return true;
        } 
        
        return false; 
    } 
    
    /**
     * stop() 
     * 
     * Stops the running instance of ices2
     */
    public function stop()
    {
        if ($this->_is_running) {
            
            shell_exec($this->_commands['stop']); 
            
            sleep(1); 
            
            if (!is_file($this->_pidfile)) {
                
                $this->_is_running = false; 
            }
        }
    } 
    
    /**
     * playlistNext() 
     * 
     * Sends a signal to the ices2 program which makes it skip to the next track 
     * if the playlist input module is being used
     */
    public function playlistNext()
    {
        if ($this->_is_running) {
            
            shell_exec($this->_commands['input']['playlist-next']);
        }
    } 
    
    /**
     * reloadMetadata() 
     * 
     * Sends a signal to the ices2 program which makes it reload the external 
     * metadata file if the input plugin uses one
     */
    public function reloadMetadata() 
    {
        if ($this->_is_running) {
            
            shell_exec($this->_commands['input']['reload-metadata']);
        }
    }
}