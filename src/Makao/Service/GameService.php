<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Table;

class GameService
{
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
        $this->isStarted = true;
    }

    public function prepareCardDeck(): Table
    {
        $cardCollection = $this->cardService->createDeck();
        $cardDeck = $this->cardService->shuffle($cardCollection);

        return $this->table->addCardCollectionToDeck($cardDeck);
    }
}