<?php
declare(strict_types=1);

namespace Tests\Makao;

use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    /**
     * @var object|Table
     */
    private object $tableUnderTest;

    public function setUp(): void
    {
        $this->tableUnderTest = new Table();
    }

    public function testShouldCreateEmptyTable()
    {
        // Given
        $expected = 0;
        // When
        $actual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);

    }


    public function testShouldAddOnePlayerToTable()
    {
        // Given
        $expected = 1;
        $player = new Player();
        // When
        $this->tableUnderTest->addPlayer($player);
        $actual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldReturnCountWhenIAddManyPlayers()
    {
        // Given
        $expected = 2;
        // When
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $actual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $actual);
    }
    
    public function testShouldThrowTooManyPlayersAtTheTableExceptionWhenITryAddMoreThanFourPlayers()
    {
        // Expect
        $this->expectException(TooManyPlayersAtTheTableException::class);
        $this->expectExceptionMessage('Max capacity is 4 players');
        // When
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
    }

}