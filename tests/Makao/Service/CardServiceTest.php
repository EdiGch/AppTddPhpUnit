<?php

declare(strict_types=1);


namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
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
    }

    public function testShouldShuffleCardsInCardCollention()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_QUEEN);

        $this->shuffleServiceMock->expects($this->once())
            ->method('shuffle')
            ->willReturn([$secondCard, $firstCard]);


        $cardCollection = new CardCollection();
        $cardCollection
            ->add($firstCard)
            ->add($secondCard);
        // When
        /** @var CardCollection $actual */
        $actual = $this->cardServiceUnderTest->shuffle($cardCollection);
        // Then
        $this->assertSame($secondCard, $actual->pickCard());
        $this->assertSame($firstCard, $actual->pickCard());

    }
}