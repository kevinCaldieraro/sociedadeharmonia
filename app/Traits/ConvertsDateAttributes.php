<?php

namespace App\Traits;

use Carbon\Carbon;

trait ConvertsDateAttributes
{
    protected function convertToDatabaseDate($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d') : null;
    }
}