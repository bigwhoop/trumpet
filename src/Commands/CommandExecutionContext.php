<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Bigwhoop\Trumpet\Presentation\Theming\Theme;

final class CommandExecutionContext
{
    /** @var Theme */
    private $theme;

    /** @var string */
    private $workingDir = '';
    
    public function __construct(Theme $theme, string $workingDirectory)
    {
        $this->theme = $theme;
        $this->workingDir = $workingDirectory;
    }
    
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function getWorkingDirectory(): string
    {
        return $this->workingDir;
    }

    public function hasFileInWorkingDirectory(string $file): bool
    {
        return file_exists($this->workingDir.'/'.$file);
    }

    public function getPathOfFileInWorkingDirectory(string $file): string
    {
        return $this->workingDir.'/'.$file;
    }

    public function getContentsOfFileInWorkingDirectory(string $file): string
    {
        $path = $this->workingDir.'/'.$file;
        if (!is_readable($path)) {
            throw new InvalidArgumentException("File '$file' must be readable from path '{$this->workingDir}'.");
        }

        return file_get_contents($path);
    }

    public function ensureTempDirectory(): string
    {
        $tmpDir = $this->getWorkingDirectory().'/.tmp';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        return $tmpDir;
    }
}
