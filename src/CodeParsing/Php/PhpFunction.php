<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

final class PhpFunction
{
    use FullyQualifiedNameTrait;
    use SourceTrait;

    public function __construct(string $name, string $source)
    {
        $this->name = $name;
        $this->source = $source;
    }
}
