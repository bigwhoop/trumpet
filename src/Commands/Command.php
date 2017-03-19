<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

interface Command
{
    public function getToken(): string;
    public function execute(CommandParams $params, CommandExecutionContext $executionContext): string;
}
