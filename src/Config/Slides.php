<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config;

final class Slides implements \IteratorAggregate, \Countable
{
    /** @var Slide[] */
    private $slides = [];

    /**
     * @return Slide
     */
    public function addBlankSlide()
    {
        $slide = new Slide();
        $this->slides[] = $slide;

        return $slide;
    }

    /**
     * @param Slide $slide
     */
    public function addSlide(Slide $slide)
    {
        $this->slides[] = $slide;
    }

    public function addContentToNewSlide(string $content)
    {
        $slide = $this->getCurrentSlide();
        if (!$slide->isEmpty()) {
            $slide = $this->addBlankSlide();
        }

        $slide->addLine($content);
    }
    
    public function addContentToCurrentSlide(string $content)
    {
        $this->getCurrentSlide()->addLine($content);
    }

    private function getCurrentSlide(): Slide
    {
        if (empty($this->slides)) {
            $this->addBlankSlide();
        }

        return $this->slides[count($this->slides) - 1];
    }

    public function trim()
    {
        if ($this->getCurrentSlide()->isEmpty()) {
            unset($this->slides[count($this->slides) - 1]);
        }
    }
    
    public function getAll(): array
    {
        return $this->slides;
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->slides);
    }

    public function count(): int
    {
        return count($this->slides);
    }
}
