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

interface Command
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @param CommandParams           $params
     * @param CommandExecutionContext $executionContext
     *
     * @return string
     */
    public function execute(CommandParams $params, CommandExecutionContext $executionContext);
}
