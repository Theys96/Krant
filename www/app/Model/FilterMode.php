<?php

namespace App\Model;

class FilterMode
{
    //alle stukjes
    const ALL = 0;
    
    //alles stukjes die klaar zijn
    const FINISHED = 1;

    //alle stukjes die klaar zijn en nog niet nagekeken door de gebruiker
    const CHECKABLE = 2;

    //alle stukjes die klaar zijn en vaak genoeg nagekeken
    const CHECKED = 3;

    static function validValue(int $value): int {
        return $value >= 0 && $value <= 3 ? $value : 1;
    }
}