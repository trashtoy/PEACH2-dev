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
 * @since  2.1.0
 * @ignore
 */
namespace Peach\DF\JsonCodec;

/**
 * 以下の BNF ルールをあらわす Expression です.
 * 
 * <pre>
 * JSON-text = ws value ws
 * </pre>
 * 
 * @ignore
 */
class Root implements Expression
{
    private $result;
    
    public function __construct()
    {
        $this->result = null;
    }
    
    /**
     * 
     * @param Context $context
     * @throws DecodeException
     */
    public function handle(Context $context)
    {
        $ws = WS::getInstance();
        $ws->handle($context);
        $value = new Value();
        $value->handle($context);
        $ws->handle($context);
        
        if ($context->hasNext()) {
            $current = $context->current();
            $context->throwException("Unexpected character('{$current}')");
        }
        
        $this->result = $value->getResult();
    }
    
    /**
     * 
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }
}
