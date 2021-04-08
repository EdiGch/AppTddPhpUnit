<?php

declare(strict_types=1);

namespace Makao\Collection;

use Makao\Card;
use Makao\Exception\CardNotFoundException;

class CardCollection implements \Countable, \Iterator
{
    const FIRST_CARD_INDEX = 0;

    /**
     * @var array
     */
    private array $cards = [];

    /**
     * @var int
     */
    private int $position = 0;

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

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->cards[$this->position]);
    }

    /**
     * @return Card
     */
    public function current(): ?Card
    {
        return $this->cards[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function rewind(): void
    {
        $this->position = self::FIRST_CARD_INDEX;
    }


}