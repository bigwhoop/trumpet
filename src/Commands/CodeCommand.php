<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

use Bigwhoop\Trumpet\CodeParsing\Php\PhpCodeParser;

/**
 * !code file.php
 * !code file.php line 12
 * !code file.php lines 12 16
 * !code file.php function my_function_name
 * !code file.php class My\Class\Name
 * !code file.php method My\Class\Name myMethod.
 */
final class CodeCommand implements Command
{
    /** @var PhpCodeParser */
    private $parser;

    public function __construct(PhpCodeParser $parser)
    {
        $this->parser = $parser;
    }
    
    public function getToken(): string
    {
        return 'code';
    }
    
    public function execute(CommandParams $params, CommandExecutionContext $executionContext): string
    {
        $fileName = $params->getFirstArgument();

        if (is_readable($fileName)) {
            $contents = file_get_contents($fileName);
        } else {
            if (!$executionContext->hasFileInWorkingDirectory($fileName)) {
                throw new ExecutionFailedException("File '$fileName' does not exist.");
            }

            $contents = $executionContext->getContentsOfFileInWorkingDirectory($fileName);
        }

        switch ($params->getSecondArgument()) {
            case 'class':
                return $this->findClass($contents, $params, $fileName);

            case 'abstract':
                return $this->findAbstract($contents);

            case 'method':
                return $this->findMethod($contents, $params, $fileName);

            case 'function':
                return $this->findFunction($contents, $params, $fileName);

            case 'line':
                return $this->findLines($contents, $params);

            default:
                return $this->wrapLines($contents);
        }
    }
    
    private function findClass(string $contents, CommandParams $params, string $fileName): string
    {
        $className = $params->getArgument(2);
        $result = $this->parser->parse($contents);

        if (!$result->hasClass($className)) {
            $availableClasses = implode(', ', array_keys($result->getClasses()));
            throw new ExecutionFailedException("Class '$className' was not found in file '$fileName'. Available classes: $availableClasses");
        }

        return $this->wrapLines($result->getClass($className)->getSource());
    }

    private function findAbstract(string $contents): string
    {
        $result = $this->parser->parse($contents);

        $out = [];

        $classes = $result->getClasses();
        if (count($classes)) {
            $out[] = 'CLASSES ('.count($classes).')';
            foreach ($classes as $class) {
                $out[] = ' '.$class->getFullName();
                foreach ($class->getMethods() as $method) {
                    $out[] = '  '.($method->isStatic() ? 'static ' : '').$method->getName().'()';
                }
            }
        }

        $functions = $result->getFunctions();
        if (count($functions)) {
            if (!empty($out)) {
                $out[] = '';
            }
            $out[] = 'FUNCTIONS ('.count($functions).')';
            foreach ($functions as $function) {
                $out[] = ' '.$function->getFullName();
            }
        }

        return $this->wrapLines($out);
    }
    
    private function findMethod(string $contents, CommandParams $params, string $fileName): string
    {
        $className = $params->getArgument(2);
        $methodName = $params->getArgument(3);
        $result = $this->parser->parse($contents);

        if (!$result->hasClass($className)) {
            $availableClasses = implode(', ', array_keys($result->getClasses()));
            throw new ExecutionFailedException("Class '$className' was not found in file '$fileName'. Available classes: $availableClasses");
        }

        $class = $result->getClass($className);

        if (!$class->hasMethod($methodName)) {
            throw new ExecutionFailedException("Method '$methodName' of class '$className' was not found in file '$fileName'.");
        }

        return $this->wrapLines($class->getMethod($methodName)->getSource());
    }

    private function findFunction(string $contents, CommandParams $params, string $fileName): string
    {
        $functionName = $params->getArgument(2);
        $result = $this->parser->parse($contents);

        if (!$result->hasFunction($functionName)) {
            throw new ExecutionFailedException("Function '$functionName' was not found in file '$fileName'.");
        }

        return $this->wrapLines($result->getFunction($functionName)->getSource());
    }
    
    private function findLines(string $contents, CommandParams $params): string
    {
        $lines = explode("\n", $contents);
        $range = $params->getThirdArgument();

        if (is_numeric($range)) {
            return $this->wrapLines(array_slice($lines, $range - 1, 1));
        }

        $matches = [];
        if (!preg_match('|(\d+)-(\d+)|', $range, $matches)) {
            throw new ExecutionFailedException("Line definition '$range' is not valid. Must be in format N or N-N.");
        }

        $from = min([$matches[1], $matches[2]]);
        $to = max([$matches[1], $matches[2]]);

        return $this->wrapLines(array_slice($lines, $from - 1, $to - $from + 1));
    }

    /**
     * @param array|string $lines
     *
     * @return string
     */
    private function wrapLines($lines): string
    {
        if (!is_array($lines)) {
            $lines = explode("\n", $lines);
        }

        $indented = array_map(function ($e) {
            return '    '.$e;
        }, $lines);

        return implode("\n", $indented);
    }
}
