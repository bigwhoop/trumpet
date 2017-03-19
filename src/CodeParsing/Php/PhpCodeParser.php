<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

use Bigwhoop\Trumpet\CodeParsing\ParserException;
use PhpParser\Error;
use PhpParser\ParserAbstract as Parser;
use PhpParser\NodeTraverserInterface as NodeTraverser;
use PhpParser\PrettyPrinterAbstract as Printer;

final class PhpCodeParser
{
    /** @var Parser */
    private $parser;

    /** @var NodeTraverser */
    private $nodeTraverser;

    /** @var Printer */
    private $printer;

    public function __construct(Parser $parser, NodeTraverser $nodeTraverser, Printer $printer)
    {
        $this->parser = $parser;
        $this->nodeTraverser = $nodeTraverser;
        $this->printer = $printer;
    }

    public function parse(string $code): ParserResult
    {
        try {
            $stmts = $this->parser->parse($code);

            $visitor = $this->createVisitor();
            $this->nodeTraverser->addVisitor($visitor);

            $this->nodeTraverser->traverse($stmts);

            return $visitor->getResult();
        } catch (Error $e) {
            throw new ParserException("Failed to parse PHP file: ".$e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function createVisitor(): NodeVisitor
    {
        return new NodeVisitor($this->printer);
    }
}
