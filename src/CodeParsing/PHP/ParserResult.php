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

class ParserResult
{
    /** @var PHPClass[] */
    private $classes = [];

    /** @var PHPFunction[] */
    private $functions = [];

    /**
     * @param PHPClass $class
     */
    public function addClass(PHPClass $class)
    {
        $this->classes[$class->getFullName()] = $class;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasClass($name)
    {
        return array_key_exists($name, $this->classes);
    }

    /**
     * @return PHPClass[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $name
     *
     * @return PHPClass
     *
     * @throws OutOfBoundsException
     */
    public function getClass($name)
    {
        if (!array_key_exists($name, $this->classes)) {
            throw new OutOfBoundsException("Class '$name' is not available.");
        }

        return $this->classes[$name];
    }

    /**
     * @param PHPFunction $function
     */
    public function addFunction(PHPFunction $function)
    {
        $this->functions[$function->getFullName()] = $function;
    }

    /**
     * @return PHPFunction[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFunction($name)
    {
        return array_key_exists($name, $this->functions);
    }

    /**
     * @param string $name
     *
     * @return PHPFunction
     *
     * @throws OutOfBoundsException
     */
    public function getFunction($name)
    {
        if (!array_key_exists($name, $this->functions)) {
            throw new OutOfBoundsException("Function '$name' is not available.");
        }

        return $this->functions[$name];
    }
}
