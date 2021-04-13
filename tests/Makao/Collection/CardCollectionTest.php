<?php

declare(strict_types=1);

namespace Tests\Makao\Collection;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\MethodNotAllowedException;
use PHPUnit\Framework\TestCase;

class CardCollectionTest extends TestCase
{
    /**
     * @var CardCollection
     */
    private CardCollection $cardCollection;

    protected function setUp(): void
    {
        $this->cardCollection = new CardCollection();
    }

    public function testShouldReturnZeroOnEmptyCollection()
    {
        // Then
        $this->assertCount(0, $this->cardCollection);
    }

    public function testShouldAddNewCardToCardToCardCollection()
    {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        // When
        $this->cardCollection->add($card);
        // Then
        $this->assertCount(1, $this->cardCollection);
    }

    public function testShouldAddNewCardsInChainToCardCollection()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_QUEEN);
        // When
        $this->cardCollection
            ->add($firstCard)
            ->add($secondCard);
        // Then
        $this->assertCount(2, $this->cardCollection);
    }

    public function testShouldThrowCardNotFoundExceptionWhenITrryPickCardFromEmptyCardCollection()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pic card from empty CardCollection');

        // When
        $this->cardCollection->pickCard();
    }
    
    public function testShouldIteravleOnCardCollection()
    {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        // When & // Then
        $this->cardCollection->add($card);
        $this->assertTrue($this->cardCollection->valid());
        $this->assertSame($card, $this->cardCollection->current());
        $this->assertSame(0, $this->cardCollection->key());

        $this->cardCollection->next();
        $this->assertFalse($this->cardCollection->valid());
        $this->assertSame(1, $this->cardCollection->key());

        $this->cardCollection->rewind();
        $this->assertTrue($this->cardCollection->valid());
        $this->assertSame($card, $this->cardCollection->current());
        $this->assertSame(0, $this->cardCollection->key());

    }
    
    public function testShouldGetFirstCardFromCardCollectionAndRemoveThisCardFromDeck()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_QUEEN);
        $this->cardCollection
            ->add($firstCard)
            ->add($secondCard);

        // When
        $actual = $this->cardCollection->pickCard();

        // Then
        $this->assertCount(1, $this->cardCollection);
        $this->assertSame($firstCard, $actual);
        $this->assertSame($secondCard, $this->cardCollection[0]);
    }

    public function testShouldThrowCardNotFoundExceptionWhenIPickedAllCardFromCardCollection()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pic card from empty CardCollection');

        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_QUEEN);
        $this->cardCollection
            ->add($firstCard)
            ->add($secondCard);

        // When
        $actual = $this->cardCollection->pickCard();
        $this->assertSame($firstCard, $actual);

        $actual = $this->cardCollection->pickCard();
        $this->assertSame($secondCard, $actual);

        $this->cardCollection->pickCard();
    }

    public function testShouldThrowMethodNotAllowedExceptionWhenTouTryAddCardToCollectionAsArray()
    {
        // Expect
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('You can not add card to collection as array. Use addCard() method!');

        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);

        // When
        $this->cardCollection[] = $card;
    }

    public function testShouldReturnCollectionAsArray()
    {
        // Given
        $carts = [
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
        ];
        // When
        $actual = new CardCollection($carts);
        // Then
        $this->assertEquals($carts, $actual->toArray());
    }

    public function testShouldAddCardCollectionToCardCollection()
    {
        // Given
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
        ]);
        // When
        $actual = $this->cardCollection->addCollection($collection);
        // Then
        $this->assertEquals($collection, $actual);
    }
}