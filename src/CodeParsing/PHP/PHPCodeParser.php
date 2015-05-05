<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\CodeParsing\PHP;

use Bigwhoop\Trumpet\CodeParsing\ParserException;
use PhpParser\Error;
use PhpParser\ParserAbstract as Parser;
use PhpParser\NodeTraverserInterface as NodeTraverser;
use PhpParser\PrettyPrinterAbstract as Printer;

class PHPCodeParser
{
    /** @var Parser */
    private $parser;

    /** @var NodeTraverser */
    private $nodeTraverser;

    /** @var Printer */
    private $printer;

    /**
     * @param Parser        $parser
     * @param NodeTraverser $nodeTraverser
     * @param Printer       $printer
     */
    public function __construct(Parser $parser, NodeTraverser $nodeTraverser, Printer $printer)
    {
        $this->parser = $parser;
        $this->nodeTraverser = $nodeTraverser;
        $this->printer = $printer;
    }

    /**
     * @param string $code
     *
     * @return ParserResult
     *
     * @throws ParserException
     */
    public function parse($code)
    {
        try {
            $stmts = $this->parser->parse($code);

            $visitor = $this->createVisitor();
            $this->nodeTraverser->addVisitor($visitor);

            $this->nodeTraverser->traverse($stmts);

            return $visitor->getResult();
        } catch (Error $e) {
            throw new ParserException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return NodeVisitor
     */
    private function createVisitor()
    {
        return new NodeVisitor($this->printer);
    }
}
