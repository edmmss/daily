<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2017/6/20
 * Time: 11:54
 */

/**
 * Date 工具
 */

/**
[示例]
返回当前的日期如 "2017-06-20"
echo QP_Date_Date::getDate();
比较两个时间
echo FrtDate::compareTiem('2017-06-20', '2017-06-21');
日期加天数： "2017-02-20"+6 = "2017-02-26"
echo FrtDate::dateAddDay("2017-02-20", 6);
日期减天数： "2017-10-20"-10 = "2017-10-10"
echo FrtDate::dateDecDay("2017-10-20", 10);
日期减日期: "2017-10-20" - "2017-10-10" = 10
echo FrtDate::dateDiff('2017-10-20', '2017-10-10');
时间相减
echo FrtDate::timeDiff('2017-10-20 10:00:00', '2005-10-20  08:00:00');
*/
class Date{

	/**
	 * 防止类实例化或被复制
	 *
	 */
	private function __construct() {}
	private function __clone() {}

	/**
	* 得到当前时间
	*
	* @param string $fmt :日期格式
	* @param int $time :时间，默认为当前时间
	* @return string
	*/
	static public function getDate($fmt = 'Y-m-d H:i:s', $time = null)
	{
		$times = $time ? $time : time();
		return date($fmt, $times);
	}

	/**
	* 计算日期天数差
	*
	* @param string $Date1 :如 "2017-10-20" 或 "20171020"
	* @param string $Date2 :如 "2017-10-10" 或 "20171010"
	* @return int
	* 例子:"2017-10-20" - "2017-10-10" = 10
	*/
	static public function dateDiff($Date1, $Date2)
	{
		$DateList1 = explode("-", date("Y-m-d", strtotime($Date1)));
		$DateList2 = explode("-", date("Y-m-d", strtotime($Date2)));
		$d1   = mktime(0, 0, 0, $DateList1[1], $DateList1[2], $DateList1[0]);
		$d2   = mktime(0, 0, 0, $DateList2[1], $DateList2[2], $DateList2[0]);
		$Days = round(abs($d1 - $d2) / 3600 / 24);
		return $Days;
	}

	/**
	* 计算日期加天数后的日期
	*
	* @param string $date :如 "2017-10-20"
	* @param int $day  :如 6
	* @param string $fmt 时间格式,默认为'Y-m-d'
	* @return string
	* 例子:2017-9-25" + 6 = "2017-10-01"
	*/
	static public function dateAddDay($date, $day, $fmt = 'Y-m-d')
	{
		$daystr = "+$day day";
		$dateday = date($fmt, strtotime($daystr, strtotime($date)));
		return $dateday;
	}

	/**
	* 计算日期加天数后的日期
	*
	* @param string $date :如 "2017-10-20"
	* @param int $day  :如 10
	* @param string $fmt 时间格式,默认为'Y-m-d'
	* @return string
	* 例子:"2017-10-20" - 10 = "2017-10-10'
	*/
	static public function dateDecDay($date, $day, $fmt = 'Y-m-d')
	{
		$daystr = "-$day day";
		$dateday = date($fmt, strtotime($daystr, strtotime($date)));
		return $dateday;
	}

	/**
	* 比较两个时间
	*
	* @param string $timeA :格式如 "2017-10-12" 或 "2017-10-12 12:30" 或 "2017-10-12 12:30:50"
	* @param string $timeB :同上
	* @return int   0:$timeA = $timeB
	*              -1:$timeA < $timeB
	*               1:$timeA > $timeB
	*/
	static public function compareTiem($timeA, $timeB)
	{
		$a = strtotime($timeA);
		$b = strtotime($timeB);
		if ($a > $b)
        {
            return 1;
        }
		else if($a == $b)
        {
            return 0;
        }
		else
        {
            return -1;
        }
	}

	/**
	* 计算时间a减去时间b的差值
	*
	* @param string $timeA :格式如 "2017-10-12" 或 "2017-10-12 12:30" 或 "2017-10-12 12:30:50"
	* @param string $timeB :同上
	* @return int   实数的小时,如"2.3333333333333"小时
	*/
	static public function timeDiff($timeA, $timeB)
	{
		$a = strtotime($timeA);
		$b = strtotime($timeB);
		$c = abs($a - $b);
		$c = $c / 3600;
		return $c;
	}

    /**
     * 查询某一天所在的星期一到星期天的日期，或者所在月的1号到最后一天的日期
     *
     * @author zhangyoutian
     * @param string $date 查询日期，格式如 "2017-10-12"
     * @param string $type  获取某一天的星期一到星期天的日期【week】，获取某一天当月1号到最后一天的日期【month】,获取当天开始和结束时间【day】
	 * @param string $fmt 时间格式,默认为'Y-m-d'
     * @return array   array('sDate'=>'', 'eDate'=>'')
     */
    static public function getDateRange($date, $type = 'week', $fmt = 'Y-m-d')
    {
        $sDate = $eDate = $date;
        $dateStr = strtotime($date);

        if ($type == 'week')
        {
            $weekDate = date ( 'D', $dateStr );
            $sDate = $weekDate == 'Mon' ? date($fmt, strtotime($date)) : date ($fmt, strtotime ("last Monday", $dateStr));
            $eDate = $weekDate == 'Sun' ? date($fmt, strtotime($date)) : date ($fmt, strtotime ("next Sunday", $dateStr));
        }
        else if ($type == 'month')
        {
            $tmpDate = date(substr($fmt, 0, -1), $dateStr);
            $dayNum = date('t', $dateStr);
            $sDate = $tmpDate . '01';
            $eDate = $tmpDate . $dayNum;
        }
        return ['sDate'=>$sDate, 'eDate'=>$eDate];
    }

    /**
     * 将秒数转为天时分秒
     *
     * @param int $second 要被转化的秒数
     * @return string 转后格式的时间
     */
    public static function secToDhms($second)
    {
    	$d = floor($second / 86400);
		$tmp = $second % 86400;
		$h = floor($tmp / 3600);
		$tmp %= 3600;
		$m = floor($tmp /60);
		$s = $tmp % 60;
		return ($d ? $d.'天':'').($h ? $h.'小时':'').($m ? $m.'分':'').$s.'秒';
    }
}
