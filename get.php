<?php
$mode               = $_GET['mode'];
$parm               = $_GET['parm'];
//网易liebiao
if($mode == 'list'){
	echo netease_user($parm);
//网易MP3
} elseif ($mode == 'net_mp3') {
	$ss = 'http://music.163.com/api/song/detail/?id=&ids=%5B' . $parm . '%5D';
	$st = J_encode(g_contents($ss));
	preg_match_all('|"mp3Url":"(.*?)","|', $st, $arr);
	$arr  = $arr[1];
	$type = "open";
	if ($type != "open") {
		$url = $arr[$type];
	} else {
		$url = end($arr);
	}
	header("Location:$url");
//pic
} elseif ($mode == 'image_info') {
	header("Content-type:text/javascript;charset=UTF-8");
	$ids = base64_decode($parm);
	if ($ids) {
		$img       = imgColor($ids);
		$gray      = '255,255,255,1';
		$grayLevel = $img['r'] * 0.299 + $img['g'] * 0.587 + $img['b'] * 0.114;
		if ($grayLevel >= 150) {
			$gray = '0,0,0,1';
		}
		echo "var imginfo=[{img_url:'" . $ids . "',img_color:'" . $img['r'] . "," . $img['g'] . "," . $img['b'] . "',font_color:'" . $gray . "'}];";
		exit();
	} else {
		echo "var imginfo=[{img_url:'/content/plugins/kl_album/style/defaultSinger.jpg',img_color:'0,0,0',font_color:'0,0,0,1'}];";
		exit();
	}
//网易歌词获取
} elseif ($mode == 'net_lrc') {
	$song_id = $parm;
	$str = g_contents("http://music.163.com/api/song/lyric?os=pc&id=" . $song_id . "&lv=-1&kv=-1&tv=-1");
	preg_match_all('#"lyric":"(.*)"#iUs', $str, $name);
	$name = lrc_th($name[1][0]);
	if ($name != '') {
		echo "var cont = '" . $name . "';";
	} else {
			echo "var cont = '[00:02.00]非常抱歉 - 歌词未找到[00:05.00]~键：播放/暂停';";
    }
}

