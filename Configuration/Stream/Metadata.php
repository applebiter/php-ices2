<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream; 
/** 
 * Metadata encapsulates and contains the metadata subportion of the ices 
 * configuration file.
 * 
 * Essentially, an arbitrary set of name=value pairs can be sent along in the 
 * header to the client. According to the online documentation for IceS, "... 
 * while Ogg Vorbis can handle any supplied tags, most players will only do 
 * anything with artist and title." However, if you are rolling your own client, 
 * then you can send any information you want, here. Add additional properties 
 * as desired. 
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
class Metadata 
{ 
    /**
     * _name 
     * 
     * @access protected
     * @var string
     */
    protected $_name; 
    
    /**
     * _genre 
     * 
     * @access protected 
     * @var string 
     */
    protected $_genre; 
    
    /** 
     * _description 
     * 
     * @access protected 
     * @var string
     */
    protected $_description; 
    
    /**
     * _url 
     * 
     * @access protected 
     * @var string
     */
    protected $_url; 
    
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
     * Populate property values from an input array
     * 
     * @access protected 
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        $this->_name = !empty($options['name']) 
            ? $options['name'] 
            : 'Untitled Stream'; 
        
        $this->_genre = !empty($options['genre']) 
            ? $options['genre'] 
            : null; 
        
        $this->_description = !empty($options['description']) 
            ? $options['description'] 
            : null; 
        
        $this->_url = !empty($options['url']) 
            ? $options['url'] 
            : null; 
    } 
    
    /**
     * export() 
     * 
     * Takes an optional boolean true indicating that the results should be 
     * returned in the form of a string representing an XML node. Returns the 
     * object properties and their values in array form
     * 
     * @param boolean optional default false
     * @return array
     */
    public function export($as_xml = false) 
    {
        if ($as_xml) {
            
            $output = "<metadata>\n" . 
                      "    <name>{$this->_name}</name>\n"; 
            
            if ($this->_description) {
                
                $output .= "    <description>{$this->_description}</description>\n";
            }
            
            if ($this->_genre) {
                
                $output .= "    <genre>{$this->_genre}</genre>\n";
            }
            
            if ($this->_url) {
                
                $output .= "    <url>{$this->_url}</url>\n";
            }
            
            $output .= "</metadata>\n"; 
        } 
        else { 
            
            $output = ['name' => $this->_name];
            
            if ($this->_description) {
                
                $output['description'] = $this->_description;
            }
            
            if ($this->_genre) {
                
                $output['genre'] = $this->_genre;
            }
            
            if ($this->_url) {
                
                $output['url'] = $this->_url;
            }
        }
        
        return $output;
    } 
    
    /**
     * import() 
     * 
     * Populate property values with input array
     * 
     * @param array $options
     */
    public function import(array $options = []) 
    {
        $this->_initialize($options);
    }
}