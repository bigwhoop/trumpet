<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