function lrc_th($str)
{
	global $url;
	$str = preg_replace("@(\w+)?\.?(\w+)\.(com|org|info|net|cn|biz|cc|uk|tk|jp|la|ru|us|ws)@U", 'tv1314.com', $str);
	$str = preg_replace("@\[by:\s?([^\]]+)\]@U", '[by:嘟嘟音乐电台]', $str);
	$str = preg_replace("@(\d+){5,11}@", '*********', $str);
	$str = preg_replace("@\[([A-Za-z]+)\]@U", '这句歌词飞不见啦~~', $str);
	$str = preg_replace("@编辑\s?：?:?\s?([^\[]+)\[@", 'BY:火星人[', $str);
	$str = str_replace("\\n", "", $str);
	$str = str_replace("TTPOD", "GS'bolg", $str);
	$str = str_replace("天天动听", "DuDuPlayer", $str);
	$str = str_replace("'", "\'", $str);
	$str = str_replace("\n", "", $str);
	return $str;
}
function imgColor($imgUrl)
{
	$imageInfo = getimagesize($imgUrl);
	$imgType   = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
	$imageFun  = 'imagecreatefrom' . ($imgType == 'jpg' ? 'jpeg' : $imgType);
	$i         = $imageFun($imgUrl);
	$rColorNum = $gColorNum = $bColorNum = $total = 0;
	for ($x = 50; $x < imagesx($i) - 50; $x++) {
		for ($y = 50; $y < imagesy($i) - 50; $y++) {
			$rgb = imagecolorat($i, $x, $y);
			$r   = ($rgb >> 16) & 0xFF;
			$g   = ($rgb >> 8) & 0xFF;
			$b   = $rgb & 0xFF;
			$rColorNum += $r;
			$gColorNum += $g;
			$bColorNum += $b;
			$total++;
		}
	}
	$rgb      = array();
	$rgb['r'] = round($rColorNum / $total);
	$rgb['g'] = round($gColorNum / $total);
	$rgb['b'] = round($bColorNum / $total);
	return $rgb;
}
function g_contents($url)
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$header[] = "Cookie: " . "appver=1.5.0.75771;";
	$ch         = curl_init();
	$reff       = 'http://music.163.com/';
	$timeout    = 20;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_REFERER, $reff);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function object_array($array)
{
	if (is_object($array)) {
		$array = (array) $array;
	}
	if (is_array($array)) {
		foreach ($array as $key => $value) {
			$array[$key] = object_array($value);
		}
	}
	return $array;
}
function convert_encoding($str, $nfate, $ofate)
{
	if (function_exists("mb_convert_encoding")) {
		$str = mb_convert_encoding($str, $nfate, $ofate);
	} else {
		if ($nfate == "GBK")
			$nfate = "GBK//IGNORE";
		$str = iconv($ofate, $nfate, $str);
	}
	return $str;
}
function outcode($string, $outEncoding = 'UTF-8')
{
	$encoding = "UTF-8";
	for ($i = 0; $i < strlen($string); $i++) {
		if (ord($string{$i}) < 128)
			continue;
		if ((ord($string{$i}) & 224) == 224) {
			$char = $string{++$i};
			if ((ord($char) & 128) == 128) {
				$char = $string{++$i};
				if ((ord($char) & 128) == 128) {
					$encoding = "UTF-8";
					break;
				}
			}
		}
		if ((ord($string{$i}) & 192) == 192) {
			$char = $string{++$i};
			if ((ord($char) & 128) == 128) {
				$encoding = "GB2312";
				break;
			}
		}
	}
	if (strtoupper($encoding) == strtoupper($outEncoding))
		return $string;
	else
		return iconv($encoding, $outEncoding, $string);
}
function J_encode($arr)
{
	$search  = "#\\\u([0-9a-f]+)#ie";
	$replace = "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))";
	$str     = preg_replace($search, $replace, $arr);
	return str_replace("\\", "", $str);
}
function netease_user($userid){
    $userplaylist = array();
    $url = "http://music.163.com/api/user/playlist/?offset=0&limit=1001&uid=" . $userid;
    $response = netease_http($url);
    $playlists = $response["playlist"];
    $songlist = 'var NeiCeList =[';
    $description = '来自网易云歌单';
    if(($userid)&&($userid!='')){
		for($i = 0 ;count($playlists)>$i;$i++){
			$songlist.="{'song_album_id':'".$playlists[$i]['id']."',";//设置专辑名
			$songlist.="'song_album':'".$playlists[$i]['name']."',";//设置专辑名
			$songlist.="'song_album1':'".$description."',";//设置介绍
			$album = netease_playlist($playlists[$i]['id']);
			$songlist.='"song_list":'.$album. "},";
		}
    }
	$songlist = substr($songlist, 0, strlen($songlist)-1);
	$songlist.='];';
    return $songlist;
}
function netease_playlist($playlist_id){
    $url = "http://music.163.com/api/playlist/detail?id=" . $playlist_id;
    $response = netease_http($url);
    if( $response["code"]==200 && $response["result"] ){
        //处理音乐信息
        $result = $response["result"]["tracks"];
        $count = count($result);
        if( $count < 1 ) return false;
        foreach($result as $k => $value){
             $id = $value["id"];
             $title = $value["name"];
             $album = $value["album"]['name'];
             $pic = $value["album"]["picUrl"];
            $artists = array();
            foreach ($value["artists"] as $artist) {
                $artists[] = $artist["name"];
            }
            $artists = implode(",", $artists);
            $collect[] = array(
                "song_id" => "$id",
                "song_title" => "$title",
                "singer" => "$artists",
                "album" => "$album",
                "pic" => "$pic",
            );
        }
        return json_encode($collect);
    }
    return false;
}
function netease_http($url){
    $refer = "http://music.163.com/";
    $header[] = "Cookie: " . "appver=1.5.0.75771;";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    $cexecute = curl_exec($ch);
    curl_close($ch);

    if ($cexecute) {
        $result = json_decode($cexecute, true);
        return $result;
    }else{
        return false;
    }
}
?>