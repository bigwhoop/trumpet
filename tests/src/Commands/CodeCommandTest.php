<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Commands;

use Bigwhoop\Trumpet\Commands\CodeCommand;
use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Tests\TestCase;

class CodeCommandTest extends TestCase
{
    /**
     * @Inject
     *
     * @var CodeCommand
     */
    private $cmd;

    /**
     * @Inject
     *
     * @var CommandExecutionContext
     */
    private $ctx;

    /**
     * !code File.php.
     */
    public function testFile()
    {
        $expected = $this->indentText(file_get_contents($this->ctx->getWorkingDirectory().'/Calc.php'));
        $actual = $this->cmd->execute(new CommandParams('Calc.php'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !code File.php class Name\Space\ClassName.
     */
    public function testClass()
    {
        $expected = <<<'CODE'
class Calc
{
    /**
     * @return Calc
     */
    public static function create()
    {
        return new self();
    }
    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function add(int $a, int $b) : int
    {
        return $a + $b;
    }
    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function multiply(int $a, int $b) : int
    {
        return $a * $b;
    }
}
CODE;
        $expected = $this->indentText($expected);
        $actual = $this->cmd->execute(new CommandParams('Calc.php class My\Test\Ns\Calc'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * @expectedException \Bigwhoop\Trumpet\Commands\ExecutionFailedException
     * @expectedExceptionMessage Class 'Calc' was not found in file 'Calc.php'. Available classes: My\Test\Ns\Calc
     */
    public function testClassNotFound()
    {
        $this->cmd->execute(new CommandParams('Calc.php class Calc'), $this->ctx);
    }

    /**
     * !code File.php method Name\Space\ClassName create.
     */
    public function testStaticMethod()
    {
        $expected = <<<'CODE'
/**
 * @return Calc
 */
public static function create()
{
    return new self();
}
CODE;
        $expected = $this->indentText($expected);
        $actual = $this->cmd->execute(new CommandParams('Calc.php method My\Test\Ns\Calc create'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !code File.php method Name\Space\ClassName MethodName.
     */
    public function testMethod()
    {
        $expected = <<<'CODE'
/**
 * @param int $a
 * @param int $b
 *
 * @return int
 */
public function add(int $a, int $b) : int
{
    return $a + $b;
}
CODE;
        $expected = $this->indentText($expected);
        $actual = $this->cmd->execute(new CommandParams('Calc.php method My\Test\Ns\Calc add'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * @expectedException \Bigwhoop\Trumpet\Commands\ExecutionFailedException
     * @expectedExceptionMessage Class 'Calc' was not found in file 'Calc.php'. Available classes: My\Test\Ns\Calc
     */
    public function testClassNotFoundForMethod()
    {
        $this->cmd->execute(new CommandParams('Calc.php method Calc divide'), $this->ctx);
    }

    /**
     * @expectedException \Bigwhoop\Trumpet\Commands\ExecutionFailedException
     * @expectedExceptionMessage Method 'divide' of class 'My\Test\Ns\Calc' was not found in file 'Calc.php'.
     */
    public function testMethodNotFound()
    {
        $this->cmd->execute(new CommandParams('Calc.php method My\Test\Ns\Calc divide'), $this->ctx);
    }

    /**
     * !line File.php LineNumber.
     */
    public function testLine()
    {
        $expected = $this->indentText('class Calc');
        $actual = $this->cmd->execute(new CommandParams('Calc.php line 5'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !line File.php From-To.
     */
    public function testLines()
    {
        $expected = <<<'CODE'
     * @return int
     */
    public function multiply(int $a, int $b): int
CODE;
        $expected = $this->indentText($expected);

        $actual = $this->cmd->execute(new CommandParams('Calc.php line 30-32'), $this->ctx);
        $this->assertSourceCode($expected, $actual);

        $actual = $this->cmd->execute(new CommandParams('Calc.php line 32-30'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !code File.php function Name\Space\FuncName.
     */
    public function testFunction()
    {
        $expected = <<<'CODE'
/**
 * @param int $a
 * @param int $b
 *
 * @return int
 */
function addNumbers(int $a, int $b) : int
{
    return $a + $b;
}
CODE;
        $expected = $this->indentText($expected);
        $actual = $this->cmd->execute(new CommandParams('Calc.php function My\Test\Ns\addNumbers'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }
}
