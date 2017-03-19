<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

use Bigwhoop\Trumpet\Exceptions\OutOfBoundsException;

final class ParserResult
{
    /** @var PhpClass[] */
    private $classes = [];

    /** @var PhpFunction[] */
    private $functions = [];

    public function addClass(PhpClass $class)
    {
        $this->classes[$class->getFullName()] = $class;
    }

    public function hasClass(string $name): bool
    {
        return array_key_exists($name, $this->classes);
    }

    /**
     * @return PhpClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
    
    public function getClass(string $name): PhpClass
    {
        if (!array_key_exists($name, $this->classes)) {
            throw new OutOfBoundsException("Class '$name' is not available.");
        }

        return $this->classes[$name];
    }

    public function addFunction(PhpFunction $function)
    {
        $this->functions[$function->getFullName()] = $function;
    }

    /**
     * @return PhpFunction[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function hasFunction(string $name): bool
    {
        return array_key_exists($name, $this->functions);
    }

    public function getFunction(string $name): PhpFunction
    {
        if (!array_key_exists($name, $this->functions)) {
            throw new OutOfBoundsException("Function '$name' is not available.");
        }

        return $this->functions[$name];
    }
}
