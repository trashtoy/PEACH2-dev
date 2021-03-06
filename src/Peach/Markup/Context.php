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

/**
 * 各ノードを変換する処理を担当するクラスです.
 * このクラスは Visitor パターンにより設計されています (Visitor クラスに相当します).
 * {@link Builder} クラスと連携して以下のように動作します.
 * 
 * 1. エンドユーザーが Builder オブジェクトの {@link Builder::build()} メソッドを実行します
 * 2. build() メソッドの内部で新しい Context オブジェクトが生成されます
 * 3. Context オブジェクトの {@link Context::handle()} メソッドが呼び出され, build() の引数に指定されたノードを変換します
 * 4. 変換結果を {@link Context::getResult()} から取り出し, build() メソッドの返り値として返します
 */
abstract class Context
{
    /**
     * 指定されたオブジェクトを処理します.
     * オブジェクトの種類に応じて, このクラスの具象クラスで定義された各 handle メソッドに処理が割り当てられます.
     * Visitor パターンの visit メソッドに相当します.
     * 
     * @param Component $c 処理対象の Component
     */
    public final function handle(Component $c)
    {
        $c->accept($this);
    }
    
    /**
     * 処理結果を取得します. まだ handle() が実行されていない場合は NULL を返します.
     * 
     * @return mixed 処理結果
     */
    public abstract function getResult();
    
    /**
     * コンテナ要素を処理します.
     * @param ContainerElement $node 処理対象のコンテナ要素
     */
    public abstract function handleContainerElement(ContainerElement $node);
    
    /**
     * 空要素を処理します.
     * @param EmptyElement $node 処理対象の空要素
     */
    public abstract function handleEmptyElement(EmptyElement $node);
    
    /**
     * テキストノードを処理します.
     * @param Text $node 処理対象のテキスト
     */
    public abstract function handleText(Text $node);
    
    /**
     * 整形済テキストを処理します.
     * @param Code $node 処理対象の整形済テキスト
     */
    public abstract function handleCode(Code $node);
    
    /**
     * コメントノードを処理します.
     * @param Comment $node 処理対象のコメント
     */
    public abstract function handleComment(Comment $node);
    
    /**
     * NodeList を処理します.
     * @param NodeList $nodeList 処理対象の NodeList
     */
    public abstract function handleNodeList(NodeList $nodeList);
    
    /**
     * None を処理します.
     * @param None $none 処理対象の None オブジェクト
     */
    public abstract function handleNone(None $none);
}
