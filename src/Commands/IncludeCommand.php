<?php declare(strict_types=1);


namespace Bigwhoop\Trumpet\Commands;

class IncludeCommand implements Command
{
    public function getToken(): string
    {
        return 'include';
    }

    public function execute(CommandParams $params, CommandExecutionContext $executionContext): string
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
