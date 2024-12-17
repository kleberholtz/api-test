<?php

namespace App\goHoltz\Utils\Time;

trait Time
{
    private ITime $time_provider;

    public function getTimeProvider(ITime $provider = null): ITime
    {
        if (!isset($this->time_provider)) {
            if (!\is_null($provider)) {
                return $this->time_provider = $provider;
            } else {
                return $this->time_provider = new LocalTime();
            }
            // throw new \Exception("No suitable Time provider found");
        } else {
            return $this->time_provider;
        }
    }
}
