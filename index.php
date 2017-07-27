<?php

namespace URI_Command;

if (!function_exists('URI_Command\version')) {
    require __DIR__.'/autoload.php';
}

if (is_admin()) {
    new Admin;
}

new Filters;
