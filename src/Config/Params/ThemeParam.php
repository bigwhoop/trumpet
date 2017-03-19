<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\Presentation;

final class ThemeParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        $presentation->themeSettings = $value;
    }
}
