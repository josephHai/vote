<?php

namespace app\index\controller;
use think\Exception;

/**
 *微信验证
 */
class WeChat
{
    /**
     * [$obj 微信端返回的消息对象]
     * @var Object
     */
    private $obj;
    /**
     * [$id 用户openid]
     * @var String
     */
    private $id;

    function __construct()
    {
        $this->appId = config('APPID');
        $this->url = "http://wxwb.applinzi.com/";
        $this->timestamp = time();
    }

    /**
     * [index 入口函数]
     * @date        2017-12-19
     * @param       string        $operateName        操作名称
     * @param       string        $open_id            用户的唯一标识(缺省)
     * @return      bool
     */
    public function index($operateName, $open_id = null)
    {
        $ver = new WeChatCallBack();
        switch ($operateName){
            case 'verify':
                $ver->verify();
                break;
            case 'response':
                $this->responseMsg();
                break;
            case 'checkId':
                if ($open_id == null){
                    break;
                }else{
                    $this->id = $open_id;
                    return $this->isOpenIdValid();
                }
                break;
            default:
                break;
        }
        return 0;
    }

    /**
     * [responseMsg 响应信息]
     * @date        2017-12-19
     */
    private function responseMsg()
    {
        $postData = $GLOBALS[HTTP_RAW_POST_DATA];

        if (!$postData) {
            echo "error";
            return;
        }
        $this->obj = simplexml_load_string($postData,"simpleXMLElement",LIBXML_NOCDATA);
        $this->id = $this->obj->FromUserName;

        $msgType = $this->obj->MsgType;

        switch ($msgType) {
            case 'text':
                echo $this->receiveText();
                break;

            default:
                echo $this->replyText("类型不符");
                break;
        }
    }

    /**
     * [receiveText 处理接收的文本信息]
     * @date        2017-12-19
     */
    private function receiveText()
    {
        $content = $this->obj->Content;

        return $this->replyText($content);
    }

    /**
     * [replyText   回复信息]
     * @date        2017-12-19
     * @param       string                   $con 需要返回的文本信息
     * @return      string                           返回的xml
     */
    private function replyText($con)
    {
        $replyXml="<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					</xml>";
        $resultStr = sprintf($replyXml, $this->obj->FromUserName, $this->obj->ToUserName, time(), $con);

        $openId = $this->obj->FromUserName;
        $url = "url?Id=" + $openId;
        $replyNewsXml ="<xml>
                            <ToUserName>< ![CDATA[%s] ]></ToUserName>
                            <FromUserName>< ![CDATA[%s] ]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType>< ![CDATA[news] ]></MsgType>
                            <ArticleCount>1</ArticleCount>
                            <Articles>
                                <item>
                                    <Title>< ![CDATA[勤工之星] ]></Title>
                                    <Description>< ![CDATA[投票] ]></Description>
                                    <PicUrl>< ![CDATA[] ]></PicUrl>
                                    <Url>< ![CDATA[%s] ]></Url>
                                </item>
                            </Articles>
                        </xml>";
        $resultStr = sprintf($replyXml, $this->obj->FromUserName, $this->obj->ToUserName, time(), $url);

        return $resultStr;
    }

    /**
     * isOpenIdValid    判断用户open_id是否有效
     *
     * @return    bool
     */
    private function isOpenIdValid(){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$this->id&lang=zh_CN";
        $res = json_decode($this->httpsRequests($url));
        if (isset($res->errcode)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * [getAccessToken 获取access_token]
     * @date        2017-12-20
     * @return      String                   access_token
     */
    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $filePath = APP_PATH."access_token.php";
        $data = json_decode($this->get_php_file($filePath));
        if ($data->expire_time < time()) {
            $appSecret = config('APPSECRET');
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$appSecret";
            $res = json_decode($this->httpsRequests($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $this->set_php_file($filePath, json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    /**
     * [httpsRequests description]
     * @date        2017-12-20
     * @param       String                   $url  请求的url
     * @param       [type]                   $data 请求的参数
     * @return      object                         网页响应内容对象
     */
    private function httpsRequests($url, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if(!empty($data)){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    /**
     * get_php_file    读取文件内容
     *
     * @param    string    $filename    文件名
     * @return   string    文件内容
     */
    private function get_php_file($filename) {
        return trim(substr(file_get_contents($filename), 15));
    }

    /**
     * set_php_file    存储文本到文件内
     *
     * @param    string    $filename    文件名
     * @param    string    $content     存储的内容
     */
    private function set_php_file($filename, $content) {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
}

/**
 *微信验证
 */
class WeChatCallBack
{
    /**
     * [verify 若检验成功,返回echoStr,反之不返回值]
     * @date        2017-12-13
     */
    public function verify()
    {
        $echoStr = $_GET['echoStr'];

        if($this->checkSignature()){
            echo $echoStr;
        }else{
            echo "error";
        }
    }

    /**
     * [checkSignature 检验signature]
     * @date        2017-12-13
     * @return      bool
     */
    private function checkSignature()
    {
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $signature = $_GET['signature'];


        $array = array($timestamp, $nonce, config('TOKEN'));
        sort($array);
        $tmpstr = implode('', $array);
        $tmpstr = sha1($tmpstr);

        if($tmpstr == $signature){
            return true;
        }else{
            return false;
        }
    }

}

?>