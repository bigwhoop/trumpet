<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation;

use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Presentation\SlideRendering\SlideRenderer;

interface Presenter
{
    public function addSlideRenderer(SlideRenderer $slideRenderer);
    public function present(Presentation $presentation): string ;
}
