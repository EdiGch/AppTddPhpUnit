<?php
declare(strict_types=1);

namespace Tests\Makao;

use Makao\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{

    public function testShouldCreateEmptyTable()
    {
        // Given
        $expected = 0;
        $testUnderTest = new Table();
        // When
        $actual = $testUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);

    }


}