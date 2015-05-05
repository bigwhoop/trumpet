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

use Bigwhoop\Trumpet\Exceptions\OutOfBoundsException;

class PHPClass
{
    use FullyQualifiedNameTrait;
    use SourceTrait;

    /** @var PHPMethod[] */
    private $methods = [];

    /**
     * @param string      $name
     * @param PHPMethod[] $methods
     * @param string      $source
     */
    public function __construct($name, array $methods = [], $source = '')
    {
        $this->name = $name;
        $this->methods = $methods;
        $this->source = $source;
    }

    /**
     * @param PHPMethod $method
     */
    public function addMethod(PHPMethod $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMethod($name)
    {
        return array_key_exists($name, $this->methods);
    }

    /**
     * @return PHPMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string $name
     *
     * @return PHPMethod
     *
     * @throws OutOfBoundsException
     */
    public function getMethod($name)
    {
        if (!array_key_exists($name, $this->methods)) {
            throw new OutOfBoundsException("Method '$name' is not available.");
        }

        return $this->methods[$name];
    }
}
