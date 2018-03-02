<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2018/3/2
 * Time: 17:28
 */

class sendSmsMingChuan
{

    private $_userId;
    private $_password;
    private $_phones;
    private $_msg;
    private $_sendTime;

    const CURLOPT_TIMEOUT = 20; // curl超时时间
    const MAX_PHONE_NUMBER = 200; // 发送的最大手机号码数量

    /**
     *
     * sendSmsMingChuan constructor.
     * @param string $userId // 账号
     * @param string $password // 密码
     * @param array $phones // 手机号数组
     * @param string $msg // 内容
     * @param int $sendTime // 发送时间
     */
    public function __construct(string $userId, string $password, array $phones, string $msg, int $sendTime = 0)
    {
        $this->_userId = $userId;
        $this->_password = $password;
        $this->_phones = $phones;
        $this->_msg = $msg;
        $this->_sendTime = $sendTime;
    }

    /**
     * 发送短信
     *
     * 其实就是模拟提交相关数据
     *
     * @author chenbin
     * @return array
     * @throws Exception
     */
    public function sendSms()
    {
        // 需要签名，名传签名就在这后面拼接
        $signatureName = "【测试】";
        $msg = $this->_msg . $signatureName;

        $url = 'http://211.147.244.114:9801/CASServer/SmsAPI/SendMessage.jsp';

        if (count($this->_phones) > self::MAX_PHONE_NUMBER) {
            throw new \Exception('名传最多一次发送200个手机号');
        } else {
            $phones = implode(',', $this->_phones);
        }

        $postData = [
            'userid'      => $this->_userId,
            'password'    => $this->_password,
            'destnumbers' => $phones,
            'msg'         => $msg,
        ];

        if ($this->_sendTime) {
            $postData['sendtime'] = $this->_sendTime;
        }

        $res = $this->curlPost($url, $postData);
        $res = (array)simplexml_load_string($res);
        $res = current($res);
        $jsonRes = json_encode($res);

        if ($res['return'] == 0) {
            return [
                'error'  => 0,
                'result' => $jsonRes,
            ];
        } else {
            return [
                'error'  => 1,
                'result' => $jsonRes,
            ];
        }
    }

    /**
     * 模拟提交
     *
     * @author chenbin
     * @param string $url
     * @param array $postData
     * @return mixed
     */
    public function curlPost(string $url, array $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);   //超时时间只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($postData) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

}

// 这里是具体的调用，账号，密码就不提供了，可以去名传无线申请
$phones = [];
$sendSmsMingChuan = new sendSmsMingChuan('xxxxx', 'xxxxxxxx', $phones, 'test');
$result = $sendSmsMingChuan->sendSms();

var_dump($result);
