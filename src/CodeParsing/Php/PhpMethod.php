<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

final class PhpMethod
{
    use SourceTrait;

    /** @var string */
    private $name = '';

    /** @var bool */
    private $isStatic = false;

    public function __construct(string $name, bool $isStatic, string $source)
    {
        $this->name = $name;
        $this->isStatic = $isStatic;
        $this->source = $source;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }
}
