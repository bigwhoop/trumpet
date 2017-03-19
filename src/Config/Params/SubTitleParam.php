<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;

final class SubTitleParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        if (!is_string($value)) {
            throw new ConfigException('Sub title must be a string.');
        }

        if (empty($value)) {
            throw new ConfigException('Sub title must not be empty.');
        }

        $presentation->subtitle = $value;
    }
}
