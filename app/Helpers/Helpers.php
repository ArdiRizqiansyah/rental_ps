<?php

if (! function_exists('rupiahFormat')) {
    /**
     * Format number to rupiah
     *
     * @param  int  $number
     * @return string
     */
    function rupiahFormat($number)
    {
        return 'Rp '.number_format($number, 0, ',', '.');
    }
}