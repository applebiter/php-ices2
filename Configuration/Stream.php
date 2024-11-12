<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Ices2\Configuration; 
/** 
 * Stream encapsulates and contains the <stream> subportion of the ices 
 * configuration file. 
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
use Ices2\Configuration\Stream\Input\Factory; 
use Ices2\Configuration\Stream\Instance; 
use Ices2\Configuration\Stream\Metadata;

class Stream 
{  
    /** 
     * _metadata 
     * 
     * This section describes what metadata information is passed in the headers 
     * at connection time to icecast. This applies to each instance defined 
     * within the stream section but maybe overridden by a per-instance 
     * <metadata> section.
     * 
     * @access protected
     * @var Metadata
     */
    protected $_metadata; 
    
    /** 
     * _input 
     * 
     * @access protected
     * @var Input
     */
    protected $_input; 
    
    /**
     * _instances
     * 
     * Multiple instances can be defined to allow for multiple encodings, this 
     * is useful for encoding the same input to multiple bitrates. Each instance 
     * defines a particular set actions that occur on the passed in audio. Any 
     * modifications to the input data is isolated to the instance.
     * 
     * @access protected
     * @var array
     */
    protected $_instances = [];
    
    /**
     * __construct() 
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
     * Populate property values from input array
     * 
     * @access protected 
     * @param array $options
     */
    protected function _initialize(array $options = []) 
    {
        $this->_metadata = !empty($options['metadata']) 
            ? new Metadata($options['metadata']) 
            : new Metadata(); 
        
        $this->_input = !empty($options['input']) 
            ? Factory::get($options['input']) 
            : Factory::get([]); 
        
        if (!empty($options['instance']) && is_array($options['instance'])) {
            
            foreach ($options['instance'] as $instance) {
                
                $this->_instances[] = new Instance($instance);
            }
        } 
        else {
            
            $this->_instances[] = new Instance();
        }
    } 
    
    /**
     * export() 
     * 
     * Takes an optional boolean true indicating that the results should be 
     * returned in the form of a string representing an XML node. Export 
     * property values in array form, otherwise
     * 
     * @return string[]|mixed[][]
     */
    public function export($as_xml = false) 
    {
        if ($as_xml) { 
            
            $output = "<stream>\n"; 
            
            $output .= $this->_metadata->export(true); 
            $output .= $this->_input->export(true); 
            
            foreach ($this->_instances as $instance) {
                
                $output .= $instance->export(true);
            }
            
            $output .= "</stream>\n"; 
        } 
        else { 
            
            $output = [
                'metadata' => $this->_metadata->export(),
                'input'    => $this->_input->export()
            ];
            
            foreach ($this->_instances as $instance) {
                
                $output['instance'][] = $instance->export();
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