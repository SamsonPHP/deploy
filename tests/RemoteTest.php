<?php
namespace tests;

use samsonphp\deploy\Remote;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class RemoteTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonphp\deploy\Remote */
    public $remote;

    /** Tests init */
    public function setUp()
    {
        \samson\core\Error::$OUTPUT = false;

        $this->remote = new Remote('test', 'test', 'test');
    }

    public function testFailedLogin()
    {
        $this->remote = new Remote('test', 'test2', 'test');
    }

    public function testWrite()
    {
        $this->assertEquals(true, $this->remote->write('test'));
        $this->assertEquals(false, $this->remote->write('test2'));
    }

    public function testMkDir()
    {
        $this->remote->mkDir('test');
        $this->remote->mkDir('test2');
    }


    public function testCdUp()
    {
        $this->remote->cdup();
    }

    public function testCD()
    {
        $this->remote->cd('test');
        $this->remote->cd('test2');
    }

    public function testIsOld()
    {
        $this->assertEquals(false, $this->remote->isOld('test'));
    }
}
