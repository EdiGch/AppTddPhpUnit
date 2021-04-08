<?php

declare(strict_types=1);

namespace Makao\Collection;

use Makao\Card;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\MethodNotAllowedException;
use PHPUnit\Util\Exception;

class CardCollection implements \Countable, \Iterator, \ArrayAccess
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

        $pickedCard = $this->cards[self::FIRST_CARD_INDEX];
        $this->offsetUnset(self::FIRST_CARD_INDEX);
        $this->cards = array_values($this->cards);

        return $pickedCard;
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

    public function offsetExists($offset): bool
    {
        return isset($this->cards[$offset]);
    }

    public function offsetGet($offset): Card
    {
        return $this->cards[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new MethodNotAllowedException('You can not add card to collection as array. Use addCard() method!');
    }

    public function offsetUnset($offset)
    {
        unset($this->cards[$offset]);
    }

    public function shuffle()
    {
        shuffle($this->cards);
    }


}