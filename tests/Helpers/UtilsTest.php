<?php

namespace Tests\Helpers;

use App\Helpers\Utils;
use Tests\TestCase;

class UtilsTest extends TestCase
{
    public function string2nullDataProvider()
    {
        return [
            ['Строка', 'Строка'],
            ['', null],
            [0, 0],
        ];
    }

    /**
     * @test
     *
     * @dataProvider string2nullDataProvider
     *
     * @param string $value
     * @param string $expected
     */
    public function string2null(string $value, ?string $expected)
    {
        // act
        $result = Utils::string2null($value);

        // assert
        $this->assertEquals($expected, $result);
    }
}
