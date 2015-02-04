<?php
namespace tests;

// Load global function mockup
use samson\core\Error;
use samson\core\Service;

require 'ftp_mockup.php';

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class DeployTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \samsonphp\deploy\Deploy */
    public $deploy;

    /** Tests init */
    public function setUp()
    {
        Error::$OUTPUT = false;

        $this->deploy = Service::getInstance('\samsonphp\deploy\Deploy');
        $this->deploy->remote = $this->getMockBuilder('\samsonphp\deploy\Remote')
            ->disableOriginalConstructor()
            ->getMock();
        $this->deploy->wwwroot = 'test';
        $this->deploy->host = 'test';
        $this->deploy->username = 'test';
        $this->deploy->password = 'test';
        $this->deploy->sourceroot = sys_get_temp_dir();
    }

    public function testFailedInit()
    {
        $this->deploy->wwwroot = '';
        $this->deploy->init();
        $this->deploy->sourceroot = '';
        $this->deploy->init();
    }

    public function testInit()
    {
        $this->deploy->init();
    }

    public function testBase()
    {
        define('__SAMSON_REMOTE_APP', true);
        $this->deploy->__BASE();
        $this->deploy->__BASE();
    }
}
