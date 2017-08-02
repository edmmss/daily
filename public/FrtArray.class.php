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
    
    

}
