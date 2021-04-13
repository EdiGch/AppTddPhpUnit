<?php
declare(strict_types=1);

namespace Tests\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;
use Makao\Service\CardService;
use Makao\Service\GameService;
use Makao\Table;
use PHPUnit\Framework\MockObject\MockObject;
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
        $player = new Player('TOM');
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
        $this->tableUnderTest->addPlayer(new Player('Mark'));
        $this->tableUnderTest->addPlayer(new Player('Mark'));
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
        $this->tableUnderTest->addPlayer(new Player('TOM'));
        $this->tableUnderTest->addPlayer(new Player('MARK'));
        $this->tableUnderTest->addPlayer(new Player('ANDRU'));
        $this->tableUnderTest->addPlayer(new Player('MAT'));
        $this->tableUnderTest->addPlayer(new Player('GREG'));
    }
    
    public function testShouldReturnEmptyCardCollectionForPlayedCard()
    {
        // When
        $actual = $this->tableUnderTest->getPlayedCards();

        // Then
        $this->assertInstanceOf(CardCollection::class, $actual);
        $this->assertCount(0, $actual);
    }

    public function testShouldPutCardDeckOnTable()
    {
        // Given
        $cards = new CardCollection(
            [
                new Card(CarD::COLOR_DIAMOND, Card::VALUE_SEVEN)
            ]
        );
        // When
        $table = new Table($cards);
        $actual = $table->getCardDeck();
        // Then
        $this->assertSame($cards, $actual);
    }

    public function testShouldAddCardCollectionToCardDeckOnTable()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
            ]
        );
        // When
        $actual = $this->tableUnderTest->addCardCollectionToDeck($cardCollection);
        // Then
        $this->assertEquals($cardCollection, $actual->getCardDeck());
    }

}