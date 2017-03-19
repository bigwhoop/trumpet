<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation\Theming;

abstract class Theme
{
    /** @var string */
    protected $basePath = '';
    
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        $this->validate();
    }

    protected function validate()
    {
        if (!is_dir($this->basePath) || !is_readable($this->basePath)) {
            throw new ThemeException("Theme path '{$this->basePath}' must be a readable directory.");
        }

        $layoutPath = $this->getLayoutPath();
        if (!file_exists($layoutPath)) {
            $fileName = basename($layoutPath);
            
            throw new ThemeException("Theme must contain a '{$fileName}' file. Could not find one at '{$layoutPath}'.");
        }
    }
    
    abstract public function render(array $params): string;
    abstract protected function getLayoutPath(): string;
    
    protected function getAssetPath(string $name): string
    {
        return $this->basePath.'/assets/'.$name;
    }
    
    public function getAsset(string $name): Asset
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
}
