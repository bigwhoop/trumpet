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

class ExecCommand implements Command
{
    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return 'exec';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CommandParams $params, CommandExecutionContext $executionContext)
    {
        $fileName = $params->getFirstArgument();

        if (!$executionContext->hasFileInWorkingDirectory($fileName)) {
            throw new ExecutionFailedException("File '$fileName' does not exist.");
        }

        $path = $executionContext->getPathOfFileInWorkingDirectory($fileName);

        $cmd = sprintf('php %s 2>&1', escapeshellarg($path));
        $output = shell_exec($cmd);

        return $this->wrapOutput($output);
    }

    /**
     * @param string $output
     *
     * @return string
     */
    private function wrapOutput($output)
    {
        $lines = explode("\n", $output);

        $indented = array_map(function ($e) {
            return '    '.$e;
        }, $lines);

        return implode("\n", $indented);
    }
}
