<?php
declare(strict_types=1);

namespace Makao\Exception;

use Throwable;

class CardNotFoundException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}