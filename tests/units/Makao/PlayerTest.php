<?php
declare(strict_types=1);


namespace Makao;


use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{

    public function testShouldWritePlayerName()
    {
        // Given
        $player = new Player('Andy');
        // When
        ob_start();
        echo $player;
        $actual = ob_get_clean();
        // Then
        $this->assertEquals('Andy', $actual);
    }
    
    public function testShouldReturnPlayerCardCollection()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_HEART, Card::VALUE_JACK)
            ]
        );
        $player = new Player('Andy', $cardCollection);
        // When
        $actual = $player->getCards();
        // Then
        $this->assertSame($cardCollection, $actual);
    }
    
    public function testShouldAllowOlayerTakeCardFromDeck()
    {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_JACK);
        $cardCollection = new CardCollection([$card]);
        $player = new Player('Andy');
        // When
        $actual = $player->takeCards($cardCollection, 1)->getCards();
        // Then
        $this->assertCount(0, $cardCollection);
        $this->assertCount(1, $player->getCards());
        $this->assertSame($card, $actual[0]);
    }

    public function testShouldAllowPlayerTakeManyCardsFromCollectiont()
    {
        // Given
        $firstCard = new Card(Card::COLOR_HEART, Card::VALUE_JACK);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_THREE);
        $thirdCard = new Card(Card::COLOR_SPADE, Card::VALUE_TEN);
        $cardCollection = new CardCollection([$firstCard, $secondCard, $thirdCard]);
        $player = new Player('Andy');
        // When
        $actual = $player->takeCards($cardCollection, 2)->getCards();
        // Then
        $this->assertCount(1, $cardCollection);
        $this->assertCount(2, $actual);
        $this->assertSame($firstCard, $actual->pickCard());
        $this->assertSame($secondCard, $actual->pickCard());
        $this->assertSame($thirdCard, $cardCollection->pickCard());
    }

    public function testShouldAllowPickChosenCardFromPlayerCardCollection()
    {
        // Given
        $firstCard = new Card(Card::COLOR_HEART, Card::VALUE_JACK);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_THREE);
        $thirdCard = new Card(Card::COLOR_SPADE, Card::VALUE_TEN);
        $cardCollection = new CardCollection([$firstCard, $secondCard, $thirdCard]);

        $player = new Player('Andy', $cardCollection);

        // When
        $actual = $player->pickCard(2);
        // Then
        $this->assertSame($thirdCard, $actual);
    }
    
    public function testShouldAllowPlayerSaysMakao()
    {
        // Given
        $player = new Player('Andy');
        // When
        $actual = $player->sayMakao();
        // Then
        $this->assertEquals('Makao', $actual);
    }
    
    public function testShouldThrowCardNotFoundExceptionWhenPlayerTryPickCardByValueAndHasNotCorrectCardInHand()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('Player has not card with value 2');

        // Given
        $player = new Player('Andy');
        // When
        $player->pickCardByValue(Card::VALUE_TWO);
    }

    public function testShouldReturnPickCardByValueWhenPlayerHasCorrectCard()
    {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_TWO);
        $player = new Player('Andy', new CardCollection([$card]));
        // When
        $actual = $player->pickCardByValue(Card::VALUE_TWO);
        // Then
        $this->assertSame($card, $actual);
    }

    public function testShouldReturnFirstCardByPickCardByValueWhenPlayerHasMoreCorrectCard()
    {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_TWO);
        $nextCard = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $player = new Player('Andy', new CardCollection([$card, $nextCard]));
        // When
        $actual = $player->pickCardByValue(Card::VALUE_TWO);
        // Then
        $this->assertSame($card, $actual);
    }

    public function testShouldReturnTrueWhenPlayerCanPlayRound()
    {
        // Given
        $player = new Player('Andy');
        // When
        $actual = $player->canPlayRound();
        // Then
        $this->assertTrue($actual);
    }
    
    public function testShouldReturnFalseWhenPlayerCanNotPlayRound()
    {
        // Given
        $player = new Player('Andy');
        // When
        $player->addRoundToSkip();
        // Then
        $this->assertFalse($player->canPlayRound());
    }
    
    public function testShouldSkipmanyRoundsAndBackToPlayerAfter()
    {
        // Given
        $player = new Player('Andy');
        // When & Then
        $this->assertTrue($player->canPlayRound());

        $player->addRoundToSkip(2);
        $this->assertFalse($player->canPlayRound());
        $this->assertSame(2, $player->getRoundToSkip());

        $player->skipRound();
        $this->assertFalse($player->canPlayRound());
        $this->assertSame(1, $player->getRoundToSkip());

        $player->skipRound();
        $this->assertTrue($player->canPlayRound());
        $this->assertSame(0, $player->getRoundToSkip());
    }
    
}