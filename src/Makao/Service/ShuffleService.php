<?php
declare(strict_types=1);


namespace Makao\Service;

/**
 * @codeCoverageIgnore
 */
class ShuffleService
{
    public function shuffle(array $data)
    {
        shuffle($data);
        return $data;
    }
}