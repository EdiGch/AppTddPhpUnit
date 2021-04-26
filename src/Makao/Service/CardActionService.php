<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Card;
use Makao\Exception\CardNotFoundException;
use Makao\Table;

class CardActionService
{
    /**
     * @var Table
     */
    private Table $table;
    /**
     * @var int
     */
    private int $cardToGet = 0;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function afterCard(Card $card): void
    {
        $this->table->finishRound();
        switch ($card->getValue()){
            case Card::VALUE_TWO:
                $this->cardTwoAction();
                break;
            default:
                break;
        }



    }

    private function cardTwoAction(): void
    {
        $this->cardToGet +=2;
        $player = $this->table->getCurrentPlayer();
        try{
            $card = $player->pickCardByValue(Card::VALUE_TWO);
            $this->table->getPlayedCards()->add($card);
            $this->table->finishRound();
            $this->cardTwoAction();
        } catch (CardNotFoundException $exception) {
            $this->playerTakeCards($this->cardToGet);
        }
    }

    private function playerTakeCards(int $count): void
    {
        $this->table->getCurrentPlayer()->takeCards($this->table->getCardDeck(), $count);
        $this->table->finishRound();
    }


}