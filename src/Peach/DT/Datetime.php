<?php
/*
 * Copyright (c) 2014 @trashtoy
 * https://github.com/trashtoy/
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * PHP class file.
 * @auhtor trashtoy
 * @since  2.0.0
 */
namespace Peach\DT;
use Peach\Util\Map;
use Peach\Util\ArrayMap;

/**
 * DATETIME 型の時間オブジェクトです.
 * このクラスは年・月・日・時・分のフィールドをサポートします.
 */
class Datetime extends Date
{
    /**
     * 時を表す整数(0～23)です.
     * @var int
     * @ignore
     */
    protected $hour = 0;
    
    /**
     * 分を表す整数(0～59)です.
     * @var int
     * @ignore
     */
    protected $minute = 0;
    
    /**
     * 現在時刻の Datetime オブジェクトを返します.
     * 
     * @param  Clock $clock 現在時刻を取得するための Clock オブジェクト
     * @return Datetime     現在時刻をあらわす Datetime
     */
    public static function now(Clock $clock = null)
    {
        if ($clock === null) {
            return self::now(DefaultClock::getInstance());
        }
        
        return $clock->getTimestamp()->toDatetime();
    }
    
    /**
     * 指定されたテキストを解析して Datetime オブジェクトに変換します.
     * $format が指定されていない場合は {@link W3cDatetimeFormat::getInstance}
     * を使って解析を行います.
     * ("YYYY-MM-DD hh:mm" 形式の文字列を受理します.
     * 日付と時刻のセパレータは, 数字以外の ASCII 1 文字であれば何でも構いません.)
     * 
     * @param  string   $text   変換対象の文字列
     * @param  Format   $format 変換に使用するフォーマット
     * @return Datetime         変換結果
     */
    public static function parse($text, Format $format = null)
    {
        if (!isset($format)) {
            $format = W3cDatetimeFormat::getInstance();
        }
        return $format->parseDatetime($text);
    }
    
    /**
     * 与えられた時刻を表現する Datetime オブジェクトを構築します.
     *
     * @param int $year  年
     * @param int $month 月
     * @param int $date  日
     * @param int $hour  時
     * @param int $min   分
     */
    public function __construct($year, $month, $date, $hour, $min)
    {
        $fields = new ArrayMap();
        $fields->put(self::$YEAR,   intval($year));
        $fields->put(self::$MONTH,  intval($month));
        $fields->put(self::$DATE,   intval($date));
        $fields->put(self::$HOUR,   intval($hour));
        $fields->put(self::$MINUTE, intval($min));
        $this->init($fields);
    }
    
    /**
     * このオブジェクトの型 {@link Time::TYPE_DATETIME} を返します.
     * @return int Time::TYPE_DATETIME
     */
    public function getType()
    {
        return self::TYPE_DATETIME;
    }
    
    /**
     * @ignore
     */
    protected function init(Map $fields)
    {
        parent::init($fields);
        $this->hour   = $fields->get(self::$HOUR);
        $this->minute = $fields->get(self::$MINUTE);
    }
    
