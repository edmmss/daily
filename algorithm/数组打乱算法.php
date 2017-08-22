<?php

/**
 * className
 *
 * @author chenbin
 * @param  $array      // 需要处理的数组
 * @return array
 */
function arrayUpset($array)
{
    $tmpAry = [];
    $count = count($array);

    for ($i = 0; $i < $count; $i++)
    {
        // 这里减一是因为数组从键从0开始
        $num = mt_rand(0, $count - $i - 1);
        $tmpAry[] = $array[$num];
        unset($array[$num]);
        $array = array_values($array);
    }

    return $tmpAry;
}

$arr = range(1, 20, 1);
$data = arrayUpset($arr);
var_dump($data);
