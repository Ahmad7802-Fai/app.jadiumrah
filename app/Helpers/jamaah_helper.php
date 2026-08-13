<?php

if (!function_exists('statusKeberangkatanLabel')) {
    function statusKeberangkatanLabel($status)
    {
        return match ($status) {
            'menunggu'   => 'Menunggu',
            'persiapan'  => 'Persiapan',
            'berangkat'  => 'Berangkat',
            default      => 'Menunggu',
        };
    }
}
