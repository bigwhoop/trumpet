<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation\Theming;

final class TwigTheme extends Theme
{
    public function render(array $params): string
    {
        $loader = new \Twig_Loader_Filesystem($this->basePath . '/tmpl');
        $twig = new \Twig_Environment($loader);
        
        return $twig->render('layout.html.twig', $params);
    }
    
    protected function getLayoutPath(): string
    {
        return $this->basePath.'/tmpl/layout.html.twig';
    }
}
