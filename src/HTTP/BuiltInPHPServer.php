<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\HTTP;

use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Monolog\Logger;

class BuiltInPHPServer implements Server
{
    /** @var string */
    private $host = '';

    /** @var int */
    private $port = 0;

    /** @var string */
    private $routerPath = '';

    /** @var string */
    private $docRoot = '';

    /** @var Logger */
    private $log;

    /**
     * @param string $host
     * @param int    $port
     * @param string $routerPath
     * @param string $docRoot
     *
     * @throws InvalidArgumentException
     */
    public function __construct($host, $port, $routerPath, $docRoot)
    {
        if (!is_readable($routerPath)) {
            throw new InvalidArgumentException("Router script '$routerPath' must be readable.");
        }

        if (!is_readable($docRoot) || !is_dir($docRoot)) {
            throw new InvalidArgumentException("Document root '$docRoot' must be a readable directory.");
        }

        $this->host = (string) $host;
        $this->port = (int) $port;
        $this->routerPath = (string) $routerPath;
        $this->docRoot = (string) $docRoot;
    }

    /**
     * @param Logger $log
     */
    public function setLog(Logger $log)
    {
        $this->log = $log;
    }

    public function listen()
    {
        $cmd = sprintf(
            'php -S %s -t %s %s',
            escapeshellarg($this->host.':'.$this->port),
            escapeshellarg($this->docRoot),
            escapeshellarg($this->routerPath)
        );

        if ($this->log) {
            $this->log->info(sprintf("Starting webserver on %s:%d ...\n", $this->host, $this->port));
        }

        putenv('foo=bar');
        exec($cmd);
    }
}
