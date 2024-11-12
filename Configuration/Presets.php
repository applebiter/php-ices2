<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration;
/** 
 * Presets manages the storage and retrieval of IceS configurations as presets 
 * in a library. 
 * 
 * API 
 * 
 *     (Presets) Presets::get($dir) 
 *         -- Takes a string path to the directory that contains or will contain 
 *            configuration files as presets 
 *         -- Returns an instance of the Presets singleton 
 *         
 *     (boolean) save($name, Configuration $config) 
 *         -- Takes a string name to identify the preset, and an instance of the 
 *            Configuration object to be stored by that name 
 *         -- Returns a boolean true on success, false otherwise 
 *         
 *     (Configuration|boolean) fetch($name) 
 *         -- Takes the name of a preset configuration 
 *         -- Returns the nameed Configuration if found, a boolean false, 
 *            otherwise 
 *            
 *     (array) fetchAll() 
 *         -- Returns the array of available presets 
 *         
 *     (void) remove($name) 
 *         -- Takes a string name of a preset 
 *         -- Attempts to remove the named configuration file from the 
 *            filesystem and removes the element from the presets array 
 *            
 *     (void) removeAll() 
 *         -- Attempts to remove all preset configuration files from the 
 *            filesystem and removes all elements from the preset array 
 *            
 *     (boolean) hasErrors() 
 *         -- Indicates whether any errors were generated 
 *         
 *     (array) getErrors() 
 *         -- Return the array containing generated error messages, if any
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
use Ices2\Configuration;

class Presets
{  
    /**
     * A property which references the singleton instance to be created and
     * returned
     *
     * @static
     * @access protected
     * @var Presets
     */
    protected static $_instance = null; 
    
    /**
     * _dir 
     * 
     * Path to a writeable directory for storing preset files
     * 
     * @access protected 
     * @var string 
     */
    protected $_dir; 
    
    /**
     * _presets 
     * 
     * Array map of preset names corresponding to file names
     * 
     * @access protected
     * @var array
     */
    protected $_presets = [];
    
    /**
     * _errors 
     * 
     * @var array
     */
    protected $_errors = []; 
    
    /**
     * _has_errors 
     * 
     * @var boolean
     */
    protected $_has_errors = false;

    /**
     * This method returns an instance of this class.
     *
     * @static 
     * @param string $dir
     * @return Presets
     */
    public static function get($dir)
    {
        if (null === self::$_instance) {
            
            self::$_instance = new self;
            
            self::$_instance->_initialize($dir);
        }
        
        return self::$_instance;
    }
    
    /**
     * Constructor
     *
     * @access protected
     * @return Presets
     */
    protected function __construct() {}
    
    /**
     * Disallow cloning of this object to maintain its singleton aspect
     *
     * @final
     * @access private
     */
    final private function __clone() {}
    
    /**
     * initialize() 
     * 
     * Takes a string path to the writeable directory containing configuration 
     * presets, stored as XML or JSON documents.
     * 
     * @param string $dir 
     */
    protected function _initialize($dir)
    {
        $dir = rtrim($dir, '/');
        
        if (!is_dir($dir)) {
            
            $this->_errors[] = 'A valid directory path was not given.'; 
            
            $this->_has_errors = true;
        } 
        
        if (!is_writable($dir)) {
            
            $this->_errors[] = 'The given directory is not writeable.';
            
            $this->_has_errors = true;
        }
        
        $presets = scandir($dir); 
        
        foreach ($presets as $preset) {
            
            if ('.' != $preset && '..' != $preset) {
                
                $slug = pathinfo($preset, PATHINFO_FILENAME);
                
                $this->_presets[str_replace('_', ' ', $slug)] = $preset;
            }
        }
        
        $this->_has_errors = false; 
    } 
    
    /** 
     * save() 
     * 
     * Takes a string name for the preset and the configuration to be stored
     * 
     * @param string $name
     * @param Configuration $config 
     * @return boolean true on success, false otherwise
     */
    public function save($name, Configuration $config) 
    {
        $file = $this->_dir . '/' . str_replace(' ', '_', $name) . '.xml';
        
        if (file_put_contents($file, $config->export(true), LOCK_EX)) {
            
            $this->presets[$name] = basename($file);
            
            return true;
        } 
        
        return false;
    }
    
    /**
     * fetch() 
     * 
     * Takes the name of a configuration preset and returns a Configuration 
     * object initialized with the preset.
     * 
     * @param string $name
     * @return Configuration|boolean
     */
    public function fetch($name) 
    {
        if (!empty($this->_presets[$name])) { 
            
            $file = $this->_dir . '/' . $this->_presets[$name]; 
            
            $config = new Configuration();
            
            return $config->read($file);
        } 
        
        return false;
    }
    
    /**
     * fetchAll() 
     * 
     * Returns an array of names => filenames of preset configurations found in 
     * the presets directory.
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->_presets;
    }
    
    /**
     * remove() 
     * 
     * Takes the name of a configuration preset and tries to remove any matching 
     * preset from both the array and from the filesystem.
     */
    public function remove($name) 
    {
        if (isset($this->_presets[$name])) {
            
            $file = $this->_dir . '/' . $this->_presets[$name];
            
            if (is_file($file)) {
                
                unset($file);
            }
            
            unset($this->_presets[$name]);
        } 
    } 
    
    /**
     * removeAll() 
     * 
     * Deletes all presets from the filesystem and empties the presets array
     */
    public function removeAll() 
    {
        if (is_dir($this->_dir)) {
            
            $this->_presets = []; 
            
            $command = 'rm -rf ' . $this->_dir . '/*'; 
            
            $result = shell_exec($command); 
            
            current($result);
        } 
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
     * getErors() 
     * 
     * Returns generated error messages, if any. Also flushes all error messages 
     * and resets the $_has_errors flag to "false".
     * 
     * @return array
     */
    public function getErrors() 
    {
        $this->_has_errors = false; 
        $output = $this->_errors; 
        $this->_errors = []; 
        
        return $output;
    }
}