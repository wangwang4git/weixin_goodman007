<?php


/**
  * 微信公众平台接口PHP100封装功能类文件
  * v0.2
  *
  * author PHP100.com  张恩民
  * date 2013-4-9 PRC:E+8 23:03
  * linkme QQ925939 chuangen.com
  *
  * modify 王汪
  * date 2013-6-4
  */

// 载入类
include('lastRSS.php');
include('lastXML.php');

define("TOKEN", "GoodMan007");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->init();
// 执行接收器方法
$wechatObj->weixin_run();

class wechatCallbackapiTest {
	private $fromUsername;
	private $toUsername;
	private $time;
	private $keyword;
	private $rss;

	public function init() {
		// 实例化
		$this->rss = new lastRSS;
		// 设置缓存目录，要手动建立
		$this->rss->cache_dir = 'cache';
		// 设置缓存时间，默认为0，即随访问更新缓存；建议设置为3600，一个小时
		$this->rss->cache_time = 5;
		// 设置字符编码
		$this->rss->default_cp = 'UTF-8';
		// 设置输出数量
		$this->rss->items_limit = 3;
		// 设置时间格式
		$this->rss->date_format = 'Y-m-d H:i';
		// 设置过滤html脚本，默认为false，即不过滤
		$this->rss->stripHTML = false;
		// 设置处理CDATA信息，默认为nochange，另有strip和content两个选项
		$this->rss->CDATA = 'content';
	}

	private function rss_reg($reg, $content) {
		if (preg_match($reg, $content, $matches)) {
        	return $matches[0];
    	} else {
    		if ($reg == '%(?<=<a href=")http://goodman007\.net/wp-content/uploads/.*?(?=">)%') {
    			return "http://goodman007.net/weixin/api/404.png";
    		} else if ($reg == '%(?<=a href=")http://goo\.gl/maps/.*?(?=">)%') {
    			return "http://goo.gl/maps/tLwfB";
    		}
    	}
	}

