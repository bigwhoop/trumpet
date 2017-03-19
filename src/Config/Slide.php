<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config;

final class Slide
{
    /** @var string */
    public $content = '';

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function addLine(string $content)
    {
        if (empty($this->content)) {
            $this->content = $content;
        } else {
            $this->content .= "\n$content";
        }
    }

    public function isEmpty(): bool
    {
        return $this->content === '';
    }
}
