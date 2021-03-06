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
namespace Peach\Markup;
use Peach\Util\Values;

/**
 * HTML や XML などを出力する際に使う, デフォルトの Builder です.
 * このクラスは, 以下の条件をカスタマイズすることが出来ます.
 * 
 * - インデントの文字列 (半角スペース, タブ文字)
 * - 改行コードの種類 (LF, CRLF, CR)
 * - 空要素タグや boolean 属性の出力方法 (SGML, XML)
 */
class DefaultBuilder extends Builder
{
    /**
     * DefaultContext のコンストラクタに渡される Indent オブジェクトです.
     * @var Indent
     */
    private $indent;
    
    /**
     * DefaultContext のコンストラクタに渡される Renderer オブジェクトです.
     * @var Renderer
     */
    private $renderer;
    
    /**
     * DefaultContext のコンストラクタに渡される BreakControl オブジェクトです.
     * @var BreakControl
     */
    private $breakControl;
    
    /**
     * デフォルトの設定を持つ DefaultBuilder インスタンスを生成します.
     */
    public function __construct()
    {
        $this->indent       = null;
        $this->renderer     = null;
        $this->breakControl = null;
    }
    
    /**
     * この Builder にセットされている Indent オブジェクトを返します.
     * もしも Indent オブジェクトがセットされていない場合は null を返します.
     * 
     * @return Indent Indent オブジェクト (セットされていない場合は null)
     */
    public function getIndent()
    {
        return $this->indent;
    }
    
    /**
     * この Builder に指定された Indent オブジェクトをセットします.
     * null を指定した場合は設定を解除します.
     * 
     * @param  Indent $indent セットする Indent オブジェクト
     */
    public function setIndent(Indent $indent = null)
    {
        $this->indent = $indent;
    }
    
    /**
     * この Builder にセットされている Renderer オブジェクトを返します.
     * もしも Renderer オブジェクトがセットされていない場合は null を返します.
     * 
     * @return Renderer Renderer オブジェクト (セットされていない場合は null)
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    /**
     * この Builder に指定された Renderer オブジェクトをセットします.
     * 引数によって以下のように動作します.
     * 
     * - Renderer オブジェクトを指定した場合: そのオブジェクトをセットします
     * - 文字列 "xml" または "xhtml" を指定した場合 (大小問わず) : {@link XmlRenderer} オブジェクトをセットします
     * - 文字列 "sgml" または "html" を指定した場合 (大小問わず) : {@link SgmlRenderer} オブジェクトをセットします
     * - null を指定した場合 : 現在セットされている Renderer を解除します
     * - それ以外: InvalidArgumentException をスローします
     * 
     * @param  Renderer|string $renderer セットする Renderer または対応する文字列
     * @throws \InvalidArgumentException 不正な引数をセットした場合
     */
    public function setRenderer($renderer = null)
    {
        $this->renderer = $this->initRenderer($renderer);
    }
    
    /**
     * 指定された引数で Renderer オブジェクトを初期化します.
     * 
     * @see    Renderer::setRenderer()
     * @param  Renderer|string $var
     * @return Renderer
     * @throws \InvalidArgumentException
     */
    private function initRenderer($var)
    {
        if ($var instanceof Renderer) {
            return $var;
        }
        if ($var === null) {
            return null;
        }
        
        $type     = strtolower(Values::stringValue($var));
        $xmlList  = array("xml", "xhtml");
        if (in_array($type, $xmlList)) {
            return XmlRenderer::getInstance();
        }
        $sgmlList = array("sgml", "html");
        if (in_array($type, $sgmlList)) {
            return SgmlRenderer::getInstance();
        }
        
        throw new \InvalidArgumentException("Invalid type name: {$type}.");
    }
    
    /**
     * この Builder にセットされている BreakControl オブジェクトを返します.
     * もしも BreakControl オブジェクトがセットされていない場合は null を返します.
     * 
     * @return BreakControl BreakControl オブジェクト (セットされていない場合は null)
     */
    public function getBreakControl()
    {
        return $this->breakControl;
    }
    
    /**
     * この Builder に指定された BreakControl をセットします.
     * null を指定した場合は設定を解除します.
     * 
     * @param  BreakControl $breakControl セットする BreakControl
     */
    public function setBreakControl(BreakControl $breakControl = null)
    {
        $this->breakControl = $breakControl;
    }
    
    /**
     * この Builder にセットされている Indent, Renderer, BreakControl
     * を使って新しい DefaultContext を生成します.
     * 
     * @return DefaultContext 新しい DefaultContext
     */
    protected function createContext()
    {
        $indent        = isset($this->indent)        ? clone($this->indent) : new Indent();
        $renderer      = isset($this->renderer)      ? $this->renderer      : XmlRenderer::getInstance();
        $breakControl  = isset($this->breakControl)  ? $this->breakControl  : DefaultBreakControl::getInstance();
        return new DefaultContext($renderer, $indent, $breakControl);
    }
}
