<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation\SlideRendering;

use Bigwhoop\Trumpet\Config\Slide;

interface SlideRenderer
{
    public function render(Slide $slide): string;
}
