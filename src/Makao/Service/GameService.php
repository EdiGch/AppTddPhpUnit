<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Exception\CardNotFoundException;
use Makao\Exception\GameException;
use Makao\Service\CardSelector\CardSelectorInterface;
use Makao\Table;

class GameService
{
    const MINIMAL_PLAYERS = 2;
    const COUNT_START_PLAYER_CARDS = 5;
    /**
     * @var Table
     */
    private Table $table;

    private bool $isStarted = false;

    /**
     * @var CardService
     */
    private CardService $cardService;

    /**
     * @var CardSelectorInterface
     */
    private CardSelectorInterface $cardSelector;

    /**
     * @var CardActionService
     */
    private CardActionService $cardActionService;

    public function __construct(Table $table, CardService $cardService, CardSelectorInterface $cardSelector, CardActionService $cardActionService)
    {
        $this->table = $table;
        $this->cardService = $cardService;
        $this->cardSelector = $cardSelector;
        $this->cardActionService = $cardActionService;
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
        $cadDeck = $this->table->getCardDeck();

        try {
            $this->isStarted = true;

            $card = $this->cardService->pickFirstNoActionCard($this->table->getCardDeck());
            $this->table->addPlayedCard($card);

            foreach ($this->table->getPlayers() as $player) {
                $player->takeCards($cadDeck, self::COUNT_START_PLAYER_CARDS);
            }
        } catch (\Exception $exception) {
            throw new GameException('The game needs help!', $exception);
        }

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

    public function playRound(): void
    {
        $player = $this->table->getCurrentPlayer();

        if (!$player->canPlayRound()) {
            $this->table->finishRound();
            return;
        }

        try {
            $card = $this->cardSelector->chooseCard(
                $player,
                $this->table->getPlayedCards()->getLastCard(),
                $this->table->getPlayedCardColor()
            );

            $this->table->addPlayedCard($card);
            $this->cardActionService->afterCard($card);
        } catch (CardNotFoundException $e) {
            $player->takeCards($this->table->getCardDeck());
            $this->table->finishRound();
        }
    }
}