<?php

declare(strict_types=1);

namespace Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;

class CardService
{

    /**
     * @return CardCollection
     */
    public function createDeck(): CardCollection
    {
        $deck = new CardCollection();

        foreach (Card::values() as $value) {
            foreach (Card::colors() as $color) {
                $deck->add(new Card($color, $value));
            }
        }

        return $deck;
    }


}