<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2018/3/27
 * Time: 16:18
 */

class sendMail
{
    private $_apiUser;
    private $_apiKey;
    private $_from;
    private $_fromName;
    private $_to;
    private $_subject;
    private $_html;
    private $_respEmailId;

    private $_error;
    private $_errorMessage;
    private $_successMessage;

    const SEND_MAIL_API_URL = 'http://api.sendcloud.net/apiv2/mail/send';
    // apiUser和apiKey可以去sendCloud申请，from就用发信地址加上发信域名
    const SEND_MAIL_API_USER = '';
    const SEND_MAIL_API_KEY = '';
    const SEND_MAIL_FROM = '';
    const SEND_MAIL_FROM_NAME = '陈彬_邮件发送测试';
    const SEND_MAIL_RESP_EMAIL_ID = 'true';

    public function __construct()
    {
        $this->_apiUser = self::SEND_MAIL_API_USER;
        $this->_apiKey = self::SEND_MAIL_API_KEY;
        $this->_from = self::SEND_MAIL_FROM;
        $this->_fromName = self::SEND_MAIL_FROM_NAME;
        $this->_respEmailId = self::SEND_MAIL_RESP_EMAIL_ID;
    }

    /**
     * 设置发送内容
     *
     * @author chenbin
     * @param $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->_html = $html;

        return $this;
    }

    /**
     * 设置收邮件地址
     *
     * @author chenbin
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->_to = $to;

        return $this;
    }

    /**
     * 设置邮件主题
     *
     * @author chenbin
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * 获取相关配置参数
     *
     * @author chenbin
     * @return array
     */
    private function getParams()
    {
        return [
            'apiUser'     => $this->_apiUser,
            'apiKey'      => $this->_apiKey,
            'from'        => $this->_from,
            'fromName'    => $this->_fromName,
            'to'          => $this->_to,
            'subject'     => $this->_subject,
            'html'        => $this->_html,
            'respEmailId' => $this->_respEmailId,
        ];
    }

    /**
     * 发送
     *
     * @author chenbin
     * @return $this
     */
    public function send()
    {
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($this->getParams()),
            ]];

        $context = stream_context_create($options);
        $result = file_get_contents(self::SEND_MAIL_API_URL, FILE_TEXT, $context);

        $result = (object)json_decode($result, true);
        if ($result->result !== true) {
            $this->_error = true;
            $this->_errorMessage = $result->message;
        } else {
            $this->_error = false;
            $this->_successMessage = $result->message;
        }

        return $this;
    }

    /**
     * 判断是否发送失败
     *
     * @author chenbin
     * @return mixed
     */
    public function isError()
    {
        return $this->_error;
    }

    /**
     * 获取错误信息
     *
     * @author chenbin
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * 获取成功信息
     *
     * @author chenbin
     * @return mixed
     */
    public function getSuccessMessage()
    {
        return $this->_successMessage;
    }
}

$mail = '';
$sendMail = new sendMail();
$result = $sendMail
    ->setTo($mail)
    ->setSubject('陈彬测试哈哈哈')
    ->setHtml('hahahaha')
    ->send();

if ($sendMail->isError()) {
    var_dump($sendMail->getErrorMessage());
} else {
    var_dump($sendMail->getSuccessMessage());
}