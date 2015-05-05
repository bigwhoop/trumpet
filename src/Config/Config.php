<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Config;

use Bigwhoop\Trumpet\Config\Params\Param;
use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /** @var Param[] */
    private $params = [];

    /**
     * @param string $name
     * @param Param  $param
     */
    public function setParam($name, Param $param)
    {
        $this->params[$name] = $param;
    }

    /**
     * @param string $path
     *
     * @return Presentation
     *
     * @throws ConfigException
     * @throws InvalidArgumentException
     */
    public function readTrumpetFile($path)
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException("Trumpet file '$path' must exist and be readable.");
        }

        $yaml = file_get_contents($path);

        try {
            $data = Yaml::parse($yaml, true, true);
        } catch (\Exception $e) {
            throw new ConfigException($e->getMessage(), 0, $e);
        }

        $presentation = new Presentation();

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->params)) {
                throw new ConfigException("Trumpet file contains a key '$key' which is not supported.");
            }

            $this->params[$key]->parse($value, $presentation);
        }

        return $presentation;
    }
}
