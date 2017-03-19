<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

final class CommandHandler
{
    /** @var Command[] */
    private $commands = [];

    public function registerCommand(Command $command)
    {
        $this->commands[$command->getToken()] = $command;
    }
    
    public function hasCommand(string $commandName): bool
    {
        return array_key_exists($commandName, $this->commands);
    }
    
    public function execute(string $commandName, CommandParams $params, CommandExecutionContext $executionContext): string
    {
        if (!array_key_exists($commandName, $this->commands)) {
            throw new UnknownCommandException("Command '$commandName' must be registered before it can be used.");
        }

        return $this->commands[$commandName]->execute($params, $executionContext);
    }
}
