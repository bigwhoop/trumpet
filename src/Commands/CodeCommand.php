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

use Bigwhoop\Trumpet\CodeParsing\PHP\PHPCodeParser;

/**
 * !code file.php
 * !code file.php line 12
 * !code file.php lines 12 16
 * !code file.php function my_function_name
 * !code file.php class My\Class\Name
 * !code file.php method My\Class\Name myMethod.
 */
class CodeCommand implements Command
{
    /** @var PHPCodeParser */
    private $parser;

    /**
     * @param PHPCodeParser $parser
     */
    public function __construct(PHPCodeParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return 'code';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CommandParams $params, CommandExecutionContext $executionContext)
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
                return $this->findClass($contents, $params);

            case 'abstract':
                return $this->findAbstract($contents, $params);

            case 'method':
                return $this->findMethod($contents, $params);

            case 'function':
                return $this->findFunction($contents, $params);

            case 'line':
                return $this->findLines($contents, $params);

            default:
                return $this->wrapLines($contents);
        }
    }

    /**
     * @param string        $contents
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function findClass($contents, CommandParams $params)
    {
        $className = $params->getArgument(2);
        $result = $this->parser->parse($contents);

        if (!$result->hasClass($className)) {
            $availableClasses = implode(', ', array_keys($result->getClasses()));
            throw new ExecutionFailedException("Class '$className' was not found in file '$fileName'. Available classes: $availableClasses");
        }

        return $this->wrapLines($result->getClass($className)->getSource());
    }

    /**
     * @param string        $contents
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function findAbstract($contents, CommandParams $params)
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

    /**
     * @param string        $contents
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function findMethod($contents, CommandParams $params)
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

    /**
     * @param string        $contents
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function findFunction($contents, CommandParams $params)
    {
        $functionName = $params->getArgument(2);
        $result = $this->parser->parse($contents);

        if (!$result->hasFunction($functionName)) {
            throw new ExecutionFailedException("Function '$functionName' was not found in file '$fileName'.");
        }

        return $this->wrapLines($result->getFunction($functionName)->getSource());
    }

    /**
     * @param string        $contents
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function findLines($contents, CommandParams $params)
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
    private function wrapLines($lines)
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
