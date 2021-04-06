<?php
declare(strict_types=1);

namespace Tests\Makao;

use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{

    public function testShouldCreateEmptyTable()
    {
        // Given
        $expected = 0;
        $tableUnderTest = new Table();
        // When
        $actual = $tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);

    }


    public function testShouldAddOnePlayerToTable()
    {
        // Given
        $expected = 1;
        $tableUnderTest = new Table();
        $player = new Player();
        // When
        $tableUnderTest->addPlayer($player);
        $actual = $tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldReturnCountWhenIAddManyPlayers()
    {
        // Given
        $expected = 2;
        $tableUnderTest = new Table();
        // When
        $tableUnderTest->addPlayer(new Player());
        $tableUnderTest->addPlayer(new Player());
        $actual = $tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);
    }
    
    public function testShouldThrowTooManyPlayersAtTheTableExceptionWhenITryAddMoreThanFourPlayers()
    {
        // Expect
        $this->expectException(TooManyPlayersAtTheTableException::class);
        $this->expectExceptionMessage('Max capacity is 4 players');
        // Given
        $tableUnderTest = new Table();
        // When
        $tableUnderTest->addPlayer(new Player());
        $tableUnderTest->addPlayer(new Player());
        $tableUnderTest->addPlayer(new Player());
        $tableUnderTest->addPlayer(new Player());
        $tableUnderTest->addPlayer(new Player());
    }

}