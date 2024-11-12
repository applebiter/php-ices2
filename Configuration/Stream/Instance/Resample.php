<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration\Stream\Instance;
/** 
 * Resample encapsulates the <resample> subportion of the ices configuration 
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
class Resample 
{  
    /** 
     * _inrate 
     * 
     * @access protected 
     * @var integer
     */
    protected $_inrate; 
    
    /**
     * _outrate
     *
     * @access protected
     * @var integer
     */
    protected $_outrate; 
    
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
     * _inititalize() 
     * 
     * Populate property values 
     * 
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        if (!empty($options)) {
            
            $this->_is_used = true;
        }
        
        $this->_inrate = !empty($options['in-rate']) 
            ? $options['in-rate'] 
            : null; 
        
        $this->_outrate = !empty($options['out-rate'])
            ? $options['out-rate']
            : null;
    } 
    
    /**
     * export() 
     * 
     * Takes an optional boolean true indicating that the results are to be 
     * returned in the form of a string representing an XML node. Returns the 
     * property values in array if any, returns null otherwise
     * 
     * @param boolean optional default false
     * @return string|array|mixed[][]
     */
    public function export($as_xml = false) 
    {
        if ($this->_is_used) { 
            
            if ($as_xml) { 
                
                $output = "<resample>\n" . 
                          "    <in-rate>{$this->_inrate}</in-rate>\n" . 
                          "    <out-rate>{$this->_outrate}</out-rate>\n" . 
                          "</resample>\n"; 
            } 
            else { 
                
                $output = [
                    'in-rate'  => $this->_inrate,
                    'out-rate' => $this->_outrate
                ];
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