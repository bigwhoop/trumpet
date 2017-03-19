<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Http;

use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class BuiltInPhpHttpServer implements HttpServer
{
    use LoggerAwareTrait;
    
    /** @var string */
    private $host = '';

    /** @var int */
    private $port = 0;

    /** @var string */
    private $routerPath = '';

    /** @var string */
    private $docRoot = '';
    
    public function __construct(string $host, int $port, string $routerPath, string $docRoot)
    {
        if (!is_readable($routerPath)) {
            throw new InvalidArgumentException("Router script '$routerPath' must be readable.");
        }

        if (!is_readable($docRoot) || !is_dir($docRoot)) {
            throw new InvalidArgumentException("Document root '$docRoot' must be a readable directory.");
        }

        $this->host = $host;
        $this->port = $port;
        $this->routerPath = $routerPath;
        $this->docRoot = $docRoot;
        $this->logger = new NullLogger();
    }

    public function listen()
    {
        $cmd = sprintf(
            'php -S %s -t %s %s',
            escapeshellarg($this->host.':'.$this->port),
            escapeshellarg($this->docRoot),
            escapeshellarg($this->routerPath)
        );
            
        $this->logger->info(sprintf("Starting webserver on %s:%d ...\n", $this->host, $this->port));
        
        putenv('foo=bar');
        exec($cmd);
    }
}
