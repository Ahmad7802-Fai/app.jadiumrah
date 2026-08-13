<?php

if (!function_exists('bc_label')) {
    function bc_label($str) {
        return ucwords(str_replace('-', ' ', $str));
    }
}
