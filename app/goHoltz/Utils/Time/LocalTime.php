<?php

namespace App\goHoltz\Utils\Time;

class LocalTime implements ITime
{
    /**
     * @return int the current timestamp
     */
    public function getTime()
    {
        return \time();
    }
}
