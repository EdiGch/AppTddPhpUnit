<?php
declare(strict_types=1);

namespace Makao;

use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;

class Table
{
    private $players = [];

    public function countPlayers(): int
    {
        return count($this->players);
    }

    public function addPlayer(Player $player): void
    {
        if($this->countPlayers() > Player::MAX_COUNT_PLAYERS - 1)
        {
            throw new TooManyPlayersAtTheTableException('Max capacity is 4 players');
        }
        $this->players[] = $player;
    }
}