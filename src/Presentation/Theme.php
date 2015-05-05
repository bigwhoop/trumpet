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

interface Theme
{
    /**
     * @return string
     */
    public function getLayout();

    /**
     * @param string $name
     *
     * @return Asset
     */
    public function getAsset($name);
}
