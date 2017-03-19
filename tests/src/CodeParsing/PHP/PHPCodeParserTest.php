<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\CodeParsing\PHP;

use Bigwhoop\Trumpet\CodeParsing\Php\PhpClass;
use Bigwhoop\Trumpet\CodeParsing\Php\PhpCodeParser;
use Bigwhoop\Trumpet\CodeParsing\Php\PhpFunction;
use Bigwhoop\Trumpet\CodeParsing\Php\PhpMethod;
use Bigwhoop\Trumpet\Tests\TestCase;

class PHPCodeParserTest extends TestCase
{
    /**
     * @Inject
     *
     * @var PhpCodeParser
     */
    private $parser;

    public function testClass()
    {
        $result = $this->parser->parse('<?php declare(strict_types=1); class Foo { protected $var = 12.54; }');

        $this->assertTrue($result->hasClass('Foo'));
        $class = $result->getClass('Foo');
        $this->assertInstanceOf(PhpClass::class, $class);

        $expectedSource = <<<'CODE'
class Foo
{
    protected $var = 12.54;
}
CODE;
        $this->assertSourceCode($expectedSource, $class->getSource());
    }

    public function testClassWithNamespace()
    {
        $result = $this->parser->parse('<?php declare(strict_types=1); namespace Foo\Bar; class Foo { protected $var = 12.54; }');

        $this->assertTrue($result->hasClass('Foo\Bar\Foo'));
        $class = $result->getClass('Foo\Bar\Foo');
        $this->assertInstanceOf(PhpClass::class, $class);

        $expectedSource = <<<'CODE'
class Foo
{
    protected $var = 12.54;
}
CODE;
        $this->assertSourceCode($expectedSource, $class->getSource());
    }

    public function testClassMethods()
    {
        $code = <<<'CODE'
<?php declare(strict_types=1);
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
        $this->assertInstanceOf(PhpClass::class, $class);

        $this->assertCount(3, $class->getMethods());

        $expectedSource = <<<'CODE'
public function __construct($value)
{
    $this->value = $value;
}
CODE;
        $this->assertClassMethod($class, '__construct', $expectedSource);

        $expectedSource = <<<'CODE'
public function getValue()
{
    return $this->value;
}
CODE;
        $this->assertClassMethod($class, 'getValue', $expectedSource);

        $expectedSource = <<<'CODE'
public function add(\Number $n)
{
    return new \Number($this->value + $n->getValue());
}
CODE;
        $this->assertClassMethod($class, 'add', $expectedSource);
    }

    public function testFunctions()
    {
        $result = $this->parser->parse('<?php declare(strict_types=1); function add($a, $b) { return $a + $b; } function sub($a, $b) { return $a - $b; }');

        $this->assertCount(2, $result->getFunctions());

        $this->assertTrue($result->hasFunction('add'));
        $func = $result->getFunction('add');
        $this->assertInstanceOf(PhpFunction::class, $func);

        $expectedSource = <<<'CODE'
function add($a, $b)
{
    return $a + $b;
}
CODE;
        $this->assertSourceCode($expectedSource, $func->getSource());

        $this->assertTrue($result->hasFunction('sub'));
        $func = $result->getFunction('sub');
        $this->assertInstanceOf(PhpFunction::class, $func);

        $expectedSource = <<<'CODE'
function sub($a, $b)
{
    return $a - $b;
}
CODE;
        $this->assertSourceCode($expectedSource, $func->getSource());
    }

    private function assertClassMethod(PhpClass $class, string $methodName, string $expectedSource)
    {
        $this->assertTrue($class->hasMethod($methodName));
        $this->assertInstanceOf(PhpMethod::class, $class->getMethod($methodName));
        $this->assertSourceCode($expectedSource, $class->getMethod($methodName)->getSource());
    }
}
