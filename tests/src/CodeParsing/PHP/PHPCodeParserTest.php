<?php

namespace Bigwhoop\Trumpet\Tests\CodeParsing\PHP;

use Bigwhoop\Trumpet\CodeParsing\PHP\PHPClass;
use Bigwhoop\Trumpet\CodeParsing\PHP\PHPCodeParser;
use Bigwhoop\Trumpet\CodeParsing\PHP\PHPFunction;
use Bigwhoop\Trumpet\CodeParsing\PHP\PHPMethod;
use Bigwhoop\Trumpet\Tests\TestCase;

class PHPCodeParserTest extends TestCase
{
    /**
     * @Inject
     *
     * @var PHPCodeParser
     */
    private $parser;

    public function testClass()
    {
        $result = $this->parser->parse('<?php class Foo { protected $var = 12.54; }');

        $this->assertTrue($result->hasClass('Foo'));
        $class = $result->getClass('Foo');
        $this->assertInstanceOf(PHPClass::class, $class);

        $expectedSource = <<<'CODE'
class Foo
{
    protected $var = 12.54;
}
CODE;
        $this->_testSourceCode($expectedSource, $class->getSource());
    }

    public function testClassWithNamespace()
    {
        $result = $this->parser->parse('<?php namespace Foo\Bar; class Foo { protected $var = 12.54; }');

        $this->assertTrue($result->hasClass('Foo\Bar\Foo'));
        $class = $result->getClass('Foo\Bar\Foo');
        $this->assertInstanceOf(PHPClass::class, $class);

        $expectedSource = <<<'CODE'
class Foo
{
    protected $var = 12.54;
}
CODE;
        $this->_testSourceCode($expectedSource, $class->getSource());
    }

    public function testClassMethods()
    {
        $code = <<<'CODE'
<?php
namespace My\Library;

class Number
{
  private $value = 0;

  public function __construct($value) { $this->value = $value; }

  public function getValue() { return $this->value; }

  public function add(\Number $n)
  {
      return new \Number($this->value + $n->getValue());
  }
}

$n1 = new \Number(5);
$n2 = $n1->add(new \Number(3));
echo $n2->getValue();
CODE;

        $result = $this->parser->parse($code);

        $this->assertTrue($result->hasClass('My\Library\Number'));
        $class = $result->getClass('My\Library\Number');
        $this->assertInstanceOf(PHPClass::class, $class);

        $this->assertCount(3, $class->getMethods());

        $expectedSource = <<<'CODE'
public function __construct($value)
{
    $this->value = $value;
}
CODE;
        $this->_testClassMethod($class, '__construct', $expectedSource);

        $expectedSource = <<<'CODE'
public function getValue()
{
    return $this->value;
}
CODE;
        $this->_testClassMethod($class, 'getValue', $expectedSource);

        $expectedSource = <<<'CODE'
public function add(\Number $n)
{
    return new \Number($this->value + $n->getValue());
}
CODE;
        $this->_testClassMethod($class, 'add', $expectedSource);
    }

    public function testFunctions()
    {
        $result = $this->parser->parse('<?php function add($a, $b) { return $a + $b; } function sub($a, $b) { return $a - $b; }');

        $this->assertCount(2, $result->getFunctions());

        $this->assertTrue($result->hasFunction('add'));
        $func = $result->getFunction('add');
        $this->assertInstanceOf(PHPFunction::class, $func);

        $expectedSource = <<<'CODE'
function add($a, $b)
{
    return $a + $b;
}
CODE;
        $this->_testSourceCode($expectedSource, $func->getSource());

        $this->assertTrue($result->hasFunction('sub'));
        $func = $result->getFunction('sub');
        $this->assertInstanceOf(PHPFunction::class, $func);

        $expectedSource = <<<'CODE'
function sub($a, $b)
{
    return $a - $b;
}
CODE;
        $this->_testSourceCode($expectedSource, $func->getSource());
    }

    /**
     * @param PHPClass $class
     * @param string   $methodName
     * @param string   $expectedSource
     */
    private function _testClassMethod(PHPClass $class, $methodName, $expectedSource)
    {
        $this->assertTrue($class->hasMethod($methodName));
        $this->assertInstanceOf(PHPMethod::class, $class->getMethod($methodName));
        $this->_testSourceCode($expectedSource, $class->getMethod($methodName)->getSource());
    }

    /**
     * @param string $expected
     * @param string $actual
     */
    private function _testSourceCode($expected, $actual)
    {
        $this->assertEquals(str_replace("\r", '', $expected), str_replace("\r", '', $actual));
    }
}
