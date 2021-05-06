<?php
declare(strict_types=1);


namespace Makao;


use Makao\Exception\CardDuplicationException;

class CardValidator
{
    /**
     * @param Card $activeCard
     * @param Card $newCard
     * @return bool
     *
     * @throws CardDuplicationException
     */
    public function valid(Card $activeCard, Card $newCard): bool
    {
        if ($activeCard === $newCard) {
            throw new CardDuplicationException($newCard);
        }

        return $activeCard->getColor() === $newCard->getColor()
            || $activeCard->getValue() === $newCard->getValue()
            || $newCard->getValue() === Card::VALUE_QUEEN
            || $activeCard->getValue() === Card::VALUE_QUEEN;
    }
}