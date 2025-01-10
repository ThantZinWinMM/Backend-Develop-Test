<?php

namespace App\Services\InternetServiceProvider;

class Wifi extends InternetServiceProviderAbstract implements InternetServiceProviderInterface
{
    protected string $operator = 'wifi';

    protected int $month = 0;

    protected int $monthlyFees = 100;

    public function setMonth(int $month)
    {
        $this->month = $month;
    }

    public function calculateTotalAmount(): float|int
    {
        return $this->month * $this->monthlyFees;
    }
}
