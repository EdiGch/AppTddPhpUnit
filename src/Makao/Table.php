<?php
declare(strict_types=1);

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;

class Table
{
    public const MAX_PLAYERS = 4;

    private $players = [];

    private $currentIndexPlayer = 0;

    /**
     * @var CardCollection
     */
    private CardCollection $cardDack;

    /**
     * @var CardCollection
     */
    private CardCollection $playedCard;

    /**
     * @var string
     */
    public ?string $playedCardColor;

    public function __construct(CardCollection $cardDack = null, CardCollection $playedCard = null)
    {
        $this->cardDack = $cardDack ?? new CardCollection();
        $this->playedCard = $playedCard ?? new CardCollection();
        $this->playedCardColor = null;
        if (!is_null($playedCard)) {
            $this->changePlayedCardColor($this->playedCard->getLastCard()->getColor());
        }
    }

    public function countPlayers(): int
    {
        return count($this->players);
    }

    public function addPlayer(Player $player): void
    {
        if($this->countPlayers() > self::MAX_PLAYERS - 1)
        {
            throw new TooManyPlayersAtTheTableException(self::MAX_PLAYERS);
        }
        $this->players[] = $player;
    }

    public function getPlayedCards(): CardCollection
    {
        return $this->playedCard;
    }

    public function getCardDeck(): CardCollection
    {
        return $this->cardDack;
    }

    public function addCardCollectionToDeck(CardCollection $cardCollection): self
    {
        $this->cardDack->addCollection($cardCollection);
            return $this;
    }

    public function getCurrentPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer];
    }

    public function getNextPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer + 1] ?? $this->players[0];
    }

    public function getPreviousPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer - 1] ?? $this->players[$this->countPlayers() - 1];
    }

    public function finishRound(): void
    {
        if (++$this->currentIndexPlayer === $this->countPlayers()) {
            $this->currentIndexPlayer = 0;
        }
    }

    public function backRound(): void
    {
        if (--$this->currentIndexPlayer < 0) {
            $this->currentIndexPlayer = $this->countPlayers() -1;
        }
    }

    public function getPlayedCardColor(): string
    {
        if (!is_null($this->playedCardColor)) {
            return $this->playedCardColor;
        }
        throw new CardNotFoundException('No played cards on the table yet!');
    }

    public function addPlayedCard(Card $card): self
    {
        $this->playedCard->add($card);
        $this->changePlayedCardColor($card->getColor());

        return $this;
    }

    public function changePlayedCardColor(string $color): self
    {
        $this->playedCardColor = $color;

        return $this;
    }

    public function addPlayedCards(CardCollection $cards): self
    {
        foreach ($cards as $card) {
            $this->addPlayedCard($card);
        }
        return $this;
    }
}