<?php

namespace App\goHoltz\Utils\Time;

interface ITime
{
    /**
     * @return int the current timestamp according to this provider
     */
    public function getTime();
}