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

    public function __construct(array $cards = [])
    {
        $this->cards = $cards;
    }

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

    public function addCollection(CardCollection $cardCollection): self
    {
        foreach (clone $cardCollection as $card) {
            $this->add($card);
        }

        return $this;
    }

    public function pickCard(int $index = self::FIRST_CARD_INDEX)
    {
        if (empty($this->cards)) {
            throw new CardNotFoundException('You can not pic card from empty CardCollection');
        }

        $pickedCard = $this->cards[$index];
        $this->offsetUnset($index);
        $this->cards = array_values($this->cards);

        return $pickedCard;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->offsetExists($this->position);
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

    public function offsetSet($offset, $value): void
    {
        throw new MethodNotAllowedException('You can not add card to collection as array. Use addCard() method!');
    }

    public function offsetUnset($offset): void
    {
        unset($this->cards[$offset]);
    }

    public function toArray(): array
    {
        return $this->cards;
    }

    public function getLastCard(): Card
    {
        if (0 === $this->count()) {
            throw new CardNotFoundException('You can not get last card from empty CardCollection');
        }
        return $this->offsetGet($this->count() - 1);
    }

}