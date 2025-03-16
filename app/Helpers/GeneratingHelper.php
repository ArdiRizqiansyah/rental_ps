<?php

namespace App\Helpers;

use App\Models\Order;

class GeneratingHelper
{
    public static function setOrderCode()
    {
        $randomNumber = mt_rand(101010111, 999999999);
        // random 8 number
        $number = $randomNumber;

        while (Order::whereCode($number)->exists()) {
            $number = $randomNumber;
        }

        return $number;
    }
}