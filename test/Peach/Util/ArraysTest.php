<?php
namespace Peach\Util;

class ArraysTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    private static $fp;
    
    public static function setUpBeforeClass()
    {
        self::$fp = fopen(__FILE__, "rb");
    }
    
    public static function tearDownAfterClass()
    {
        fclose(self::$fp);
    }
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * {@link Arrays::max} のテストです.
     * 以下を確認します.
     * 
     * - 与えられた配列の中で一番大きな値を返すこと
     * - Comparable を実装しているオブジェクトの場合, compareTo() メソッドを使って大小を比較すること
     * - 空の配列が指定された場合は null を返すこと
     * - Comparator が指定された場合はその Comparator の仕様に従って比較すること
     * 
     * @covers Peach\Util\Arrays::max
     * @covers Peach\Util\Arrays::getTop
     */
    public function testMax()
    {
        $test1 = array(5, 1, 3, 10, -10);
        $this->assertSame(10, Arrays::max($test1));
        
        $test2 = array(
            new ArraysTest_C(150, "ABC"),
            new ArraysTest_C(100, "XYZ"),
            new ArraysTest_C(150, "DEF"),
            new ArraysTest_C(125, "MNG"),
        );
        $this->assertEquals($test2[2], Arrays::max($test2));
        
        $this->assertNull(Arrays::max(array()));
        
        $comparator = new ArraysTest_Comparator();
        $result     = Arrays::max(array("three", "one", "five", "two", "four"), $comparator);
        $this->assertSame("five", $result);
    }

    /**
     * {@link Arrays::min} のテストです.
     * 以下を確認します.
     * 
     * - 与えられた配列の中で一番小さな値を返すこと
     * - Comparable を実装しているオブジェクトの場合, compareTo() メソッドを使って大小を比較すること
     * - 空の配列が指定された場合は null を返すこと
     * - Comparator が指定された場合はその Comparator の仕様に従って比較すること
     * 
     * @covers Peach\Util\Arrays::min
     * @covers Peach\Util\Arrays::getTop
     */
    public function testMin()
    {
        $test1 = array(5, 1, 3, 10, -10);
        $this->assertSame(-10, Arrays::min($test1));
        
        $test2 = array(
            new ArraysTest_C(150, "ABC"),
            new ArraysTest_C(100, "XYZ"),
            new ArraysTest_C(150, "DEF"),
            new ArraysTest_C(125, "MNG"),
        );
        $this->assertEquals($test2[1], Arrays::min($test2));
        
        $this->assertNull(Arrays::min(array()));
        
        $comparator = new ArraysTest_Comparator();
        $result     = Arrays::min(array("three", "one", "five", "two", "four"), $comparator);
        $this->assertSame("one", $result);
    }

    /**
     * pickup() をテストします. 以下を確認します.
     * 
     * - string, numeric, bool など各キーワードを解釈すること (文字の大小は問わず)
     * - その他の文字列はクラス / インタフェース名として解釈すること
     * - 第 3 引数に true を指定した場合は元の配列の添字を維持すること
     * 
     * @covers Peach\Util\Arrays::pickup
     * @covers Peach\Util\Arrays::pickupMatch
     */
    public function testPickup()
    {
        $obj1 = new ArraysTest_Object("Hoge", 100);
        $obj2 = new ArraysTest_Object("Fuga", 200);
        $arr = array(
            "A",
            1,
            null,
            array(),
            $obj1,
            "B",
            true,
            $obj2,
            2.5,
            array(1, 3, 5),
            null,
            false,
            self::$fp
        );
        
        $expected1 = array("A", "B");
        $this->assertSame($expected1, Arrays::pickup($arr, "string"));
        
        $expected2 = array(1, 2.5);
        $this->assertSame($expected2, Arrays::pickup($arr, "numeric"));
        $this->assertSame($expected2, Arrays::pickup($arr, "float"));
        
        $expected3 = array(1);
        $this->assertSame($expected3, Arrays::pickup($arr, "INT"));
        $this->assertSame($expected3, Arrays::pickup($arr, "integer"));
        
        $expected4 = array(true, false);
        $this->assertSame($expected4, Arrays::pickup($arr, "bool"));
        $this->assertSame($expected4, Arrays::pickup($arr, "Boolean"));
        
        $expected5 = array(self::$fp);
        $this->assertSame($expected5, Arrays::pickup($arr, "resource"));
        
        $expected6 = array(null, null);
        $this->assertSame($expected6, Arrays::pickup($arr, "null"));
        
        $expected7 = array(array(), array(1, 3, 5));
        $this->assertSame($expected7, Arrays::pickup($arr, "array"));
        
        $expected8 = array($obj1, $obj2);
        $this->assertSame($expected8, Arrays::pickup($arr, "object"));
        $this->assertSame($expected8, Arrays::pickup($arr, "Peach\\Util\\ArraysTest_Object"));
        
        $expected9 = array();
        $this->assertSame($expected9, Arrays::pickup($arr, "Unknown_Object"));
        
        // 第3引数を TRUE にした場合は添字を維持する
        $expectedA = array(0 => "A", 1 => "B");
        $this->assertSame($expectedA, Arrays::pickup($arr, "string", false));
        $expectedB = array(0 => "A", 5 => "B");
        $this->assertSame($expectedB, Arrays::pickup($arr, "string", true));
    }

    /**
     * sort() をテストします. 以下を確認します.
     * 
     * - 空の配列, 長さが 1 の配列の場合はそのまま返すこと
     * - 第 2 引数を指定しない場合, DefaultComparator のソート基準に従ってソートが行われること
     * - 返り値の添字が連番で初期化されること
     * 
     * @covers Peach\Util\Arrays::sort
     */
    public function testSort()
    {
        // 空の配列をソートした場合は空の配列を返す
        $test1 = Arrays::sort(array());
        $this->assertSame(array(), $test1);
        
        // 要素数が 1 の配列はそのままとなる
        $test2 = Arrays::sort(array(5));
        $this->assertSame(array(5), $test2);
        
        // 要素数が 2 の場合のテスト
        $test3 = Arrays::sort(array(20, 10));
        $this->assertSame(array(10, 20), $test3);
        
        // 通常のテストデータでソート
        $expected = array_values(self::getSampleArray());
        
        $subject4 = self::getSampleArray();
        $test4    = Arrays::sort($subject4);
        $this->assertSame($expected, $test4);
        
        $subject5 = self::getSampleReverseArray();
        $test5    = Arrays::sort($subject5);
        $this->assertSame($expected, $test5);
        
        $subject6 = self::getSampleShuffleArray();
        $test6    = Arrays::sort($subject6);
        $this->assertSame($expected, $test6);
        
        $expected2 = range(1, 200);
        $largeArr  = Arrays::concat(array_reverse(range(2, 200, 2)), range(1, 199, 2));
        $this->assertSame($expected2, Arrays::sort($largeArr));
    }
    
    /**
     * asort() をテストします. 以下を確認します.
     * 
     * - 空の配列, 長さが 1 の配列の場合はそのまま返すこと
     * - 第 2 引数を指定しない場合, DefaultComparator のソート基準に従ってソートが行われること
     * - 返り値の添字が維持されること
     * 
     * @covers Peach\Util\Arrays::asort
     */
    public function testAsort()
    {
        // 空の配列をソートした場合は空の配列を返す
        $test1 = Arrays::asort(array());
        $this->assertSame(array(), $test1);
        
        // 要素数が 1 の配列はそのままとなる
        $test2 = Arrays::asort(array("foo" => "asdf"));
        $this->assertSame(array("foo" => "asdf"), $test2);
        
        // 要素数が 2 の場合のテスト
        $test3 = Arrays::asort(array("hoge" => 5, "hogu" => 3));
        $this->assertSame(array("hogu" => 3, "hoge" => 5), $test3);
        
        // 通常のテストデータでソート
        $expected = self::getSampleArray();
        
        $subject4 = self::getSampleArray();
        $test4    = Arrays::asort($subject4);
        $this->assertSame($expected, $test4);
        
        $subject5 = self::getSampleReverseArray();
        $test5    = Arrays::asort($subject5);
        $this->assertSame($expected, $test5);
        
        $subject6 = self::getSampleShuffleArray();
        $test6    = Arrays::asort($subject6);
        $this->assertSame($expected, $test6);
        
        $ranges    = range(1, 200);
        $expected2 = array_combine($ranges, $ranges);
        $reversed  = array_reverse($ranges);
        $subject7  = array_combine($reversed, $reversed);
        $test7     = Arrays::asort($subject7);
        $this->assertSame($expected2, $test7);
    }
    
    /**
     * 引数に指定された配列と値が連結されて 1 つの配列として返されることを確認します.
     * 
     * @covers Peach\Util\Arrays::concat
     */
    public function testConcat()
    {
        $test1 = Arrays::concat();
        $this->assertSame(array(), $test1);
        
        $test2 = Arrays::concat(100, 200, 400, 800);
        $this->assertSame(array(100, 200, 400, 800), $test2);
        
        $arr1 = array(1, 2, 3);
        $arr2 = array(5, 7);
        $test3     = Arrays::concat($arr1, "X", $arr2, "Y");
        $expected3 = array(1, 2, 3, "X", 5, 7, "Y");
        $this->assertSame($expected3, $test3);
        
        $subarr1   = array(10, 20);
        $subarr2   = array(30, 50, 70);
        $arr3      = array($subarr1, $subarr2);
        $test4     = Arrays::concat($arr1, $arr3);
        $expected4 = array(1, 2, 3, $subarr1, $subarr2);
        $this->assertSame($expected4, $test4);
    }

    /**
     * unique() をテストします. 以下を確認します.
     * 
     * - 2 度目以降に出現した値が取り除かれた配列を返すこと
     * - 配列のキーが維持されていること
     * 
     * @covers Peach\Util\Arrays::unique
     */
    public function testUnique()
    {
        $test     = array(1, 3, "2", 7, 5, 0, 8, "3", 2, 9, 3, 6, "5", 4);
        $expected = array(0 => 1, 3 => 7, 4 => 5, 5 => 0, 6 => 8, 7 => "3", 8 => 2, 9 => 9, 11 => 6, 13 => 4);
        $result   = Arrays::unique($test);
        $this->assertSame($expected, $result);
    }
    
    /**
     * @return array
     */
    private static function getSampleArray()
    {
        static $arr = null;
        if (!isset($arr)) {
            $arr = array(
                "the"   => 1,
                "quick" => 2,
                "brown" => 3,
                "fox"   => 4,
                "jumps" => 8,
                "over"  => 12,
                "lazy"  => 16,
                "dogs"  => 20
            );
        }
        return $arr;
    }
    
    /**
     * @return array
     */
    private static function getSampleReverseArray()
    {
        $arr = self::getSampleArray();
        return array_reverse($arr);
    }
    
    /**
     * @return array
     */
    private static function getSampleShuffleArray()
    {
        $arr  = self::getSampleArray();
        $keys = array_keys($arr);
        $vals = array_values($arr);
        $nums = range(0, 7);
        $test = array();
        shuffle($nums);
        foreach ($nums as $i) {
            $key = $keys[$i];
            $val = $vals[$i];
            $test[$key] = $val;
        }
        return $test;
    }
}

