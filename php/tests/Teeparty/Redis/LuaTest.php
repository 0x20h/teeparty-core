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

Class LuaTest extends \PHPUnit_Framework_TestCase {

    public function testNonExistingScript()
    {
        $lua = new Lua;
        $this->assertNull($lua->getSource('/etc/passwd'));
        $this->assertNull($lua->getSHA1('/etc/passwd'));
        $this->assertNull($lua->getSource('../../../../../../../../passwd'));
        $this->assertNull($lua->getSHA1('../../../../../../../../passwd'));
    }


    public function testExistingScript()
    {
        $lua = new Lua;
        $source = $lua->getSource('task/ack');
        $this->assertNotNull($source);
        $this->assertNotNull($lua->getSHA1('task/ack'));
    }


    public function testGetScripts()
    {
        $lua = new Lua;
        $scripts = $lua->getScripts();
        $this->assertNotEmpty($scripts);
        $this->assertTrue(array_key_exists('task/put', $scripts));

        $lua = new Lua('/non-existant/foo/bar');
        $this->assertEquals(array(), $lua->getScripts());
    }
}
