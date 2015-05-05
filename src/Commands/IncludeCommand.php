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

class IncludeCommand implements Command
{
    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return 'include';
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

        $contents = $executionContext->getContentsOfFileInWorkingDirectory($fileName);

        switch ($params->getSecondArgument()) {
            case 'line':
                $lines = explode("\n", $contents);
                $range = $params->getThirdArgument();

                if (is_numeric($range)) {
                    return implode("\n", array_slice($lines, $range - 1, 1));
                }

                $matches = [];
                if (!preg_match('|(\d+)-(\d+)|', $range, $matches)) {
                    throw new ExecutionFailedException("Line definition '$range' is not valid. Must be in format N or N-N.");
                }

                $from = min([$matches[1], $matches[2]]);
                $to = max([$matches[1], $matches[2]]);

                return implode("\n", array_slice($lines, $from - 1, $to - $from + 1));

            default:
                return $contents;
        }
    }
}
