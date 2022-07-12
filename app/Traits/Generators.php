<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait Generators
{
    public function generateReference()
    {
        $noOfCharacters = 16;
        $setOfCharactersToSelectFrom = '123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ';
        return substr(str_shuffle(str_repeat($setOfCharactersToSelectFrom, $noOfCharacters)), 0, $noOfCharacters);
    }
}
