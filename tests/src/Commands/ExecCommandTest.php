<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Commands;

use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Commands\ExecCommand;
use Bigwhoop\Trumpet\Tests\TestCase;

class ExecCommandTest extends TestCase
{
    /**
     * @Inject
     *
     * @var ExecCommand
     */
    private $cmd;

    /**
     * @Inject
     *
     * @var CommandExecutionContext
     */
    private $ctx;

    /**
     * !exec File.php.
     */
    public function testExecution()
    {
        $expected = $this->indentText('98 + 3 = 101');
        $actual = $this->cmd->execute(new CommandParams('Calc.php'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }

    public function testIncludeRequire()
    {
        $expected = $this->indentText('98 + 3 = 101');
        $actual = $this->cmd->execute(new CommandParams('CalcIncluder.php'), $this->ctx);
        $this->assertSourceCode($expected, $actual);
    }
}
