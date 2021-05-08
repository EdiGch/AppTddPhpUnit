<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Exception\GameException;
use Makao\Table;

class GameService
{
    const MINIMAL_PLAYERS = 2;
    /**
     * @var Table
     */
    private Table $table;

    private bool $isStarted = false;

    /**
     * @var CardService
     */
    private CardService $cardService;

    public function __construct(Table $table, CardService $cardService)
    {
        $this->table = $table;
        $this->cardService = $cardService;
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function addPlayers(array $players): self
    {
        foreach ($players as $player){
            $this->table->addPlayer($player);
        }
        return $this;
    }

    public function startGame(): void
    {
        $this->validateBeforeStartGame();
        $this->isStarted = true;
    }

    public function prepareCardDeck(): Table
    {
        $cardCollection = $this->cardService->createDeck();
        $cardDeck = $this->cardService->shuffle($cardCollection);

        return $this->table->addCardCollectionToDeck($cardDeck);
    }

    private function validateBeforeStartGame(): void
    {
        if (0 === $this->table->getCardDeck()->count()) {
            throw new GameException('Prepare card deck before game start');
        }

        if (self::MINIMAL_PLAYERS > $this->table->countPlayers()) {
            throw new GameException('You need minimum ' . self::MINIMAL_PLAYERS . ' players to start game');
        }
    }
}