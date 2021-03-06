<?php
namespace Peach\Http\Header;

use Peach\DT\HttpDateFormat;
use Peach\DT\Timestamp;
use PHPUnit_Framework_TestCase;

class HttpDateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpDate
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $format = new HttpDateFormat(-540);
        $time   = new Timestamp(2012, 5, 21, 8, 34, 45);
        $this->object = new HttpDate("Last-Modified", $time, $format);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 妥当なヘッダー名を引数に指定した場合に正常終了することを確認します.
     * 
     * @covers Peach\Http\Header\HttpDate::__construct
     */
    public function test__constructSuccess()
    {
        new HttpDate("Valid-Name", Timestamp::now(), new HttpDateFormat());
    }
    
    /**
     * 妥当でないヘッダー名を引数に指定した場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\HttpDate::__construct
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidName()
    {
        new HttpDate("Invalid/Name", Timestamp::now(), new HttpDateFormat());
    }
    
    /**
     * @covers Peach\Http\Header\HttpDate::format
     */
    public function testFormat()
    {
        $this->assertSame("Sun, 20 May 2012 23:34:45 GMT", $this->object->format());
    }
    
    /**
     * @covers Peach\Http\Header\HttpDate::getName
     */
    public function testGetName()
    {
        $this->assertSame("Last-Modified", $this->object->getName());
    }
}