	public function weixin_run() {
		$this->responseMsg();
		switch ($this->keyword) {
			case "我的足迹" :
				{
					$reg = '%(?<=<a href=")http://goodman007\.net/wp-content/uploads/.*?(?=">)%';
					// 指定RSS源
					$rssURL = 'http://goodman007.net/archives/category/journey/feed/';
					// 处理RSS并获取内容
					$rssResult = $this->rss->Get($rssURL);

					$arr = array();
					$count = 0;
					foreach ($rssResult['items'] as $rssItem) {
						$title = $rssItem['title'] ."--" .$rssItem['pubDate'];
						$description = $title;
						$picUrl = $this->rss_reg($reg, $rssItem['content:encoded']);
						$url = $rssItem['link'];

						$arr[$count] = array($title, $description, $picUrl, $url);
						++$count;
					}
					$arr[$count] = array("更多足迹，请移步 GoodMan007旅途归档", "更多足迹，参见 GoodMan007旅途归档",
						"http://goodman007.net/weixin/api/4more.png", "http://goodman007.net/archives/category/journey/");

					$this->fun_xml("news", $arr, array($count + 1, 0));
					break;
				}
			case "我在哪儿" :
				{
					$reg = '%(?<=a href=")http://goo\.gl/maps/.*?(?=">)%';
					// 指定RSS源
					$rssURL = 'http://goodman007.net/archives/category/journey/feed/';
					// 处理RSS并获取内容
					$rssResult = $this->rss->Get($rssURL);

					$location = null;
					foreach ($rssResult['items'] as $rssItem) {
						$location = $this->rss_reg($reg, $rssItem['content:encoded']);
						break;
					}

					$arr[] = "我们在哪儿，猛击连接~\n" .$location;

					$this->fun_xml("text", $arr);
					break;
				}
			case "我这天气" :
				{
					$url = 'http://weather.china.xappengine.com/api?city=huangshan';
					$re = file_get_contents($url);
					$weatherResult = json_decode($re);

					$arr = array();
					$count = 0;
					foreach ($weatherResult->forecasts as $weatherItem) {
						$date = substr($weatherItem->date, strpos($weatherItem->date, '-') + 1);
						if ($count == 0) {
							$title = $weatherResult->name ."天气 " .$date ." [" .$weatherItem->text ."] 气温：[" .$weatherItem->low ."~" .$weatherItem->high ."℃]";
						} else {
							$title = $date ." [" .$weatherItem->text ."]\n气温：[" .$weatherItem->low ."~" .$weatherItem->high ."℃]";
						}
						$description = $title;
						if ($count == 0) {
							$picUrl = $weatherItem->image_large;
						} else {
							$picUrl = $weatherItem->image_small;
						}
						$url = 'http://www.weather.com.cn/weather/101221001.shtml';

						$arr[$count] = array($title, $description, $picUrl, $url);
						++$count;
					}

					$this->fun_xml("news", $arr, array($count, 0));

					break;
				}
				case "武汉天气" :
				{
					$url = 'http://weather.china.xappengine.com/api?city=wuhan';
					$re = file_get_contents($url);
					$weatherResult = json_decode($re);

					$arr = array();
					$count = 0;
					foreach ($weatherResult->forecasts as $weatherItem) {
						$date = substr($weatherItem->date, strpos($weatherItem->date, '-') + 1);
						if ($count == 0) {
							$title = $weatherResult->name ."天气 " .$date ." [" .$weatherItem->text ."] 气温：[" .$weatherItem->low ."~" .$weatherItem->high ."℃]";
						} else {
							$title = $date ." [" .$weatherItem->text ."]\n气温：[" .$weatherItem->low ."~" .$weatherItem->high ."℃]";
						}
						$description = $title;
						if ($count == 0) {
							$picUrl = $weatherItem->image_large;
						} else {
							$picUrl = $weatherItem->image_small;
						}
						$url = 'http://www.weather.com.cn/weather/101200101.shtml';

						$arr[$count] = array($title, $description, $picUrl, $url);
						++$count;
					}

					$this->fun_xml("news", $arr, array($count, 0));

					break;
				}
				case "武汉公交" :
				{
					$arr[] = "回复：公交+空格+公交车号，即可查询公交路线。\n" .
							"如：公交 703(中间只有一个空格~)\n" .
							"回复：公交+空格+站台名称，即可查询公交站点。\n" .
							"如：公交 广埠屯\n" .
							"回复：公交+空格+起点+空格+终点，即可查询公交驾乘。\n" .
							"如：公交 广埠屯 光谷\n";
					$this->fun_xml("text", $arr);
					break;
				}
				case "武汉地铁" :
				{
					$str = "";
					// 查询地铁路线
					$url = 'http://openapi.aibang.com/bus/lines?app_key=5c9dce7828a4cc468d4e14a4cac2df01&city=武汉&q=1号线';
					$re = file_get_contents($url);
	                $data = XML_unserialize($re);
	                // 结果选取
					$str .= "1号线" .$data['root']['lines']['line'][0]['info'] ."\n" .
							str_replace(";", " -> ", $data['root']['lines']['line'][0]['stats']) ."\n\n";

					$url = 'http://openapi.aibang.com/bus/lines?app_key=5c9dce7828a4cc468d4e14a4cac2df01&city=武汉&q=2号线';
					$re = file_get_contents($url);
	                $data = XML_unserialize($re);
					$str .= "2号线" .$data['root']['lines']['line'][0]['info'] ."\n" .
							str_replace(";", " -> ", $data['root']['lines']['line'][0]['stats']);

	                $arr = array();
                    $arr[] = $str;

					$this->fun_xml("text", $arr);
					break;
				}
			case "帮助" :
				{
					$arr[] = "回复以下括号内的关键词就可以收到相应的内容~\n" .
							"黄山信息篇...\n" .
							"【我的足迹】\n【我在哪儿】\n【我这天气】\n" .
							"武汉信息篇...\n" .
							"【武汉天气】\n【武汉公交】\n【武汉地铁】";
					$this->fun_xml("text", $arr);
					break;
				}
			default:
				{
					if (strpos($this->keyword, "公交 ") === 0) {
						if (substr_count($this->keyword, ' ') == 1) {
			                $key = substr($this->keyword, strpos($this->keyword, ' ') + 1);
			                if (intval($key) != 0) {
			                    // 查询公交路线
			                    $url = 'http://openapi.aibang.com/bus/lines?app_key=5c9dce7828a4cc468d4e14a4cac2df01&city=武汉&q=' .$key;
			                    $re = file_get_contents($url);
			                    $data = XML_unserialize($re);
			                    $arr = array();
			                    if ($data['root']['result_num'] != 0) {
			                        // 结果选取
			                        $arr[] = $data['root']['lines']['line'][0]['info'] ."\n" .
			                        	str_replace(";", " -> ", $data['root']['lines']['line'][0]['stats']);
			                    } else {
			                        // 查无结果
			                        $arr[] = "查无结果，请确认回复格式！";
			                    }
			                    $this->fun_xml("text", $arr);
			                } else {
			                    // 查询公交站点
			                    $url = 'http://openapi.aibang.com/bus/stats?app_key=5c9dce7828a4cc468d4e14a4cac2df01&city=武汉&q=' .$key;
			                    $re = file_get_contents($url);
			                    $data = XML_unserialize($re);
			                    $arr = array();
			                    if ($data['root']['result_num'] != 0) {
			                        // 结果选取
			                        $str = "";
			                        foreach ($data['root']['stats']['stat'] as $statItem) {
			                            $line_names = $statItem['line_names'] .";";
			                            $line_names = preg_replace('/\(.*?\)/', '', $line_names);
			                            $line_names = preg_replace('/(.*?;)\1/', '$1', $line_names);
			                            $str .= $statItem['name'] ."\n" .preg_replace('/;/', "\t", $line_names) ."\n";
			                        }
			                        $arr[] = $str;
			                    } else {
			                        // 查无结果
			                        $arr[] = "查无结果，请确认回复格式！";
			                    }
			                    $this->fun_xml("text", $arr);
			                }
				        } else if (substr_count($this->keyword, ' ') == 2) {
				        	$arr = array();
				        	$arr[] = '底层数据接口报错：地点"广埠屯"无法定位!' .
									"\n广埠屯都不支持定位，心都凉了，留着以后再换数据服务接口~(>_<)~";
				        	$this->fun_xml("text", $arr);
				        }
					} else {
						$arr[] = "Friend，你好！\n谨以此，纪念肖艳和王汪黄山之行！人生，且行且珍惜！！\n" .
								"如需查询旅行足迹，请使用指定关键词进行查询。\n比如回复【帮助】可以查看高级功能~";
						$this->fun_xml("text", $arr);
				    }
					break;
				}
		}
	}

