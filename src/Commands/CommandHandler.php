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

class CommandHandler
{
    /** @var Command[] */
    private $commands = [];

    /**
     * @param Command $command
     */
    public function registerCommand(Command $command)
    {
        $this->commands[$command->getToken()] = $command;
    }

    /**
     * @param string $commandName
     *
     * @return bool
     */
    public function hasCommand($commandName)
    {
        return array_key_exists($commandName, $this->commands);
    }

    /**
     * @param string                  $commandName
     * @param CommandParams           $params
     * @param CommandExecutionContext $executionContext
     *
     * @return string
     */
    public function execute($commandName, CommandParams $params, CommandExecutionContext $executionContext)
    {
        if (!array_key_exists($commandName, $this->commands)) {
            throw new UnknownCommandException("Command '$commandName' must be registered before it can be used.");
        }

        return $this->commands[$commandName]->execute($params, $executionContext);
    }
}
