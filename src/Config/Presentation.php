<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config;

final class Presentation
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
