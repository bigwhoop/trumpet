<?php declare(strict_types=1);

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
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function multiply(int $a, int $b): int
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
function addNumbers(int $a, int $b): int
{
    return $a + $b;
}

echo '98 + 3 = '.addNumbers(98, 3);
