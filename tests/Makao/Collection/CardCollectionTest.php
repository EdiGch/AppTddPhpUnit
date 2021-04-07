<?php

declare(strict_types=1);

namespace Tests\Makao\Collection;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
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
        $card = new Card();
        // When
        $this->cardCollection->add($card);
        // Then
        $this->assertCount(1, $this->cardCollection);
    }

    public function testShouldAddNewCardsInChainToCardCollection()
    {
        // Given
        $firstCard = new Card();
        $secondCard = new Card();
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
}