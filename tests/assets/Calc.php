<?php

namespace My\Test\Ns;

class Calc
{
    /**
     * @return Calc
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function add($a, $b)
    {
        return $a + $b;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function multiply($a, $b)
    {
        return $a * $b;
    }
}

/**
 * @param int $a
 * @param int $b
 *
 * @return int
 */
function addNumbers($a, $b)
{
    return $a + $b;
}

echo "98 + 3 = ".addNumbers(98, 3);
