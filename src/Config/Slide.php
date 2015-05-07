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

class Slide
{
    /** @var string */
    public $content = '';

    /**
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * @param string $content
     */
    public function addLine($content)
    {
        if (empty($this->content)) {
            $this->content = $content;
        } else {
            $this->content .= "\n$content";
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->content === '';
    }
}
