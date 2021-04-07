<?php

declare(strict_types=1);

namespace Makao\Collection;

use Makao\Card;
use Makao\Exception\CardNotFoundException;

class CardCollection implements \Countable
{
    /**
     * @var array
     */
    private array $cards = [];

    /**
     * @return int|void
     */
    public function count(): int
    {
        return count($this->cards);
    }

    /**
     * @param Card $card
     * @return $this
     */
    public function add(Card $card): self
    {
        $this->cards[] = $card;

        return $this;
    }


    public function pickCard()
    {
        if (empty($this->cards)) {
            throw new CardNotFoundException('You can not pic card from empty CardCollection');
        }
    }

}