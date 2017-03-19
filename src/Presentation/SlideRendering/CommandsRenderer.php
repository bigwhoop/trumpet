<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Presentation\SlideRendering;

use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandHandler;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Config\Slide;

final class CommandsRenderer implements SlideRenderer
{
    const COMMAND_PREFIX = '!';

    /** @var CommandHandler */
    private $commandHandler;

    /** @var CommandExecutionContext */
    private $executionContext;

    public function __construct(CommandHandler $handler, CommandExecutionContext $executionContext)
    {
        $this->commandHandler = $handler;
        $this->executionContext = $executionContext;
    }
    
    public function render(Slide $slide): string
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
