<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\CodeParsing\Php;

trait FullyQualifiedNameTrait
{
    private $name = '';

    public function getFullName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        $chunks = explode('\\', $this->name);

        return array_pop($chunks);
    }

    public function getNamespace(): string
    {
        $chunks = explode('\\', $this->name);
        array_pop($chunks);

        return implode('\\', $chunks);
    }
}