	public function valid() {
		$echoStr = $_GET["echostr"];

		// valid signature , option
		if ($this->checkSignature()) {
			echo $echoStr;
			exit;
		}
	}

	public function responseMsg() {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty ($postStr)) {
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->fromUsername = $postObj->FromUserName;
			$this->toUsername = $postObj->ToUserName;
			$this->keyword = trim($postObj->Content);
		} else {
			echo "this a failure for weixin API!";
			exit;
		}
	}

	// 微信封装类,
	// type: text 文本类型,news 图文类型...
	// text,array(内容),array(ID)
	// news,array(array(标题,介绍,图片链接,超链接),...小于10条),array(条数,ID)
	// image,array(图片链接),array(ID) <当前不支持>
	// location,array(地理位置纬度,地理位置经度,地图缩放大小,地理位置信息),array(ID) <当前不支持>
	// link,array(消息标题,消息描述,消息链接),array(ID) <当前不支持>
	// music,array(音乐标题,音乐描述,音乐链接,高质量音乐链接),array(ID) <当前支持不完善>
	private function fun_xml($type, $value_arr, $o_arr = array (
		0
	)) {
		// =================xml header============
		$con = "<xml>" .
		"<ToUserName><![CDATA[{$this->fromUsername}]]></ToUserName>" .
		"<FromUserName><![CDATA[{$this->toUsername}]]></FromUserName>" .
		"<CreateTime>{$this->time}</CreateTime>" .
		"<MsgType><![CDATA[{$type}]]></MsgType>";

		// =================type content============
		switch ($type) {
			case "text" :
				{
					$con .= "<Content><![CDATA[{$value_arr[0]}]]></Content>" .
					"<FuncFlag>{$o_arr}</FuncFlag>";
					break;
				}
			case "news" :
				{
					$con .= "<ArticleCount>{$o_arr[0]}</ArticleCount>" .
					"<Articles>";
					foreach ($value_arr as $id => $v) {
						// 判断数组数不超过设置数
						if ($id >= $o_arr[0])
							break;
						else
							null;
						$con .= "<item>" .
						"<Title><![CDATA[{$v[0]}]]></Title>" .
						"<Description><![CDATA[{$v[1]}]]></Description>" .
						"<PicUrl><![CDATA[{$v[2]}]]></PicUrl>" .
						"<Url><![CDATA[{$v[3]}]]></Url>" .
						"</item>";
					}
					$con .= "</Articles>" .
					"<FuncFlag>{$o_arr[1]}</FuncFlag>";
					break;
				}
			case "image" :
				{
					$con .= "<PicUrl><![CDATA[{$value_arr[0]}]]></PicUrl>" .
					"<FuncFlag>{$o_arr}</FuncFlag>";
					break;
				}
			case "location" :
				{
					$con .= "<Location_X><![CDATA[{$value_arr[0]}]]></Location_X>" .
					"<Location_Y><![CDATA[{$value_arr[1]}]]></Location_Y>" .
					"<Scale><![CDATA[{$value_arr[2]}]]></Scale>" .
					"<Label><![CDATA[{$value_arr[3]}]]></Label>" .
					"<FuncFlag>{$o_arr}</FuncFlag>";
					break;
				}
			case "link" :
				{
					$con .= "<Title><![CDATA[{$value_arr[0]}]]></Title>" .
					"<Description><![CDATA[{$value_arr[1]}]]></Description>" .
					"<Url><![CDATA[{$value_arr[2]}]]></Url>" .
					"<FuncFlag>{$o_arr}</FuncFlag>";
					break;
				}
			case "music" :
				{
					$con .= "<Music>" .
					"<Title><![CDATA[{$value_arr[0]}]]></Title>" .
					"<Description><![CDATA[{$value_arr[1]}]]></Description>" .
					"<MusicUrl><![CDATA[{$value_arr[2]}]]></MusicUrl>" .
					"<HQMusicUrl><![CDATA[{$value_arr[3]}]]></HQMusicUrl>" .
					"</Music>" .
					"<FuncFlag>{$o_arr}</FuncFlag>";
					break;
				}
		} // end switch

		// =================end return============
		echo $con . "</xml>";
	}

	private function checkSignature() {
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array (
			$token,
			$timestamp,
			$nonce
		);
		sort($tmpArr);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}
?>