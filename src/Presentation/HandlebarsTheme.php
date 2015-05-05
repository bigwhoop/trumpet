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

class HandlebarsTheme implements Theme
{
    /** @var string */
    private $basePath = '';

    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;

        $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout()
    {
        return file_get_contents($this->getLayoutPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getAsset($name)
    {
        $path = $this->getAssetPath($name);
        if (!file_exists($path)) {
            throw new ThemeException("Theme asset '$name' must exist. Could not find it at '$path'.");
        }

        $asset = new Asset();
        $asset->content = file_get_contents($path);

        switch (pathinfo($name, PATHINFO_EXTENSION)) {
            case 'css':  $asset->contentType = 'text/css';        break;
            case 'js':   $asset->contentType = 'text/javascript'; break;
            case 'jpg':  $asset->contentType = 'image/jpeg';      break;
            case 'gif':  $asset->contentType = 'image/gif';       break;
            case 'png':  $asset->contentType = 'image/png';       break;
        }

        return $asset;
    }

    private function validate()
    {
        if (!is_dir($this->basePath) || !is_readable($this->basePath)) {
            throw new ThemeException("Theme path '{$this->basePath}' must be a readable directory.");
        }

        $layoutPath = $this->getLayoutPath();
        if (!file_exists($layoutPath)) {
            throw new ThemeException("Theme must contain a 'layout.hbs' file. Could not find one at '$layoutPath'.");
        }
    }

    /**
     * @return string
     *
     * @throws ThemeException
     */
    private function getLayoutPath()
    {
        return $this->basePath.'/tmpl/layout.hbs';
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws ThemeException
     */
    private function getAssetPath($name)
    {
        return $this->basePath.'/assets/'.$name;
    }
}
