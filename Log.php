<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2;
/**
 * Log encapsulates log-related activities, including parsing and clearing of 
 * the ices2 log file.
 *
 * API:
 *
 *     (void) clear()
 *         -- Clears out all log entries 
 *         
 *     (array) getErrors() 
 *         -- Returns an array of generated error messages, if any 
 *         
 *     (boolean) hasErrors() 
 *         -- Indicates whether any errors have been generated 
 *         
 *     (array|boolean) parse()
 *         -- Returns the contents of the log file as an array of simple 
 *            objects, each representing one entry. The log entry direction is 
 *            reversed, so that the latest log entry is always at the top. 
 *         -- Returns a boolean false if the log cannot be parsed
 *
 * Examples:
 *
 *     Get the log 
 *     
 *         $logger = new Log('/path/to/logfile'); 
 *         
 *         $log = $logger->parse(); 
 *         
 *     Clear the log 
 *     
 *         $log->clear(); 
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
use stdClass;

class Log
{
    /**
     * _errors 
     * 
     * Holds any generated error messages
     * 
     * @access protected
     * @var array
     */
    protected $_errors = []; 
    
    /**
     * _has_errors 
     * 
     * Indicates whether any error messages have been generated
     * 
     * @access protected
     * @var boolean
     */
    protected $_has_errors = false;
    
    /**
     * _logfile 
     * 
     * The full path to the log file
     * 
     * @access protected 
     * @var string
     */
    protected $_logfile;
    
    /**
     * __construct() 
     * 
     * @param string $logfile
     */
    public function __construct($logfile)
    {
        if (!is_file($logfile)) {
            
            $this->_errors[] = "The given log file does not exist."; 
            $this->_has_errors = true;
        } 
        else {
            
            if (!is_readable($logfile)) {
                
                $this->_errors[] = "The given log file is not readable.";
                $this->_has_errors = true;
            } 
            
            $this->_logfile = $logfile;
        } 
    } 
    
    /** 
     * clear() 
     * 
     * Clears out the log file
     */
    public function clear()
    {
        file_put_contents($this->_logfile, "");
    }
    
    /** 
     * getErrors() 
     * 
     * Returns generated error messages, if any
     * 
     * @return array
     */
    public function getErrors() 
    {
        return $this->_errors;
    }
    
    /** 
     * hasErrors() 
     * 
     * Indicates whether any error message shave been generated
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->_has_errors;
    }
    
    /** 
     * parse() 
     * 
     * Parses and returns the log file into an array of objects, with each 
     * object representing a single entry in its parts: date, time, log level, 
     * the worker that generated the entry, and the message. 
     * 
     * @return array|boolean
     */
    public function parse() 
    {
        $handle = @fopen($this->_logfile, 'r'); 
        $log = [];
        
        if ($handle) {
            
            while (($line = fgets($handle, 4096)) !== false) { 
                
                $line = preg_replace('/\s+/', ' ',$line);
                
                $parts = explode(" ", rtrim($line, "\n")); 
                
                $stdOb = new stdClass();
                $stdOb->date = ltrim($parts[0], "["); 
                $stdOb->time = rtrim($parts[1], "]"); 
                $stdOb->level = $parts[2]; 
                $stdOb->worker = $parts[3]; 
                $stdOb->message = implode(" ", array_slice($parts, 4, (count($parts) - 1))); 
                
                $log[] = $stdOb;
            }
            
            fclose($handle); 
            
            return array_reverse($log);
        } 
        else { 
            
            $this->_errors[] = "Unable to open the log file.";
            $this->_has_errors = true;
        }
        
        return false;
    } 
}