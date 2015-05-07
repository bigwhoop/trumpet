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

class PHPMethod
{
    use SourceTrait;

    /** @var string */
    private $name = '';

    /** @var bool */
    private $isStatic = false;

    /**
     * @param string $name
     * @param bool   $isStatic
     * @param string $source
     */
    public function __construct($name, $isStatic, $source)
    {
        $this->name = $name;
        $this->isStatic = $isStatic;
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->isStatic;
    }
}
