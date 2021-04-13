<?php
declare(strict_types=1);


namespace Makao;


use PHPUnit\Framework\TestCase;

class CardValidatorTest extends TestCase
{
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
        // Given
        $cardValidator = new CardValidator();
        // When
        $actual = $cardValidator->valid($activeCard, $newCard);
        // Then
        $this->assertSame($expected, $actual);
    }
}