<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\CodeParsing\PHP;

class PHPFunction
{
    use FullyQualifiedNameTrait;
    use SourceTrait;

    /**
     * @param string $name
     * @param string $source
     */
    public function __construct($name, $source)
    {
        $this->name = $name;
        $this->source = $source;
    }
}
