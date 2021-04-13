<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Table;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private  $gamseServiceUnderTest;

    /** @var MockObject | CardService $cardServiceMock */
    private  $cardServiceMock;

    protected function setUp(): void
    {
        $this->cardServiceMock = $this->createMock(CardService::class);
        $this->gamseServiceUnderTest = new GameService(new Table(), $this->cardServiceMock);
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

    /**
     * @throws \ReflectionException
     */
    public function testShouldCreateShuffledCardDeck()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
            ]
        );

        $shuffledCardCollection = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
            ]
        );

        $this->cardServiceMock->expects($this->once())
            ->method('createDeck')
            ->willReturn($cardCollection);

        $this->cardServiceMock->expects($this->once())
            ->method('shuffle')
            ->with($cardCollection)
            ->willReturn($shuffledCardCollection);

        // When
        /** @var Table $table */
        $table = $this->gamseServiceUnderTest->prepareCardDeck();
        // Then
        $this->assertCount(2, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
        $this->assertEquals($shuffledCardCollection, $table->getCardDeck());
    }
}