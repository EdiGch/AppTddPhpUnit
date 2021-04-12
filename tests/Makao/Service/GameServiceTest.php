<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Player;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private  $gamseServiceUnderTest;

    protected function setUp(): void
    {
        $this->gamseServiceUnderTest = new GameService();
    }

    public function testShouldReturnFalseWhenGameIsNotStarted()
    {
        // When
        $actual = $this->gamseServiceUnderTest->isStarted();
        // Then
        $this->assertFalse($actual);
    }
    
    public function testShouldInitNewGameWithEmptyTable()
    {
        // When
        $table = $this->gamseServiceUnderTest->getTable();
        // Then
        $this->assertSame(0, $table->countPlayers());
        $this->assertCount(0, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
    }

    public function testShouldAddPlayersToTheTable()
    {
        // Given
        $players = [
            new Player('Andy'),
            new Player('Tom'),
            new Player('Greg'),
        ];

        // When
        $actual = $this->gamseServiceUnderTest->addPlayers($players)->getTable();
        // Then
        $this->assertSame(3, $actual->countPlayers());
    }
    
    public function testShouldReturnTrueWhenGameIsStarted()
    {
        // When
        $this->gamseServiceUnderTest->startGame();
        $actual = $this->gamseServiceUnderTest->isStarted();
        // Then
        $this->assertTrue($actual);
    }
}