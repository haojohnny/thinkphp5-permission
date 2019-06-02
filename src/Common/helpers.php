<?php

if (! function_exists('array_flatten')) {
    /**
     * @param $array
     * @param $depth
     * @return array
     */
    function array_flatten($array, $depth = INF)
    {
        $result = [];
        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } elseif ($depth === 1) {
                $result = array_merge($result, array_values($item));
            } else {
                $result = array_merge($result, array_flatten($item, $depth - 1));
            }
        }

        return $result;
    }
}
