<?php
declare(strict_types=1);

namespace Makao\Exception;

use Throwable;

class TooManyPlayersAtTheTableException extends \RuntimeException
{
    public function __construct(int $maxPlayers, $code = 0, Throwable $previous = null)
    {
        $message = 'Max capacity is ' . $maxPlayers . ' players';
        parent::__construct($message, $code, $previous);
    }
}