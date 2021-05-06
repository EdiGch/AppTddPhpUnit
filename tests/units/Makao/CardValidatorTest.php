<?php
declare(strict_types=1);


namespace Makao;


use Makao\Exception\CardDuplicationException;
use PHPUnit\Framework\TestCase;

class CardValidatorTest extends TestCase
{

    /**
     * @var CardValidator
     */
    private CardValidator $cardValidatorUnderTest;

    protected function setUp(): void
    {
        $this->cardValidatorUnderTest = new CardValidator();
    }

    public function cardsProvider()
    {
        return [
            'Return True When Valid Cards With The Same Colors' => [
                new Card(Card::COLOR_HEART,Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART,Card::VALUE_FIVE),
                true
            ],
            'Return False When Valid Cards With Different Colors And Values' => [
                new Card(Card::COLOR_SPADE,Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART,Card::VALUE_FIVE),
                false
            ],
            'Return True When Valid Cards With The Same Values' => [
                new Card(Card::COLOR_SPADE,Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART,Card::VALUE_FOUR),
                true
            ],
            'Queens for all' => [
                new Card(Card::COLOR_SPADE,Card::VALUE_TEN),
                new Card(Card::COLOR_HEART,Card::VALUE_QUEEN),
                true
            ],
            'All for Queens' => [
                new Card(Card::COLOR_SPADE,Card::VALUE_TEN),
                new Card(Card::COLOR_HEART,Card::VALUE_QUEEN),
                true
            ]

        ];
    }

    /**
     * @dataProvider cardsProvider
     * @param Card $activeCard
     * @param Card $newCard
     * @param bool $expected
     */
    public function testShouldValidCards(Card $activeCard, Card $newCard, bool $expected)
    {
        // When
        $actual = $this->cardValidatorUnderTest->valid($activeCard, $newCard);
        // Then
        $this->assertSame($expected, $actual);
    }
    
    public function testShouldThrowCardDuplicationExceptionWhenValidCardsAreTheSame()
    {
        // Expect
        $this->expectException(CardDuplicationException::class);
        $this->expectExceptionMessage('Valid card get the same cards: 5 spade');
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FIVE);
    
        // When
        $this->cardValidatorUnderTest->valid($card, $card);
    }
}