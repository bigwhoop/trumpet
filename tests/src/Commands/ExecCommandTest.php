<?php

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
        $this->assertEquals($expected, $actual);
    }

    public function testIncludeRequire()
    {
        $expected = $this->indentText('98 + 3 = 101');
        $actual = $this->cmd->execute(new CommandParams('CalcIncluder.php'), $this->ctx);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $text
     * @param string $indent
     *
     * @return string
     */
    private function indentText($text, $indent = '    ')
    {
        $lines = explode("\n", str_replace("\r", '', $text));
        $lines = array_map(function ($line) use ($indent) {
            return $indent.$line;
        }, $lines);

        return  implode("\n", $lines);
    }
}
