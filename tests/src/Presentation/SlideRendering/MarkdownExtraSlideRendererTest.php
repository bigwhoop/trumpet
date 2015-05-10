<?php

namespace Bigwhoop\Trumpet\Tests\Presentation\SlideRendering;

use Bigwhoop\Trumpet\Config\Slide;
use Bigwhoop\Trumpet\Presentation\SlideRendering\MarkdownExtraSlideRenderer;
use Bigwhoop\Trumpet\Tests\TestCase;

class MarkdownExtraSlideRendererTest extends TestCase
{
    /**
     * @Inject
     *
     * @var MarkdownExtraSlideRenderer
     */
    private $renderer;

    public function testRendering()
    {
        $expected = <<<'CODE'
<h1>Hi</h1>

<h2>There</h2>

<p>My name is...</p>

CODE;

        $slide = new Slide("# Hi\n\n##There\n\nMy name is...");
        $actual = $this->renderer->render($slide);

        $this->assertEquals($expected, $actual);
    }
}
