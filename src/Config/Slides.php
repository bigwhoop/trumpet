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

class Slides implements \IteratorAggregate, \Countable
{
    /** @var Slide[] */
    private $slides = [];

    public function addBlankSlide()
    {
        $this->slides[] = new Slide();
    }

    /**
     * @param Slide $slide
     */
    public function addSlide(Slide $slide)
    {
        $this->slides[] = $slide;
    }

    /**
     * @param string $content
     */
    public function addContentToNewSlide($content)
    {
        $slide = $this->getCurrentSlide();
        if (!$slide->isEmpty()) {
            $slide = new Slide();
        }

        $slide->addLine($content);

        $this->addSlide($slide);
    }

    /**
     * @param string $content
     */
    public function addContentToCurrentSlide($content)
    {
        $this->getCurrentSlide()->addLine($content);
    }

    /**
     * @return Slide
     */
    private function getCurrentSlide()
    {
        if (empty($this->slides)) {
            $this->addBlankSlide();
        }

        return $this->slides[count($this->slides) - 1];
    }

    public function trim()
    {
        if ($this->getCurrentSlide()->isEmpty()) {
            unset($slides[count($slides) - 1]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->slides);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->slides);
    }
}
