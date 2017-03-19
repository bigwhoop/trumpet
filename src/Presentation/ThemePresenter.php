<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation;

use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Config\Slide;
use Bigwhoop\Trumpet\Presentation\SlideRendering\SlideRenderer;
use Bigwhoop\Trumpet\Presentation\Theming\Theme;
use Handlebars\Handlebars;

final class ThemePresenter implements Presenter
{
    /** @var Theme */
    private $theme;

    /** @var SlideRenderer[] */
    private $slideRenderers = [];
    
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }
    
    public function addSlideRenderer(SlideRenderer $slideRenderer)
    {
        $this->slideRenderers[] = $slideRenderer;
    }
    
    public function present(Presentation $presentation): string
    {
        foreach ($presentation->slides as $slide) {
            $this->renderSlide($slide);
        }

        return $this->theme->render([
            'title'          => $presentation->title,
            'subtitle'       => $presentation->subtitle,
            'date'           => $presentation->date->toString(),
            'presenters'     => $presentation->authors,
            'license'        => $presentation->license,
            'theme_settings' => $presentation->themeSettings,
            'slides'         => $presentation->slides->getAll(),
        ]);
    }
    
    private function renderSlide(Slide $slide): Slide
    {
        foreach ($this->slideRenderers as $renderer) {
            $slide->content = $renderer->render($slide);
        }

        return $slide;
    }
}
