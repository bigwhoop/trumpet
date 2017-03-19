<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation\SlideRendering;

use Bigwhoop\Trumpet\Config\Slide;

final class MarkdownExtraSlideRenderer implements SlideRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(Slide $slide): string
    {
        return \Michelf\MarkdownExtra::defaultTransform($slide->content);
    }
}