class ArraysTest_Object implements Comparable
{
    private $name;
    private $value;
    
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    
    public function compareTo($subject)
    {
        $cmp = strcmp($this->name, $subject->name);
        return $cmp ? $cmp : $this->value - $subject->value;
    }
    
    public function __toString()
    {
        return $this->name . ":" . $this->value;
    }
}

class ArraysTest_C implements Comparable
{
    private $var1;
    private $var2;
    
    public function __construct($var1, $var2)
    {
        $this->var1 = $var1;
        $this->var2 = $var2;
    }
    
    public function compareTo($subject)
    {
        if ($subject instanceof ArraysTest_C) {
            $comp = $this->var1 - $subject->var1;
            return $comp === 0 ? strcmp($this->var2, $subject->var2) : $comp;
        } else {
            throw new Exception();
        }
    }
}

class ArraysTest_Comparator implements Comparator
{
    /**
     * @param  string $var1
     * @param  string $var2
     * @return int
     */
    public function compare($var1, $var2)
    {
        return $this->convert($var1) - $this->convert($var2);
    }
    
    /**
     * @param  string $var
     * @return int
     */
    private function convert($var)
    {
        static $names = array(
            "one"   => 1,
            "two"   => 2,
            "three" => 3,
            "four"  => 4,
            "five"  => 5,
        );
        return array_key_exists($var, $names) ? $names[$var] : 0;
    }
}
