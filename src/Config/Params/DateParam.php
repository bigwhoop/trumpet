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

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Date;

class DateParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        if (is_int($value)) {
            // We don't use format 'U' as the TZ would be UTC
            $date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', $value));
        } elseif (preg_match('#\d{4}-\d{1,2}-\d{1,2}#', $value)) {
            $date = \DateTime::createFromFormat('Y-m-d', $value);
        } else {
            throw new ConfigException('Dates must be a string in the format YYYY-MM-DD.');
        }

        $date->setTime(0, 0, 0);

        $presentation->date = new Date($date);
    }
}
