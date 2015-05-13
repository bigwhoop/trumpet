<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Commands;

use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Bigwhoop\Trumpet\Presentation\Theming\Theme;

class CommandExecutionContext
{
    /** @var Theme */
    private $theme;

    /** @var string */
    private $workingDir = '';

    /**
     * @Inject({"Bigwhoop\Trumpet\Presentation\Theming\Theme","WorkingDirectory"})
     *
     * @param Theme  $theme
     * @param string $workingDirectory
     */
    public function __construct(Theme $theme, $workingDirectory)
    {
        $this->theme = $theme;
        $this->workingDir = $workingDirectory;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->workingDir;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function hasFileInWorkingDirectory($file)
    {
        return file_exists($this->workingDir.'/'.$file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getPathOfFileInWorkingDirectory($file)
    {
        return $this->workingDir.'/'.$file;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getContentsOfFileInWorkingDirectory($file)
    {
        $path = $this->workingDir.'/'.$file;
        if (!is_readable($path)) {
            throw new InvalidArgumentException("File '$file' must be readable from path '{$this->workingDir}'.");
        }

        return file_get_contents($path);
    }

    /**
     * @return string
     */
    public function ensureTempDirectory()
    {
        $tmpDir = $this->getWorkingDirectory().'/.tmp';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        return $tmpDir;
    }
}
