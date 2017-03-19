<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\Presentation;

interface Param
{
    /**
     * @param mixed        $value
     * @param Presentation $presentation
     */
    public function parse($value, Presentation $presentation);
}
