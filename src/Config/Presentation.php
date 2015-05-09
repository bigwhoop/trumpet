<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Config;

class Presentation
{
    /** @var string */
    public $title = '';

    /** @var string */
    public $subtitle = '';

    /** @var Date */
    public $date;

    /** @var Author[] */
    public $authors = [];

    /** @var Slides */
    public $slides;

    /** @var string */
    public $license = '';

    /** @var mixed */
    public $themeSettings;

    public function __construct()
    {
        $this->slides = new Slides();
    }
}
