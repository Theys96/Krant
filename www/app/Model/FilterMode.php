<?php

namespace App\Model;

class FilterMode
{
    /**
     * @var int alle stukjes
     */
    const ALL = 0;
    
    /** 
     * @var int alle stukjes die klaar zijn
    */
    const FINISHED = 1;

    /** 
     * @var int alle stukjes die klaar zijn en nog niet nagekeken door de gebruiker
    */
    const CHECKABLE = 2;

    /** 
     * @var int alle stukjes die klaar zijn en vaak genoeg nagekeken
    */
    const CHECKED = 3;

    /** 
     * @param int $value
     * @return bool
    */
    public static function isValidValue(int $value): bool {
        return $value >= 0 && $value <= 3;
    }
}