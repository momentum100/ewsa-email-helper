<?php

namespace App\Services;

class CostManager
{
    private $totalCost = 0;

    public function addCost($cost)
    {
        $this->totalCost += $cost;
    }

    public function getTotalCost()
    {
        return $this->totalCost;
    }

    public function formatCost($cost)
    {
        return number_format((float)$cost, 4);
    }
}