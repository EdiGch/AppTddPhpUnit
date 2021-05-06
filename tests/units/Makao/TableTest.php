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
    
    public function testShouldReturnCurrentPlayer()
    {
        // Given
        $player1 = new Player('TOM');
        $player2 = new Player('MARK');
        $player3 = new Player('ANDRU');
        $player4 = new Player('MAT');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        $this->tableUnderTest->addPlayer($player4);

        // When
        $actual = $this->tableUnderTest->getCurrentPlayer();
        // Then
        $this->assertSame($player1, $actual);
    }

    public function testShouldReturnNextPlayer()
    {
        // Given
        $player1 = new Player('TOM');
        $player2 = new Player('MARK');
        $player3 = new Player('ANDRU');
        $player4 = new Player('MAT');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        $this->tableUnderTest->addPlayer($player4);

        // When
        $actual = $this->tableUnderTest->getNextPlayer();
        // Then
        $this->assertSame($player2, $actual);
    }

    public function testShouldReturnPreviousPlayer()
    {
        // Given
        $player1 = new Player('TOM');
        $player2 = new Player('MARK');
        $player3 = new Player('ANDRU');
        $player4 = new Player('MAT');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        $this->tableUnderTest->addPlayer($player4);

        // When
        $actual = $this->tableUnderTest->getPreviousPlayer();
        // Then
        $this->assertSame($player4, $actual);
    }
    
    public function testShouldSwitchCurrentPlayerWhenRoundFinished()
    {
        // Given
        $player1 = new Player('TOM');
        $player2 = new Player('MARK');
        $player3 = new Player('ANDRU');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
    
        // When & Then
        $actual = $this->tableUnderTest->getPreviousPlayer();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player2, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player3, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

    }


    public function testShouldAllowBackRoundOnTable()
    {
        // Given
        $player1 = new Player('TOM');
        $player2 = new Player('MARK');
        $player3 = new Player('ANDRU');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);

        // When & Then
        $actual = $this->tableUnderTest->getPreviousPlayer();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player2, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->backRound();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

    }

}