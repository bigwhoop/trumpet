<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Slide;

class SlidesParam implements Param
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

        /** @var Slide[] $slides */
        $slides = [new Slide()];
        foreach ($lines as $line) {
            $currentSlideIsEmpty = $slides[count($slides) - 1]->isEmpty();
            if ($this->isHeader1($line)) {
                if ($currentSlideIsEmpty) {
                    $slides[count($slides) - 1]->addLine($line);
                } else {
                    $slides[] = new Slide($line);
                }
                $slides[] = new Slide();
            } elseif ($this->isHeader2($line)) {
                if ($currentSlideIsEmpty) {
                    $slides[count($slides) - 1]->addLine($line);
                } else {
                    $slides[] = new Slide($line);
                }
            } else {
                $slides[count($slides) - 1]->addLine($line);
            }
        }

        if ($slides[count($slides) - 1]->isEmpty()) {
            unset($slides[count($slides) - 1]);
        }

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
