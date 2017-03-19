<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Date;

final class DateParam implements Param
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
