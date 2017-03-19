<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Commands;

use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Tests\TestCase;

class CommandParamsTest extends TestCase
{
    public function testSimpleStrings()
    {
        $params = new CommandParams('foo bar baz fu bar baz');

        $this->assertEquals(['foo', 'bar', 'baz', 'fu', 'bar', 'baz'], $params->getArguments());

        $this->assertSame('foo', $params->getFirstArgument());
        $this->assertSame('bar', $params->getSecondArgument());
        $this->assertSame('baz', $params->getThirdArgument());

        $this->assertSame('foo', $params->getArgument(0));
        $this->assertSame('bar', $params->getArgument(1));
        $this->assertSame('baz', $params->getArgument(2));
        $this->assertSame('fu', $params->getArgument(3));
        $this->assertSame('bar', $params->getArgument(4));
        $this->assertSame('baz', $params->getArgument(5));
    }

    public function testEscapedStrings()
    {
        $params = new CommandParams('"foo bar" "baz fu" \'bar baz\'');

        $this->assertEquals(['foo bar', 'baz fu', "'bar", "baz'"], $params->getArguments());

        $this->assertSame('foo bar', $params->getFirstArgument());
        $this->assertSame('baz fu', $params->getSecondArgument());
        $this->assertSame("'bar", $params->getThirdArgument());

        $this->assertSame('foo bar', $params->getArgument(0));
        $this->assertSame('baz fu', $params->getArgument(1));
        $this->assertSame("'bar", $params->getArgument(2));
        $this->assertSame("baz'", $params->getArgument(3));
    }
}
