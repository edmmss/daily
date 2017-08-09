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
 
    /**
     * 读取xml为数组
     *
     * @author chenbin
     * @param string $filePath    // xml文件路径
     * @return array|bool|mixed   // 成功:返回读取后的数组, 失败:返回false
     */
    public static function xmlToAry($filePath = '')
    {
        if (empty($filePath) || !file_exists($filePath) || !is_file($filePath))
        {
            return false;
        }
        else
        {
            $fileStr = file_get_contents($filePath);
            $fileStr = str_replace('&', '＆', $fileStr);
            $var = simplexml_load_string($fileStr);

            if (is_object($var))
            {
                return self::objToAry($var);
            }
            else
            {
                return false;
            }
        }
    }
 
   /**
     * 获取数据某一列值
     * （通常我们会需要一个多维数组的某一键值列的值,而不需要其他列的值,这个时候你可以使用本函数）
     * （如果列的值为数组，则返回只是该列数组的值，键还是按照原始数组的）
     *
     * @author chenbin
     * @param  array $ary    // 原始数组
     * @param  $key          // 指定键名
     * @return array|bool|mixed|string
     */
    public static function getKeyAry($array = [], $key)
    {
        // 如果是一维数组,就直接返回
        if (isset($array[$key]))
        {
            return $array[$key];
        }
 
        // 如果是多维数组,就遍历
        $tmp = $reThing = '';
        if (!is_array($array))
        {
            return false;
        }
 
        foreach ($array as $val)
        {
            if (!is_array($val))
            {
                return false;
            }
 
            $tmp = self::getKeyAry($val, $key);
 
            if ($tmp !== false)
            {
                $reThing[] = $tmp;
            }
        }
 
        return !empty($reThing) ? $reThing : false;
    }
 
}
