<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

use Bigwhoop\Trumpet\Exceptions\OutOfBoundsException;

final class PhpClass
{
    use FullyQualifiedNameTrait;
    use SourceTrait;

    /** @var PhpMethod[] */
    private $methods = [];

    /**
     * @param string      $name
     * @param PhpMethod[] $methods
     * @param string      $source
     */
    public function __construct(string $name, array $methods = [], string $source = '')
    {
        $this->name = $name;
        $this->addMethods($methods);
        $this->source = $source;
    }
    
    public function addMethods(array $methods)
    {
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
    }
    
    public function addMethod(PhpMethod $method)
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
     * @return PhpMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string $name
     *
     * @return PhpMethod
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
