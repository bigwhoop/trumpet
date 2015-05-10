<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Presentation;

use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Slide;
use Handlebars\Handlebars;

class HandlebarsPresenter implements Presenter
{
    /** @var Theme */
    private $theme;

    /** @var Handlebars */
    private $hbs;

    /** @var SlideRenderer[] */
    private $slideRenderers = [];

    /**
     * @param Theme      $theme
     * @param Handlebars $hbs
     */
    public function __construct(Theme $theme, Handlebars $hbs)
    {
        $this->theme = $theme;
        $this->hbs = $hbs;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlideRenderer(SlideRenderer $slideRenderer)
    {
        $this->slideRenderers[] = $slideRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function present(Presentation $presentation)
    {
        $layout = $this->theme->getLayout();

        foreach ($presentation->slides as $slide) {
            $this->renderSlide($slide);
        }

        $out = $this->hbs->render($layout, [
            'title'          => $presentation->title,
            'subtitle'       => $presentation->subtitle,
            'date'           => $presentation->date->toString(),
            'presenters'     => $presentation->authors,
            'license'        => $presentation->license,
            'theme_settings' => $presentation->themeSettings,
            'slides'         => $presentation->slides->getAll(),
        ]);

        return $out;
    }

    /**
     * @param Slide $slide
     *
     * @return Slide
     */
    private function renderSlide(Slide $slide)
    {
        foreach ($this->slideRenderers as $renderer) {
            $slide->content = $renderer->render($slide);
        }

        return $slide;
    }
}
