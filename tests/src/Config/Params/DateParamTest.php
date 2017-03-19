<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Config\Params;

use Bigwhoop\Trumpet\Config\Date;
use Bigwhoop\Trumpet\Config\Params\DateParam;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Tests\TestCase;

class DateParamTest extends TestCase
{
    /**
     * @Inject
     *
     * @var DateParam
     */
    private $param;

    /**
     * @return array
     */
    public function invalidDates()
    {
        return [
            ['2015-05'],
            ['05-08-2015'],
            ['05/08/2015'],
            ['2015/05/08'],
            ['05.08.2015'],
        ];
    }

    public function testTimestamp()
    {
        $this->assertDate(new Date(new \DateTime('2015-05-08 00:00:00')), 1431115764);
    }

    public function testDateFormat()
    {
        $this->assertDate(new Date(new \DateTime('2015-05-08 00:00:00')), '2015-05-08');
    }

    /**
     * @param string $date
     * @dataProvider invalidDates
     * @expectedException \Bigwhoop\Trumpet\Config\ConfigException
     * @expectedExceptionMessage Dates must be a string in the format YYYY-MM-DD.
     */
    public function testInvalidDateFormat(string $date)
    {
        $this->param->parse($date, new Presentation());
    }

    /**
     * @param Date         $expected
     * @param string|array $paramValue
     *
     * @throws \Bigwhoop\Trumpet\Config\ConfigException
     */
    private function assertDate(Date $expected, $paramValue)
    {
        $presentation = new Presentation();
        $this->param->parse($paramValue, $presentation);
        $actual = $presentation->date;

        $this->assertEquals($expected, $actual);
    }
}
