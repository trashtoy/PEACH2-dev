<?php
namespace Peach\Util;

class ArrayMapEntryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayMap
     */
    private $map;
    
    /**
     * @var array
     */
    private $entryList;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->map = new ArrayMap();
        $this->map->put("key1", "foo");
        $this->map->put("key2", "bar");
        $this->map->put("key3", "baz");
        $this->entryList = $this->map->entryList();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * ArrayMap に値を put した際に ArrayMapEntry オブジェクトが生成されることを確認します.
     * @covers Peach\Util\ArrayMapEntry::__construct
     */
    public function test__construct()
    {
        $map = new ArrayMap();
        $map->put("key", "value");
        $entryList = $map->entryList();
        $this->assertInstanceOf("Peach\\Util\\ArrayMapEntry", $entryList[0]);
    }
    
    /**
     * setValue() をテストします. 以下を確認します.
     * 
     * - 引数に指定した値で MapEntry の値が更新されること
     * - 元の ArrayMap の該当するマッピングが上書きされること
     * 
     * @covers Peach\Util\ArrayMapEntry::setValue
     */
    public function testSetValue()
    {
        $this->entryList[0]->setValue("hoge");
        $this->assertSame("hoge", $this->entryList[0]->getValue());
        $expected = array("key1" => "hoge", "key2" => "bar", "key3" => "baz");
        $this->assertSame($expected, $this->map->asArray());
    }
    
    /**
     * この MapEntry にセットされているキーを返すことを確認します.
     * 
     * @covers Peach\Util\ArrayMapEntry::getKey
     */
    public function testGetKey()
    {
        $this->assertSame("key2", $this->entryList[1]->getKey());
    }
    
    /**
     * この MapEntry にセットされている値を返すことを確認します.
     * 
     * @covers Peach\Util\ArrayMapEntry::getValue
     */
    public function testGetValue()
    {
        $this->assertSame("baz", $this->entryList[2]->getValue());
    }
    
    /**
     * "[キー=値]" 形式の文字列を返すことを確認します.
     * 
     * @covers Peach\Util\ArrayMapEntry::__toString
     */
    public function test__toString()
    {
        $this->assertSame("[key3=baz]", $this->entryList[2]->__toString());
    }
}
