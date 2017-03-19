<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

final class ExecCommand implements Command
{
    public function getToken(): string
    {
        return 'exec';
    }

    public function execute(CommandParams $params, CommandExecutionContext $executionContext): string
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
    
    private function wrapOutput(string $output): string
    {
        $lines = explode("\n", $output);

        $indented = array_map(function ($e) {
            return '    '.$e;
        }, $lines);

        return implode("\n", $indented);
    }
}
