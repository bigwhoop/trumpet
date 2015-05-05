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

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinterAbstract as Printer;

class NodeVisitor extends NodeVisitorAbstract
{
    /** @var ParserResult */
    private $result;

    /** @var PHPClass|null */
    private $currentClass;

    /** @var Printer */
    private $printer;

    /**
     * @param Printer      $printer
     * @param ParserResult $result
     */
    public function __construct(Printer $printer, ParserResult $result = null)
    {
        if (!$result) {
            $result = new ParserResult();
        }

        $this->printer = $printer;
        $this->result = $result;
    }

    /**
     * @return ParserResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $fullyQualifiedName = $node->namespacedName->toString();
            $this->currentClass = new PHPClass($fullyQualifiedName, [], $this->getSource($node));
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentClass->addMethod(new PHPMethod($node->name, $this->getSource($node)));
        } elseif ($node instanceof Node\Stmt\Function_) {
            $fullyQualifiedName = $node->namespacedName->toString();
            $this->result->addFunction(new PHPFunction($fullyQualifiedName, $this->getSource($node)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->result->addClass($this->currentClass);
        }
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    private function getSource(Node $node)
    {
        return $this->printer->prettyPrint([$node]);
    }
}
