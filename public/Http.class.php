<?php
/**
 * Frt Http类
 *
 * @author chenbin
 * @encoding UTF-8
 * @package core
 */

class FrtHttp
{
    // curl信息
    public static $curlInfo = [];

    public function __construct()
    {

    }

	/**
     +----------------------------------------------------------
     * 下载文件
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param string $content  下载的内容
     * @param integer $expire  下载内容浏览器缓存时间
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws // ThinkExecption
     +----------------------------------------------------------
     */
    public static function download ( $showname,$content='',$expire=180 )
    {
        //发送Http Header信息 开始下载
        header("Pragma: public");
        header("Cache-control: max-age=".$expire);
        header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
        self::getFilename($showname);
        header("Content-Length: ".strlen($content));
        header("Content-type: application/octet-stream");
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );

        return $content;
    }

    /**
     +----------------------------------------------------------
     * 下载服务器上已有的文件
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $filePath 完整的文件路径(包括文件名)
     * @param string $filename 下载显示的文件名(默认使用第一个参数所带的文件名)
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws // ThinkExecption
     +----------------------------------------------------------
     */
    public static function downloadFile($filePath,$filename = '')
	{
	   if(file_exists($filePath))
	   {
	       $filename = !empty($filename) ? $filename : basename($filePath);

	       $fileSize=filesize($filePath);
	       //修改后的http下载报头 #兼容IE
	       header("Cache-Control:public");
		   header("Pragma:public");
		   header("Expires: 0");
		   header("Content-type:application/octet-stream");
		   header("Accept-Ranges: bytes");
	       self::getFilename($filename);
	       header ("Content-Length: " . $fileSize);
	       readfile($filePath);
	   }
	   else
	   {
	       return false;
	   }
	}

	/**
	 * 获取兼容各种浏览器编码格式的下载文件名
	 *
	 * 在下载文件时，由于各自终端编码格式不一直，故不能使用统一的编码格式，而需要服务端动态获取并处理
	 *
	 * @param string $filename 文件名
	 */
	public static function getFilename( $filename )
	{
		//获取当前访问的浏览器头信息
		$ua = $_SERVER["HTTP_USER_AGENT"];

		//不同浏览器做差异处理
		if(preg_match("/MSIE/", $ua) || preg_match("/Trident\/7.0/", $ua))
		{
			header('Content-Disposition: attachment; filename="' . str_replace("+", "%20", urlencode($filename)) . '"');
		}
		elseif(preg_match("/Firefox/", $ua))
		{
			header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '"');
		}
	}

    /**
     * CURL GET方式提交数据
     *
     * 通过curl的get方式提交获取数据
     *
     * @param string $url api	  地址
     * @param int 	 $second 	  超时时间,单位为秒
     * @param array $diyParamArr 添加了自定义参数，方便各自需求扩展, demo: array( CURLOPT_TIMEOUT=>1 );
     * @return string || boolen
     */
    public static function curlGet( $url, $vars, $second=30, $diyParamArr=array() )
    {
        $url = $vars ? $url . '?' . $vars : $url;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 0);
    	curl_setopt($ch,CURLOPT_HEADER, 0);

    	// 遍历各种diy参数设置
	    if( !empty($diyParamArr) )
	    {
	        foreach ( $diyParamArr as $diyK=>$diyV )
	        {
	            // 参数名和参数值都不为空的时候才配置该项
	            !empty($diyK) && curl_setopt($ch, $diyK, $diyV);
	        }
	    }

        $data = curl_exec($ch);
        $return_ch = curl_errno($ch);
        self::$curlInfo = curl_getinfo($ch);//获取curl相关信息
        curl_close($ch);
        if($return_ch!=0)
        {
        	return false;
        }
        else
        {
        	return $data;
        }
    }

    /**
     * CURL POST方式提交数据
     *
     * 通过curl的post方式提交获取数据
     *
     * @param string $url api	  地址
     * @param int 	 $second 	  超时时间,单位为秒
     * @param array $diyParamArr 添加了自定义参数，方便各自需求扩展, demo: array( CURLOPT_TIMEOUT=>1 );
     * @return string || boolen
     */
    public static function curlPost( $url, $vars, $second=30, $diyParamArr=array() )
    {
    	$ch = curl_init();
    	curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch,CURLOPT_URL,$url);
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    	curl_setopt($ch,CURLOPT_POST, 1);
    	curl_setopt($ch,CURLOPT_HEADER, 0);

    	// 遍历各种diy参数设置
	    if( !empty($diyParamArr) )
	    {
	        foreach ( $diyParamArr as $diyK=>$diyV )
	        {
	            // 参数名和参数值都不为空的时候才配置该项
	            !empty($diyK) && curl_setopt($ch, $diyK, $diyV);
	        }
	    }

    	$data = curl_exec($ch);
    	$return_ch = curl_errno($ch);
    	self::$curlInfo = curl_getinfo($ch);//获取curl相关信息
        curl_close($ch);
        if($return_ch!=0)
        {
        	return false;
        }
        else
        {
        	return $data;
        }
    }
}
