<?php
declare(strict_types=1);


namespace Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardDuplicationException;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\GameException;
use Makao\Player;
use Makao\Service\CardSelector\CardSelectorInterface;
use Makao\Table;
use phpDocumentor\Reflection\Types\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private  $gameServiceUnderTest;

    /** @var MockObject | CardService $cardServiceMock */
    private $cardServiceMock;
    /**
     * @var CardActionService|MockObject
     */
    private $actionServiceMock;
    /**
     * @var CardSelectorInterface|MockObject
     */
    private $cardSelectorMock;

    protected function setUp(): void
    {
        $this->cardSelectorMock = $this->getMockForAbstractClass(CardSelectorInterface::class);
        $this->actionServiceMock = $this->createMock(CardActionService::class);
        $this->cardServiceMock = $this->createMock(CardService::class);
        $this->gameServiceUnderTest = new GameService(
            new Table(),
            $this->cardServiceMock,
            $this->cardSelectorMock,
            $this->actionServiceMock
        );
    }

    public function testShouldReturnFalseWhenGameIsNotStarted()
    {
        // When
        $actual = $this->gameServiceUnderTest->isStarted();
        // Then
        $this->assertFalse($actual);
    }
    
    public function testShouldInitNewGameWithEmptyTable()
    {
        // When
        $table = $this->gameServiceUnderTest->getTable();
        // Then
        $this->assertSame(0, $table->countPlayers());
        $this->assertCount(0, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
    }

    public function testShouldAddPlayersToTheTable()
    {
        // Given
        $players = [
            new Player('Andy'),
            new Player('Tom'),
            new Player('Greg'),
        ];

        // When
        $actual = $this->gameServiceUnderTest->addPlayers($players)->getTable();
        // Then
        $this->assertSame(3, $actual->countPlayers());
    }
    
    public function testShouldReturnTrueWhenGameIsStarted()
    {
        // Given
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(
            new CardCollection(
                [
                    new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                    new Card(Card::COLOR_HEART, Card::VALUE_THREE),
                    new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                    new Card(Card::COLOR_HEART, Card::VALUE_JACK),
                    new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
                    new Card(Card::COLOR_HEART, Card::VALUE_KING),
                    new Card(Card::COLOR_HEART, Card::VALUE_ACE),

                    new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
                    new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
                    new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
                    new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
                    new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
                    new Card(Card::COLOR_SPADE, Card::VALUE_KING),
                    new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

                    new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                    new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                    new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                    new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                    new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                    new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                    new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                ]
            )
        );

        $this->gameServiceUnderTest->addPlayers(
            [
                new Player('Andy'),
                new Player('Tom'),
                new Player('Greg'),
            ]
        );

        // When
        $this->gameServiceUnderTest->startGame();
        $actual = $this->gameServiceUnderTest->isStarted();
        // Then
        $this->assertTrue($actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testShouldCreateShuffledCardDeck()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
            ]
        );

        $shuffledCardCollection = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
            ]
        );

        $this->cardServiceMock->expects($this->once())
            ->method('createDeck')
            ->willReturn($cardCollection);

        $this->cardServiceMock->expects($this->once())
            ->method('shuffle')
            ->with($cardCollection)
            ->willReturn($shuffledCardCollection);

        // When
        /** @var Table $table */
        $table = $this->gameServiceUnderTest->prepareCardDeck();
        // Then
        $this->assertCount(2, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
        $this->assertEquals($shuffledCardCollection, $table->getCardDeck());
    }

    public function testShouldThrowExceptionWhenStartGameWithoutCardDeck()
    {
        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Prepare card deck before game start');
        // When
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldThrowExceptionWhenStartGameWithoutMinimalPlayers()
    {
        // Given
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(
            new CardCollection(
                [
                    new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
                ]
            )
        );
        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('You need minimum 2 players to start game');
        // When
        $this->gameServiceUnderTest->startGame();
    }


    public function testShouldChooseNoActionCardAsFirstPlayedCardWhenStartGame()
    {
        // Given
        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);

        $collection = new CardCollection(
            [
                new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                $noActionCard,
                new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                new Card(Card::COLOR_HEART, Card::VALUE_THREE),
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_JACK),
                new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
                new Card(Card::COLOR_HEART, Card::VALUE_KING),
                new Card(Card::COLOR_HEART, Card::VALUE_ACE),

                new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
                new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
                new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
                new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
                new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
                new Card(Card::COLOR_SPADE, Card::VALUE_KING),
                new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            ]
        );

        $this->gameServiceUnderTest->addPlayers(
            [
                new Player('Andy'),
                new Player('Max'),
            ]
        );

        $table->addCardCollectionToDeck($collection);
        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->willReturn($noActionCard);

        // When
        $this->gameServiceUnderTest->startGame();

        // Then
        $this->assertCount(1, $table->getPlayedCards());
        $this->assertSame($noActionCard, $table->getPlayedCards()->pickCard());
    }


    public function testShouldThrowGameExceptionWhenCardServiceThrowException()
    {
        // Expect
        $notFoundException = new CardNotFoundException('No regular cards in collection');
        $gameException = new GameException('The game needs help!', $notFoundException);

        $this->expectExceptionObject($gameException);
        $this->expectExceptionMessage('The game needs help! Issue: No regular cards in collection');

        // Given
        $table = $this->gameServiceUnderTest->getTable();
        $collection = new CardCollection([
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE)
        ]);
        $table->addCardCollectionToDeck($collection);

        $this->gameServiceUnderTest->addPlayers(
            [
                new Player('Andy'),
                new Player('Max'),
            ]
        );

        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willThrowException($notFoundException);
        // When
        $this->gameServiceUnderTest->startGame();
        
    }
    
    public function testShouldPlayersTakesFiveCardsFromDeckOnStartGame()
    {
        // Given
        $players = [
            new Player('Andy'),
            new Player('Tom'),
            new Player('Max'),
        ];
        $this->gameServiceUnderTest->addPlayers($players);

        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);

        $collection = new CardCollection(
            [
                new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                new Card(Card::COLOR_HEART, Card::VALUE_THREE),
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_JACK),
                new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
                new Card(Card::COLOR_HEART, Card::VALUE_KING),
                new Card(Card::COLOR_HEART, Card::VALUE_ACE),

                new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
                new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
                new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
                new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
                new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
                new Card(Card::COLOR_SPADE, Card::VALUE_KING),
                new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                $noActionCard
            ]
        );

        $table->addCardCollectionToDeck($collection);
        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);
        // When
        $this->gameServiceUnderTest->startGame();
        // Then
        foreach ($players as $player) {
            $this->assertCount(5, $player->getCards());
        }

    }

    public function testShouldChooseCardToPlayFromPlayerCardsAndPutItOneTheTable()
    {
        // Given
        $correctCard = new Card(Card::COLOR_HEART, Card::VALUE_FIVE);
        $player1 = new Player(
            'Max', new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
                $correctCard
            ]
        )
        );
        $player2 = new Player('Tom');
        $this->gameServiceUnderTest->addPlayers([$player1, $player2]);
        $table = $this->gameServiceUnderTest->getTable();

        $playedCard = new Card(Card::COLOR_HEART, Card::VALUE_SIX);
        $table->addPlayedCard($playedCard);

        $colection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
                new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
                new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_KING),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                new Card(Card::COLOR_HEART, Card::VALUE_THREE),
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_JACK),
                new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
                new Card(Card::COLOR_HEART, Card::VALUE_KING),
                new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            ]
        );
        $table->addCardCollectionToDeck($colection);

        $this->cardSelectorMock->expects($this->once())
            ->method('chooseCard')
            ->with($player1, $playedCard, $table->getPlayedCardColor() )
            ->willReturn($correctCard);

        $this->actionServiceMock->expects($this->once())
            ->method('afterCard')
            ->with($correctCard);

        // When
        $this->gameServiceUnderTest->playRound();
        // Then
        $this->assertSame($correctCard, $table->getPlayedCards()->getLastCard());
    }

}