<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Presentation;

use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandHandler;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Config\Slide;

class CommandsRenderer implements SlideRenderer
{
    const COMMAND_PREFIX = '!';

    /** @var CommandHandler */
    private $commandHandler;

    /** @var CommandExecutionContext */
    private $executionContext;

    /**
     * @param CommandHandler          $handler
     * @param CommandExecutionContext $executionContext
     */
    public function __construct(CommandHandler $handler, CommandExecutionContext $executionContext)
    {
        $this->commandHandler = $handler;
        $this->executionContext = $executionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Slide $slide)
    {
        $lines = explode("\n", str_replace("\r", '', $slide->content));

        $out = [];
        foreach ($lines as $line) {
            if (0 === strpos($line, self::COMMAND_PREFIX)) {
                $command = substr($line, strlen(self::COMMAND_PREFIX));
                list($commandName, $commandParams) = explode(' ', $command, 2);

                if ($this->commandHandler->hasCommand($commandName)) {
                    $line = $this->commandHandler->execute($commandName, new CommandParams($commandParams), $this->executionContext);
                }
            }

            $out[] = $line;
        }

        return implode("\n", $out);
    }
}
