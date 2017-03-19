<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Commands;

use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Commands\IncludeCommand;
use Bigwhoop\Trumpet\Tests\TestCase;

class IncludeCommandTest extends TestCase
{
    /**
     * @Inject
     *
     * @var IncludeCommand
     */
    private $cmd;

    /**
     * @Inject
     *
     * @var CommandExecutionContext
     */
    private $ctx;

    /**
     * !include File.php.
     */
    public function testFile()
    {
        $expected = file_get_contents($this->ctx->getWorkingDirectory().'/Calc.php');
        $actual = $this->cmd->execute(new CommandParams('Calc.php'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !include File.php line 10.
     */
    public function testLine()
    {
        $expected = '    public static function create()';
        $actual = $this->cmd->execute(new CommandParams('Calc.php line 10'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    /**
     * !include File.php line 10:12.
     */
    public function testLines()
    {
        $expected = <<<'CODE'
    public static function create()
    {
        return new self();
    }
CODE;

        $actual = $this->cmd->execute(new CommandParams('Calc.php line 10-13'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }
}
