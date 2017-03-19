<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Config;

final class Author
{
    /** @var string */
    public $name = '';

    /** @var string */
    public $company = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $twitter = '';

    /** @var string */
    public $website = '';

    /** @var string */
    public $skype = '';

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function __toString(): string
    {
        return $this->name;
    }
}
