<?php

namespace App\Config;

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';

}

function dump_die($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;

}
