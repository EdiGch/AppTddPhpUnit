<?php
declare(strict_types=1);


namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class CardActionServiceTest extends TestCase
{

    /**
     * @var Player
     */
    private Player $player1;
    /**
     * @var Player
     */
    private Player $player2;
    /**
     * @var Player
     */
    private Player $player3;
    /**
     * @var Table
     */
    private Table $table;
    /**
     * @var CardActionService
     */
    private CardActionService $cardActionServiceUnderTest;

    protected function setUp(): void
    {
        // Given
        $playedCard = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT)
            ]
        );

        $deck = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
                new Card(Card::COLOR_SPADE, Card::VALUE_SIX),
                new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
                new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
                new Card(Card::COLOR_SPADE, Card::VALUE_NINE),
                new Card(Card::COLOR_SPADE, Card::VALUE_TEN),

                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                new Card(Card::COLOR_HEART, Card::VALUE_SIX),
                new Card(Card::COLOR_HEART, Card::VALUE_SEVEN),
                new Card(Card::COLOR_HEART, Card::VALUE_EIGHT),
                new Card(Card::COLOR_HEART, Card::VALUE_NINE),
                new Card(Card::COLOR_HEART, Card::VALUE_TEN),

                new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
                new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
                new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
                new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
                new Card(Card::COLOR_CLUB, Card::VALUE_TEN),

                new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_SIX),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_SEVEN),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_EIGHT),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_NINE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_TEN),
            ]
        );

        $this->player1 = new Player('TOM');
        $this->player2 = new Player('MARK');
        $this->player3 = new Player('ANDRU');

        $this->table = new Table($deck, $playedCard);

        $this->table->addPlayer($this->player1);
        $this->table->addPlayer($this->player2);
        $this->table->addPlayer($this->player3);

        $this->cardActionServiceUnderTest = new CardActionService($this->table);
    }

    public function testShouldGiveNextPlayerTwoCardsWhenCardTwoWasDropped()
    {
        // ==
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(2, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerForCardsWhenCardTwoWasDroppedAndSecondPlayerHesCardTwoToDefend()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );

        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(4, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveFirstPlayerSixCardsWhenCardTwoWasDroppedAndSecondAndThirdPlayerHesCardTwoToDefend()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );

        $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO)
        );

        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(6, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveSecondPlayerEightCardsWhenCardTwoWasDroppedAndSecondAndAllPlayersHesCardTwoToDefend()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        $this->player1->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );

        $this->player2->getCards()->add(
            new Card(Card::COLOR_DIAMOND, Card::VALUE_TWO)
        );

        $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO)
        );

        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(8, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(0, $this->player1->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    //TODO Dla karty numer 3

    public function testShouldSkipRoundForNextPlayerWhenCardDourWasDropped()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);

        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR)
        );

        $this->player3->getCards()->add(
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR)
        );

        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
        $this->assertEquals(2, $this->player1->getRoundToSkip());
        $this->assertFalse($this->player1->canPlayRound());
        $this->assertTrue($this->player2->canPlayRound());
        $this->assertTrue($this->player3->canPlayRound());
    }
    
    public function testShouldSkipManyRoundsForNextPlayerWhenCardFourWasDroppedAndNextPlayersHaveCardsFourToDefend()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldRequestCardByValueWhenCardJackWasDroppedAndTakeCardForEachPlayerWhenTheyNotHaveRequstedCard()
    {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_HEART, Card::VALUE_JACK);

        $requestedCard = new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN);
        $this->player1->getCards()->add($requestedCard);
        // When
        $this->cardActionServiceUnderTest->afterCard($card, $requestValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldRequestCardByValueWhenCardJackWasDroppedAndPickCardsForEachPlayerWhenTheyHaveRequstedCard()
    {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_HEART, Card::VALUE_JACK);

        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN));
        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_SEVEN));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN));

        // When
        $this->cardActionServiceUnderTest->afterCard($card, $requestValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldAllowDropManyRequestedCardsByValueWhenPlayerDropJackCard()
    {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_HEART, Card::VALUE_JACK);

        $requestedCard = new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN);
        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN));
        $this->player1->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_SEVEN));
        $this->player1->getCards()->add($requestedCard);

        // When
        $this->cardActionServiceUnderTest->afterCard($card, $requestValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveNextPlayerFiveCardsWhenCardKingHeartWasDropped()
    {
        // Given
        $card = new Card( Card::COLOR_HEART, Card::VALUE_KING);
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(5, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGivePreviousPlayerFiveCardsWhenCardKingSpadeWasDropped()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_KING,);
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(5, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveCurrentPlayerTenCardsWhenCardKingHeartWasDroppedAndNextPlayerHasKingSpadeToDefence()
    {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_KING);
        $this->player2->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_KING));
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(10, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveCurrentPlayerTenCardsWhenCardKingSpadeWasDroppedAndPreviousPlayerHasKingHeartToDefence()
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_KING);
        $this->player3->getCards()->add(new Card( Card::COLOR_HEART, Card::VALUE_KING));
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(10, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldNotRunAnyActionForOtherKings()
    {
        // Given
        $card = new Card(Card::COLOR_DIAMOND, Card::VALUE_KING);
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldNotRunAnyActionForAnyNoActionCard()
    {
        // Given
        $card = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);
        // When
        $this->cardActionServiceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldChangeColorToPlayOnTableAfterCardAce()
    {
        // Given
        $requestColor = Card::COLOR_HEART;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_ACE);
        // When & Then
        $this->assertEquals(Card::COLOR_SPADE, $this->table->getPlayedCardColor());
        $this->cardActionServiceUnderTest->afterCard($card, $requestColor);
        $this->assertEquals(Card::COLOR_HEART, $this->table->getPlayedCardColor());
    }

}