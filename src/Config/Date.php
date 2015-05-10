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

class Date
{
    /** @var \DateTime */
    public $date;

    /** @var string */
    public $format = '';

    /**
     * @param \DateTime $date
     * @param string    $format
     */
    public function __construct(\DateTime $date, $format = 'l, F j Y')
    {
        $this->date = $date;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->date->format($this->format);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
