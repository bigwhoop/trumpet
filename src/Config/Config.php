<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config;

use Bigwhoop\Trumpet\Config\Params\Param;
use Bigwhoop\Trumpet\Exceptions\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

final class Config
{
    /** @var Param[] */
    private $params = [];

    public function setParam(string $name, Param $param)
    {
        $this->params[$name] = $param;
    }
    
    public function readTrumpetFile(string $path): Presentation
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException("Trumpet file '$path' must exist and be readable.");
        }

        $yaml = file_get_contents($path);

        try {
            $data = Yaml::parse($yaml, true, true);
        } catch (\Throwable $t) {
            throw new ConfigException($t->getMessage(), 0, $t);
        }

        if (!is_array($data)) {
            throw new ConfigException("Trumpet file '$path' is invalid. It probably is empty.");
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
