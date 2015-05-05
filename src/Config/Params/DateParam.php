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
            $presentation->date = new Date(\DateTime::createFromFormat('U', $value));

            return;
        }

        if (!preg_match('#\d{4}-\d{1,2}-\d{1,2}#', $value)) {
            throw new ConfigException('Dates must be a string in the format YYYY-MM-DD.');
        }

        $presentation->date = new Date(\DateTime::createFromFormat('Y-m-d', $value));
    }
}
