<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinterAbstract as Printer;

final class NodeVisitor extends NodeVisitorAbstract
{
    /** @var ParserResult */
    private $result;

    /** @var PhpClass|null */
    private $currentClass;

    /** @var Printer */
    private $printer;

    /**
     * @param Printer      $printer
     * @param ParserResult $result
     */
    public function __construct(Printer $printer, ParserResult $result = null)
    {
        $this->printer = $printer;
        $this->result = $result ?: new ParserResult();
    }

    public function getResult(): ParserResult
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
            $this->currentClass = new PhpClass($fullyQualifiedName, [], $this->getSource($node));
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentClass->addMethod(new PhpMethod($node->name, $node->isStatic(), $this->getSource($node)));
        } elseif ($node instanceof Node\Stmt\Function_) {
            $fullyQualifiedName = $node->namespacedName->toString();
            $this->result->addFunction(new PhpFunction($fullyQualifiedName, $this->getSource($node)));
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

    private function getSource(Node $node): string
    {
        return $this->printer->prettyPrint([$node]);
    }
}
