<?php
declare(strict_types=1);

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;

class Table
{
    public const MAX_PLAYERS = 4;

    private $players = [];

    /**
     * @var CardCollection
     */
    private CardCollection $cardDack;

    /**
     * @var CardCollection
     */
    private CardCollection $playedCard;

    public function __construct(CardCollection $cardDack = null)
    {
        $this->cardDack = $cardDack ?? new CardCollection();
        $this->playedCard = new CardCollection();

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
}