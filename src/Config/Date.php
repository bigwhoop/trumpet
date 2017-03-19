<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config;

final class Date
{
    /** @var \DateTime */
    public $date;

    /** @var string */
    public $format = '';

    public function __construct(\DateTime $date, string $format = 'l, F j Y')
    {
        $this->date = $date;
        $this->format = $format;
    }

    public function toString(): string
    {
        return $this->date->format($this->format);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
