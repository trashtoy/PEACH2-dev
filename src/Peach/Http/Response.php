<?php

/*
 * Copyright (c) 2015 @trashtoy
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
 * @since  2.2.0
 */
namespace Peach\Http;

use Peach\Http\Header\NoField;
use Peach\Http\Header\Status;
use Peach\Util\ArrayMap;

class Response
{
    /**
     * HeaderField 型オブジェクトを格納する ArrayMap です.
     * @var ArrayMap
     */
    private $headerList;
    
    /**
     * HTTP レスポンスのメッセージボディ部分を表す Body オブジェクトです.
     * @var Body
     */
    private $body;
    
    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->headerList = new ArrayMap();
        $this->body       = null;
    }
    
    /**
     * 指定された名前のヘッダーを取得します.
     * 存在しない場合は null を返します.
     * 
     * @param  string $name ヘッダー名
     * @return HeaderField   指定されたヘッダーに該当する HeaderField オブジェクト
     */
    public function getHeader($name)
    {
        $header = $this->headerList->get(strtolower($name));
        return ($header instanceof HeaderField) ? $header : NoField::getInstance();
    }
    
    /**
     * この Response が持つヘッダーの一覧を取得します.
     * 返り値の配列に対する操作はこのオブジェクトに影響しません.
     * 
     * @return HeaderField[] この Response にセットされている HeaderField の一覧
     */
    public function getHeaderList()
    {
        return $this->headerList->asArray();
    }
    
    /**
     * 指定されたヘッダーをこの Response に設定します.
     * 
     * @param HeaderField $item
     */
    public function setHeader(HeaderField $item)
    {
        $name = strtolower($item->getName());
        $this->headerList->put($name, $item);
    }
    
    /**
     * 指定された名前の HeaderField が存在するかどうか調べます.
     * @param  string $name ヘッダー名
     * @return bool         指定された名前の HeaderField が存在する場合のみ true
     */
    public function hasHeader($name)
    {
        return $this->headerList->containsKey(strtolower($name));
    }
    
    /**
     * この Response が malformed (奇形) かどうかを判断します.
     * 
     * @todo 実装する
     * @return bool このオブジェクトが表現する Response が malformed (奇形) と判定される場合に true, それ以外は false
     */
    public function isMalformed()
    {
        if (!$this->validateStatusHeader()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * この Response の :status 擬似ヘッダーが適切かどうかを調べます.
     * 
     * @return bool
     */
    private function validateStatusHeader()
    {
        if (!$this->hasHeader(":status")) {
            return false;
        }
        $status = $this->getHeader(":status");
        return ($status instanceof Status);
    }
    
    /**
     * 
     * @param Body $body
     */
    public function setBody(Body $body)
    {
        $this->body = $body;
    }
    
    /**
     * 
     * @return Body
     */
    public function getBody()
    {
        return $this->body;
    }
}
