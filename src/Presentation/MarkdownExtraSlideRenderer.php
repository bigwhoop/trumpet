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

use Bigwhoop\Trumpet\Config\Slide;

class MarkdownExtraSlideRenderer implements SlideRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(Slide $slide)
    {
        return \Michelf\MarkdownExtra::defaultTransform($slide->content);
    }
}
