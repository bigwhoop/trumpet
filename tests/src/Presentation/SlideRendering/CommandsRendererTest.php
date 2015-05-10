<?php

namespace Bigwhoop\Trumpet\Tests\Presentation\SlideRendering;

use Bigwhoop\Trumpet\Config\Slide;
use Bigwhoop\Trumpet\Presentation\SlideRendering\CommandsRenderer;
use Bigwhoop\Trumpet\Tests\TestCase;

class CommandsRendererTest extends TestCase
{
    /**
     * @Inject
     *
     * @var CommandsRenderer
     */
    private $renderer;

    public function testRendering()
    {
        $expected = <<<'CODE'
html {
    width: 100%;
}
    98 + 3 = 101
CODE;

        $slide = new Slide("!include theme/assets/styles.css\n!exec Calc.php");
        $actual = $this->renderer->render($slide);

        $this->assertEquals($expected, $actual);
    }
}
