<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2018/3/2
 * Time: 9:53
 */

/**
 * Class cetQuery
 *
 * 通过学信息模拟提交抓取四六级数据
 */
class cetQuery
{

    /**
     * 获取学信网的验证码url
     *
     * @author chenbin
     * @param $number // 准考证号
     * @return bool|mixed
     */
    public function getCaptchaImgUrl($number)
    {
        $cookFileName = __FILE__ . '/clockInCetScoresQueryCookie.txt';
        $url = "http://cache.neea.edu.cn/Imgs.do?c=CET&ik={$number}&t=" . (mt_rand() / mt_getrandmax());
        $header = [
            'Accept:*/*',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4,ja;q=0.2,de;q=0.2,zh-TW;q=0.2,hu;q=0.2,ca;q=0.2',
            'DNT:1',
            'Host:cache.neea.edu.cn',
            'Proxy-Connection:keep-alive',
            'Referer:http://cet.neea.edu.cn/cet/',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36',
        ];

        if (!is_file($cookFileName) || (time() - filemtime($cookFileName) > 300)) {
            $customExpansion[CURLOPT_COOKIEJAR] = $cookFileName;
        } else {
            $customExpansion[CURLOPT_COOKIEFILE] = $cookFileName;
        }
        $customExpansion[CURLOPT_HTTPHEADER] = $header;

        $result = $this->curlGet($url, 3, $customExpansion);

        return $result['operation'] ?
            str_replace('");', '', str_replace('result.imgs("', '', $result['dataInfo'])) : false;
    }

    /**
     * 获取四六级的查询数据
     *
     * 查询四六级数据前需要通过准考证查询对应的二维码，准考证和二维码必须一起对应传到这个接口
     *
     * @author chenbin
     * @param $name // 姓名
     * @param $number // 准考证
     * @param $captcha // 验证码
     * @return array|string
     */
    public function queryByNameAndNumber($name, $number, $captcha)
    {
        $cookFileName = __FILE__ . '/clockInCetScoresQueryCookie.txt';
        // code 参数可能每次的四六级的开放查询都会不一样，之前的是CET4_171_DANGCI和CET6_171_DANGCI
        $code = $number[9] == 1 ? 'CET4_172_DANGCI' : 'CET6_172_DANGCI';
        $postData = [
            'data' => "{$code},{$number},{$name}",
            'v'    => $captcha,
        ];

        $url = "http://cache.neea.edu.cn/cet/query";
        $header = [
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4,ja;q=0.2,de;q=0.2,zh-TW;q=0.2,hu;q=0.2,ca;q=0.2',
            'Cache-Control:max-age=0',
//            'Content-Length:75',
            'Content-Type:application/x-www-form-urlencoded',
            'DNT:1',
            'Host:cache.neea.edu.cn',
            'Origin:http://cet.neea.edu.cn',
            'Proxy-Connection:keep-alive',
            'Referer:http://cet.neea.edu.cn/cet/',
            'Upgrade-Insecure-Requests:1',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36',
        ];

        if (is_file($cookFileName)) {
            $customExpansion[CURLOPT_COOKIEFILE] = $cookFileName;
        }
        $customExpansion[CURLOPT_HTTPHEADER] = $header;
        $result = $this->curlPost($url, $postData, 3, $customExpansion);

        if (!$result['operation']) {
            return '目前四六级查分系统繁忙，请稍后重试';
        }

        preg_match('/{.*}/', $result['dataInfo'], $returnString);
        if (!$returnString) {
            return '目前四六级查分系统繁忙，请稍后重试';
        }

        if (!$returnString || strpos($returnString[0], 'error') !== false) {
            if (strpos($returnString[0], '验证码错误') !== false) {
                return '抱歉，验证码错误！';
            }

            return '无法找到对应的分数，请确认你输入的准考证号及姓名无误';
        }
        $result = str_replace(['{', '}'], '', $returnString[0]);
        $result = explode(',', $result);

        return [
            'name'                   => str_replace(["n:'", "'"], '', $result[1]),
            'school'                 => str_replace(["x:'", "'"], '', $result[2]),
            'type'                   => $number[9] == 1 ? '英语四级' : '英语六级',
            'examRegistrationNumber' => str_replace(["z:'", "'"], '', $result[0]),
            'total'                  => intval(str_replace("s:", '', $result[3])),
            'listening'              => intval(str_replace("l:", '', $result[5])),
            'read'                   => intval(str_replace("r:", '', $result[6])),
            'writing'                => intval(str_replace("w:", '', $result[7])),
        ];
    }

    /**
     * CURL GET方式提交数据
     *
     * @author chenbin
     * @param $url // 请求url
     * @param int $second // 超时时间
     * @param array $customExpansion // 添加了自定义参数，方便各自需求扩展, demo: $customExpansion[CURLOPT_TIMEOUT] = 30;
     * @return array
     */
    public function curlGet($url, $second = 30, $customExpansion = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // 遍历各种自定义参数设置
        if (!empty($customExpansion)) {
            foreach ($customExpansion as $option => $value) {
                // 参数名和参数值都不为空的时候才配置该项
                if (!empty($option) && !empty($value)) {
                    curl_setopt($ch, $option, $value);
                }
            }
        }

        $result = curl_exec($ch);
        $curlErrorNo = curl_errno($ch);
        $curlErrorInfo = curl_errno($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if ($curlErrorNo == 0) {
            return [
                'operation' => true,
                'dataInfo'  => $result,
            ];
        } else {
            return [
                'operation'     => false,
                'curlErrorNo'   => $curlErrorNo,
                'curlErrorInfo' => $curlErrorInfo,
                'curlInfo'      => $curlInfo,
            ];
        }
    }

    /**
     * 通过curl的post方式提交获取数据
     *
     * @author chenbin
     * @param $url // 请求url
     * @param $postData // post的数据
     * @param int $second // 超时时间
     * @param array $customExpansion // 添加了自定义参数，方便各自需求扩展, demo: $customExpansion[CURLOPT_TIMEOUT] = 30;
     * @return array
     */
    public function curlPost($url, $postData, $second = 30, $customExpansion = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // 遍历各种自定义参数设置
        if (!empty($customExpansion)) {
            foreach ($customExpansion as $option => $value) {
                // 参数名和参数值都不为空的时候才配置该项
                if (!empty($option) && !empty($value)) {
                    curl_setopt($ch, $option, $value);
                }
            }
        }

        $result = curl_exec($ch);
        $curlErrorNo = curl_errno($ch);
        $curlErrorInfo = curl_errno($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if ($curlErrorNo == 0) {
            return [
                'operation' => true,
                'dataInfo'  => $result,
            ];
        } else {
            return [
                'operation'     => false,
                'curlErrorNo'   => $curlErrorNo,
                'curlErrorInfo' => $curlErrorInfo,
                'curlInfo'      => $curlInfo,
            ];
        }
    }
}