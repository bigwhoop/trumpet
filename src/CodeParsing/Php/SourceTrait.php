<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\CodeParsing\Php;

trait SourceTrait
{
    private $source = '';
    
    public function setSource(string $source)
    {
        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