    /**
     * 時刻の不整合を調整します.
     * @ignore
     */
    protected function adjust(Map $fields)
    {
        parent::adjust($fields);
        $hourAd = $this->getHourAdjuster();
        $minAd  = $this->getMinuteAdjuster();
        $hour   = $fields->get(self::$HOUR);
        $min    = $fields->get(self::$MINUTE);
        
        if ($hour < 0) {
            $hourAd->moveDown($fields);
        } else if (23 < $hour) {
            $hourAd->moveUp($fields);
        } else if ($min < 0) {
            $minAd->moveDown($fields);
        } else if (59 < $min) {
            $minAd->moveUp($fields);
        } else {
            return;
        }
        
        $this->adjust($fields);
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @return Datetime
     * @see AbstractTime::newInstance
     * @ignore
     */
    protected function newInstance(Map $fields)
    {
        $year  = $fields->get(self::$YEAR);
        $month = $fields->get(self::$MONTH);
        $date  = $fields->get(self::$DATE);
        $hour  = $fields->get(self::$HOUR);
        $min   = $fields->get(self::$MINUTE);
        return new self($year, $month, $date, $hour, $min);
    }
    
    /**
     * この時間と指定された時間を比較します.
     * 
     * この型の時間フィールドと引数の型の時間フィールドのうち,
     * 共通しているフィールド同士を比較します.
     * 
     * 引数がこのクラスを継承したオブジェクトではない場合,
     * 引数のオブジェクトに対して get("year"), get("month"), get("date"), get("hour"), get("minute")
     * を呼び出した結果を比較対象のフィールドとします.
     * 
     * @param  Time 比較対象の時間
     * @return int  この時間のほうが過去の場合は負の値, 未来の場合は正の値, それ以外は 0
     * @ignore
     */
    protected function compareFields(Time $time)
    {
        $c = parent::compareFields($time);
        if ($c !== 0) {
            return $c;
        }
        
        $className = __CLASS__;
        if ($time instanceof $className) {
            if ($this->hour   !== $time->hour) {
                return $this->hour   - $time->hour;
            }
            if ($this->minute !== $time->minute) {
                return $this->minute - $time->minute;
            }
            return 0;
        }
        else {
            $h = $time->get("hour");
            $m = $time->get("minute");
            if ($this->hour   !== $h) {
                return (isset($h) ? $this->hour   - $h : 0);
            }
            if ($this->minute !== $m) {
                return (isset($m) ? $this->minute - $m : 0);
            }
            return 0;
        }
    }
    
    /**
     * @ignore
     */
    protected function handleFormat(Format $format)
    {
        return $format->formatDatetime($this);
    }
    
    /**
     * このオブジェクトの時刻部分の文字列を "hh:mm" 形式で返します.
     * 
     * @return string "hh:mm" 形式の文字列
     */
    public function formatTime()
    {
        $hour = str_pad($this->hour,   2, '0', STR_PAD_LEFT);
        $min  = str_pad($this->minute, 2, '0', STR_PAD_LEFT);
        return $hour . ":" . $min;
    }
    
    /**
     * このオブジェクトの文字列表現です.
     * "YYYY-MM-DD hh:mm" 形式の文字列を返します.
     * 
     * @return string W3CDTF に則った文字列表現
     */
    public function __toString()
    {
        $date = parent::__toString();
        return $date . ' ' . $this->formatTime();
    }
    
    /**
     * このオブジェクトを Datetime 型にキャストします.
     * 返り値はこのオブジェクトのクローンです.
     *
     * @return Datetime このオブジェクトのクローン
     */
    public function toDatetime()
    {
        return new self($this->year, $this->month, $this->date, $this->hour, $this->minute);
    }
    
    /**
     * このオブジェクトを Timestamp 型にキャストします.
     * この時刻の 0 秒を表す Timestamp オブジェクトを返します.
     *
     * @return Timestamp このオブジェクトの timestamp 表現
     */
    public function toTimestamp()
    {
        return new Timestamp($this->year, $this->month, $this->date, $this->hour, $this->minute, 0);
    }
    
    /**
     * 「時」フィールドを調整する Adjuster です
     * @return FieldAdjuster
     * @codeCoverageIgnore
     */
    private function getHourAdjuster()
    {
        static $adjuster = null;
        if ($adjuster === null) {
            $adjuster = new FieldAdjuster(self::$HOUR, self::$DATE, 0, 23);
        }
        return $adjuster;
    }
    
    /**
     * 「分」フィールドを調整する Adjuster です
     * @return FieldAdjuster
     * @codeCoverageIgnore
     */
    private function getMinuteAdjuster()
    {
        static $adjuster = null;
        if ($adjuster === null) {
            $adjuster = new FieldAdjuster(self::$MINUTE, self::$HOUR, 0, 59);
        }
        return $adjuster;
    }
}
