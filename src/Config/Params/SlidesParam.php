<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Slides;

final class SlidesParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        if (!is_string($value)) {
            throw new ConfigException('Slides must be a string.');
        }

        $text = $this->normalizeNewLines($value);

        $lines = explode("\n", $text);

        if (empty($lines)) {
            throw new ConfigException('Slides must not be empty.');
        }

        $slides = new Slides();

        foreach ($lines as $line) {
            if ($this->isHeader1($line)) {
                $slides->addContentToNewSlide($line);
                $slides->addBlankSlide();
            } elseif ($this->isHeader2($line)) {
                $slides->addContentToNewSlide($line);
            } else {
                $slides->addContentToCurrentSlide($line);
            }
        }

        $slides->trim();

        $presentation->slides = $slides;
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    private function isHeader1($line)
    {
        return substr($line, 0, 2) === '# ';
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    private function isHeader2($line)
    {
        return substr($line, 0, 3) === '## ';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function normalizeNewLines($text)
    {
        $text = str_replace("\r", '', $text);

        return $text;
    }
}
