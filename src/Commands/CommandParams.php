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

class CommandParams
{
    /** @var string */
    private $argumentSeparator = ' ';

    /** @var string */
    private $params = '';

    /**
     * @param string $params
     */
    public function __construct($params = '')
    {
        $this->params = $params;
    }

    /**
     * @param string $separator
     */
    public function setArgumentSeparator($separator)
    {
        $this->argumentSeparator = $separator;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param null|string $default
     *
     * @return string
     */
    public function getFirstArgument($default = null)
    {
        return $this->getArgument(0, $default);
    }

    /**
     * @return bool
     */
    public function hasFirstArgument()
    {
        return $this->hasArgument(0);
    }

    /**
     * @param null|string $default
     *
     * @return string
     */
    public function getSecondArgument($default = null)
    {
        return $this->getArgument(1, $default);
    }

    /**
     * @return bool
     */
    public function hasSecondArgument()
    {
        return $this->hasArgument(1);
    }

    /**
     * @param null|string $default
     *
     * @return string
     */
    public function getThirdArgument($default = null)
    {
        return $this->getArgument(2, $default);
    }

    /**
     * @return bool
     */
    public function hasThirdArgument()
    {
        return $this->hasArgument(2);
    }

    /**
     * @param int         $n
     * @param null|string $default
     *
     * @return string
     */
    public function getArgument($n, $default = null)
    {
        $args = $this->getArguments();

        return array_key_exists($n, $args) ? $args[$n] : $default;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return str_getcsv($this->params, $this->argumentSeparator, '"');
    }

    /**
     * @param int $n
     *
     * @return bool
     */
    public function hasArgument($n)
    {
        return array_key_exists($n, $this->getArguments());
    }
}
