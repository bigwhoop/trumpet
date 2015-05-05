<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Config;

class Author
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

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
}
