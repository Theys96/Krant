<?php

namespace App\Model;

class FilterMode
{
    /**
     * @var int alle stukjes
     */
    public const ALL = 0;

    /**
     * @var int alle stukjes die klaar zijn
     */
    public const FINISHED = 1;

    /**
     * @var int alle stukjes die klaar zijn en nog niet nagekeken door de gebruiker
     */
    public const CHECKABLE = 2;

    /**
     * @var int alle stukjes die klaar zijn en vaak genoeg nagekeken
     */
    public const CHECKED = 3;

    public static function isValidValue(int $value): bool
    {
        return $value >= 0 && $value <= 3;
    }
}
