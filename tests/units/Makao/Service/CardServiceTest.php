<?php

declare(strict_types=1);


namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Service\CardService;
use Makao\Service\ShuffleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CardServiceTest extends TestCase
{
    /**
     * @var CardService
     */
    private CardService $cardServiceUnderTest;

    /**
     * @var MockObject | ShuffleService
     */
    private ShuffleService $shuffleServiceMock;

    protected function setUp(): void
    {
        $this->shuffleServiceMock = $this->createMock(ShuffleService::class);
        $this->cardServiceUnderTest = new CardService($this->shuffleServiceMock);
    }

    public function testShouldAllowCreateNewCardCollection()
    {
        // Given
        $cardService = $this->cardServiceUnderTest;
        // When
        $actual = $cardService->createDeck();
        // Then
        $this->assertInstanceOf(CardCollection::class, $actual);
        $this->assertCount(52, $actual);

        $i = 0;
        foreach (Card::values() as $value) {
            foreach (Card::colors() as $color) {
                $this->assertEquals($value, $actual[$i]->getValue());
                $this->assertEquals($color, $actual[$i]->getColor());
                ++$i;
            }
        }

        return $actual;
    }

    /**
     * @depends testShouldAllowCreateNewCardCollection
     * @param CardCollection $cardCollection
     */
    public function testShouldShuffleCardsInCardCollention(CardCollection $cardCollection)
    {
        // Given
        $this->shuffleServiceMock->expects($this->once())
            ->method('shuffle')
            ->willReturn( array_reverse($cardCollection->toArray()));
        // When
        $actual = $this->cardServiceUnderTest->shuffle($cardCollection);
        // Then
        $this->assertNotEquals($cardCollection, $actual);
        $this->assertEquals($cardCollection->pickCard(), $actual[51]);
    }

    public function testShouldPickFirstNoActionCardFromCollection()
    {
        // Given
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                $noActionCard
            ]
        );
        // When
        $actual = $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
        // Then
        $this->assertCount(7, $collection);
        $this->assertSame($noActionCard, $actual);
    }

    public function testShouldThrowCardNotFoundExceptionWhenPickFirstNoActionCardFromCollectionWithOnlyActionCards()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('No regular cards in collection');
        // Given
        $collection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            ]
        );
        // When
        $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
    }

    public function testShouldPickFirstNoActionCardFromCollectionAndMovePriviousActionCardsOnTheEnd()
    {
        // Given
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                $noActionCard,
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            ]
        );

        $expectCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            ]
        );

        // When
        $actual = $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
        // Then
        $this->assertCount(7, $collection);
        $this->assertSame($noActionCard, $actual);
        $this->assertEquals($expectCollection, $collection);
    }
}