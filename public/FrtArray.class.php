<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2017/8/2
 * Time: 11:53
 */
 
class FrtArray
{
    /**
     * 在数组$aryA中的任意位置，插入数组$aryB
     *
     * @author chenbin
     * @param  array $aryA    原数组
     * @param  array $aryB    待插入数组
     * @param  int $key       待插入位置
     * @return array
     */
    public static function insertAry($aryA = [], $aryB = [], $key = 100)
    {
        if ($key >= count($aryA))
        {
            return array_merge($aryA, $aryB);
        }
        else
        {
            return array_merge(array_slice($aryA, 0, $key), $aryB, array_slice($aryA, $key));
        }
        
    }

    /**
     * 将对象转为数组
     *
     * @author chenbin
     * @param  $data       // 对象数据
     * @return array|mixed
     */
    public static function objToAry($data)
    {
        if (is_object($data))
        {
            $data = get_object_vars($data);
        }

        return is_array($data) ? json_decode(json_encode($data), true) : $data;
    }

}
