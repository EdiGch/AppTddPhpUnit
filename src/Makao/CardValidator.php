<?php
declare(strict_types=1);


namespace Makao;


class CardValidator
{
    public function valid(Card $activeCard, Card $newCard): bool
    {
        return $activeCard->getColor() === $newCard->getColor() || $activeCard->getValue() === $newCard->getValue();
    }
}