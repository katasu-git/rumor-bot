<?php

function sortDesk($array, $sortVal) {
    foreach ((array) $array as $key => $value) {
        $sort[$key] = $value[$sortVal];
    }
    array_multisort($sort, SORT_DESC, $array);
    return $array;
}