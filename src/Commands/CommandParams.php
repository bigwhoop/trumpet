<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

final class CommandParams
{
    /** @var string[] */
    private $params = [];

    public function __construct(string $params = '', string $argumentSeparator = ' ')
    {
        $this->params = str_getcsv($params, $argumentSeparator, '"');
    }

    public function getFirstArgument(string $default = ''): string
    {
        return $this->getArgument(0, $default);
    }

    public function hasFirstArgument(): bool
    {
        return $this->hasArgument(0);
    }
    
    public function getSecondArgument(string $default = ''): string
    {
        return $this->getArgument(1, $default);
    }

    public function hasSecondArgument(): bool
    {
        return $this->hasArgument(1);
    }

    public function getThirdArgument(string $default = ''): string
    {
        return $this->getArgument(2, $default);
    }

    public function hasThirdArgument(): bool
    {
        return $this->hasArgument(2);
    }

    public function getArgument(int $n, string $default = ''): string
    {
        $args = $this->getArguments();

        return array_key_exists($n, $args) ? $args[$n] : $default;
    }
    
    public function getArguments(): array
    {
        return $this->params;
    }
    
    public function hasArgument(int $n): bool
    {
        return array_key_exists($n, $this->getArguments());
    }
}
