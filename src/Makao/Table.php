<?php
declare(strict_types=1);

namespace Makao;

use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;

class Table
{
    public const MAX_PLAYERS = 4;

    private $players = [];

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
}