<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Presentation;

use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Presentation\SlideRendering\SlideRenderer;

interface Presenter
{
    /**
     * @param SlideRenderer $slideRenderer
     */
    public function addSlideRenderer(SlideRenderer $slideRenderer);

    /**
     * @param Presentation $presentation
     *
     * @return string
     */
    public function present(Presentation $presentation);
}
