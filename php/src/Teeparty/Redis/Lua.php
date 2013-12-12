<?php
/**
 * This file is part of the teeparty-schema package.
 *
 * Copyright (c) 2013 Jan Kohlhof <kohj@informatik.uni-marburg.de>
 *
 * Permission is hereby granted, free of charge, to any person 
 * obtaining a copy of this software and associated documentation 
 * files (the "Software"), to deal in the Software without 
 * restriction, including without limitation the rights to use, 
 * copy, modify, merge, publish, distribute, sublicense, and/or 
 * sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
 * IN THE SOFTWARE.
 */

namespace Teeparty\Redis;

/**
 * This class provides access to lua scripts for task related operations.
 *
 * Teeparty provides several lua scripts to perform task related operations.
 * This class provides access to these scripts via their names relative from the
 * redis/ folder.
 */
class Lua {

    private $scripts = array();
    private $sha1 = array();
    private $baseDir = null;

    public function __construct($baseDir = null)
    {
        $this->baseDir = $baseDir 
            ? $baseDir 
            : realpath(__DIR__ . '/../../../../' . '/redis/');
    }


    /**
     * Return the script source for the given script.
     *
     *
     * @param string $script The script name. Must match [a-zA-Z/-_]+.
     * @return string the script source. null if script was not found.
     */
    public function getSource($script) 
    {
        if (!preg_match("/[\/a-z\-_]+/i", $script)) {
            return null;
        }
        
        if (!isset($this->scripts[$script])) { 
            $file = realpath($this->baseDir . '/' . $script . '.lua');
            
            if (!is_readable($file)) {
                return null;
            }

            $this->scripts[$script] = file_get_contents($file);
            $this->sha1[$script] = sha1($this->scripts[$script]);
        }

        return $this->scripts[$script];
    }


    /**
     * Return the SHA1 hash of the given script.
     *
     * The SHA1 value is used in combination with the evalSHA command from 
     * redis in order to trigger the server side cached script.
     *
     * @param string $script The script name.
     * @return string SHA1 hash of the script. null if script was not found.
     */
    public function getSHA1($script) {
        if (!isset($this->sha1[$script])) {
            $script = $this->getSource($script);

            if (!$script) {
                return null;
            }

            $this->sha1[$script] = sha1($script);
        }
        
        return $this->sha1[$script];
    }


    /**
     * Retrieve all available lua scripts relative from baseDir.
     *
     * @return string[] dict with script names as keys, source as value.
     */
    public function getScripts() {
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->baseDir),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
        } catch (\UnexpectedValueException $e) {
            // dir not found
            return array();
        }

        while ($iterator->valid()) {
            $element = $iterator->current();
            $iterator->next();

            $filename = $element->getPathname();

            if (strpos($filename, '.lua') !== strlen($filename) - 4) {
                continue;
            }

            $scriptName = substr($filename, strlen($this->baseDir . '/'), -4);

            if (isset($this->scripts[$scriptName])) {
                continue;
            }

            $this->scripts[$scriptName] = file_get_contents($filename);
        }

        return $this->scripts;
    }
}
