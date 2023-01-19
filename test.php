<?php

declare(strict_types=1);

/**
 * This file is part of the Micro framework package.
 *
 * (c) Stanislau Komar <head.trackingsoft@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$array = [
    -1  => ['a,b,c'],
    10  => ['n,m,j'],
    3   => ['g', 'f', 'k'],
];
krsort($array);

foreach ($array as $k) {
    var_dump($k);
}