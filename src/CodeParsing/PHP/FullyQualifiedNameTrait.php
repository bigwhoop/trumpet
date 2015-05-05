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

trait FullyQualifiedNameTrait
{
    /** @var string */
    private $name = '';

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        $chunks = explode('\\', $this->name);

        return array_pop($chunks);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        $chunks = explode('\\', $this->name);
        array_pop($chunks);

        return implode('\\', $chunks);
    }
}
