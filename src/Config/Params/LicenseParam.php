<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;

final class LicenseParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        if (!is_string($value)) {
            throw new ConfigException('License must be a string.');
        }

        if (empty($value)) {
            throw new ConfigException('License must not be empty.');
        }

        $presentation->license = $value;
    }
}
