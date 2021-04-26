<?php
declare(strict_types=1);

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;

class Player
{
    const MAKAO = 'Makao';
    private string $name;
    /**
     * @var CardCollection
     */
    private CardCollection $cardCollection;
    private int $roundToSkip = 0;

    public function __construct(string $name, CardCollection $cardCollection = null)
    {
        $this->name = $name;
        $this->cardCollection = (is_null($cardCollection)) ? new CardCollection() : $cardCollection;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCards(): CardCollection
    {
        return $this->cardCollection;
    }

    public function pickCard(int $index = 0): Card
    {
        return $this->getCards()->pickCard($index);
    }

    public function takeCards(CardCollection $cardCollection, int $count = 1): self
    {
        for ($i = 0; $i < $count; $i++) {
            $this->cardCollection->add($cardCollection->pickCard());
        }
        return $this;
    }

    public function sayMakao(): string
    {
        return self::MAKAO;
    }

    public function pickCardByValue(string $valueTwo): Card
    {
        foreach ($this->cardCollection as $index => $card) {
            if ($valueTwo === $card->getValue()) {
                return $this->pickCard($index);
            }
        }

        throw new CardNotFoundException('Player has not card with value ' . $valueTwo);
    }

    public function getRoundToSkip(): int
    {
        return $this->roundToSkip;
    }

    public function canPlayRound(): bool
    {
        return $this->getRoundToSkip() === 0;
    }

    public function addRoundToSkip(int $rounds = 1): self
    {
        $this->roundToSkip += $rounds;
        return $this;
    }

    public function skipRound(): self
    {
        --$this->roundToSkip;
        return $this;
    }

}