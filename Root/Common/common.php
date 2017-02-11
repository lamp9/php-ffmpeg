<?php
//主站
function get_video_websize(){
	$config = get_config();
	return $config['video_websize'];
}
//ffmpeg
function get_ffmpeg(){
	$config = get_config();
	return $config['ffmpeg'];
}
//网站目录
function get_web_dir(){
	$config = get_config();
	return $config['web_dir'];
}
//获取视频目录
function get_video_dir(){
	$config = get_config();
	return $config['video_dir'];
}
//获取视频设置
function get_video_set(){
	$config = get_config();
	return $config['video_set'];
}
//获取分类
function get_sort_arr(){
	$config = get_config();
	return $config['sort_arr'];
}
//获取标签
function get_tab_arr(){
	$config = get_config();
	return $config['tab_arr'];
}
//返回加密信息
function return_login_data(){
	$config = get_config();
	$data = $config['user'];
	return $data;
}
//返回加密信息
function return_char_set(){
	$config = get_config();
	$data = $config['char_set'];
	return $data;
}

//获得配置
function get_config(){
	return C('server_config');
}
/******************************************************************************************************/
//解密
function epwd_decode(){
	$time = $_GET['time'];
	$epwd = $_GET['epwd'];
	if((time() - $time > 5) || ($epwd != md5($time.get_key()))) exit;
}
//加密
function encrypt($time, $key){
	return md5($time.$key);
}
//获取服务器时间
function get_server_time($websize){
	return file_get_contents($websize);
}
/******************************************************************************************************/
//
function data_post($websize, $webin, $data, $key){
	$data = http_build_query($data);//生成url-encode后的请求字符串，将数组转换为字符串
	$opts = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n"."Content-Length: " . strlen($data) . "\r\n",
			'content' => $data
		)
	);
	$context = stream_context_create($opts);//生成请求的句柄文件
	$html = file_get_contents(
		$websize.$webin.'?time='.$key['time'].'&epwd='.$key['epwd']
		, false, $context);
	
	return $html;
}

//后台服务器添加数据
function data_add($websize, $json, $time, $pwd){
	$data = array ('json' => $json);
	$data = http_build_query($data);//生成url-encode后的请求字符串，将数组转换为字符串
	$opts = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n"."Content-Length: " . strlen($data) . "\r\n",
			'content' => $data
		)
	);
	$context = stream_context_create($opts);//生成请求的句柄文件
	$html = file_get_contents(
		$websize.'?time='.$time.'&pwd='.$pwd
		, false, $context);
	return $html;
}
//后台服务器修改数据
function data_modify($websize, $webin, $json, $pwd){
	$data = array ('json' => $json);
	$data = http_build_query($data);//生成url-encode后的请求字符串，将数组转换为字符串
	$opts = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n"."Content-Length: " . strlen($data) . "\r\n",
			'content' => $data
		)
	);
	$context = stream_context_create($opts);//生成请求的句柄文件
	$html = file_get_contents(
		$websize.$webin.'Uploads_admin-data_modify'
		.'-time-'.$pwd['time'].'-epwd-'.$pwd['epwd']
		, false, $context);
	echo $html;
}

//返回分类
function ppt_sort($websize, $webin, $table, $sid, $pwd){
	$html = file_get_contents(
		$websize.$webin.'Uploads_admin-get_sort-table-'.$table
		.'-sid-'.$sid.'-time-'.$pwd['time'].'-epwd-'.$pwd['epwd']);
	echo $html;
}
//返回标签
function get_tab($websize, $webin, $table, $table_sort, $pwd){
	$html = file_get_contents(
		$websize.$webin.'Uploads_admin-get_tab-table-'.$table
		.'-table_sort-'.$table_sort.'-time-'.$pwd['time'].'-epwd-'.$pwd['epwd']);
	echo $html;
}
/******************************************************************************************************/
//读取文件内容
function return_file_content($path){
	if($file = file($path)) {
		foreach ($file as $content) {
			$text .= $content;
		}
	}
	return $text;
}
//删除批量文件
function del($count, $arr){
	$video_dir = get_video_dir();
	$path	= '.'.$video_dir['temp'].'/';
	for($i = 0; $i < $count; $i++){
		$name = return_char_set_str($path.$arr[$i], true);
		if(is_dir($name)) deldir($name);
		else unlink($name);
	}
}
//目录删除
function deldir($dir) {//先删除目录下的文件：
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				deldir($fullpath);
			}
		}
	}
	closedir($dh);
	//删除当前文件夹
	if(rmdir($dir)) return true;
	else return false;
}

function re_name($name_o, $name){
	$video_dir = get_video_dir();
	$path	= '.'.$video_dir['temp']."/";
	
	$from	= return_char_set_str($path.$name_o, true);
	$to		= return_char_set_str($path.$name, true);
	rename($from, $to);
}

//输出分割目录
function current_path() {
	$arr = explode("/", session('path'));
	$all = "";
	$b = 1;
	foreach($arr as $a) {
		if($b == 1) {
			$all .= $a;
			$b = 0;
		} else $all .= "/".$a;
		echo "<li><a href=\"javascript:jump('$all')\">$a</a></li>";
	}
}

//当前目录扫描
function get_dir_scandir($path){
	$i = 0;
	foreach(scandir($path) as $single){
		if (in_array($single,array('.','..'))) continue;
		$name = return_char_set_str($single, false);
		if (is_dir($path.'/'.$single)) {
			$data[$i]['batch'] = $name;
			$data[$i]['is_dir'] = "文件夹";
			$data[$i]['name'] = "<a href=\\\"javascript:jump_dir('$name')\\\">$name</a>";
			$data[$i]['operation'] = "";
			$i++;
		}
	}
	foreach(scandir($path) as $single){
		if (in_array($single,array('.','..'))) continue;
		$name = return_char_set_str($single, false);
		if (!is_dir($path.'/'.$single)) {
			$data[$i]['batch'] = $name;
			$data[$i]['is_dir'] = "文件";
			$data[$i]['name'] = $name;
			$data[$i]['size'] = format_bytes(filesize($path.'/'.$single));
			$data[$i]['operation'] = "";
			$i++;
		}
	}
	return $data;
}
/**
$sym : true=>utf8 to $char_set, false=>$char_set to utf8
*/
function return_char_set_str($str, $sym){
	$char_set = return_char_set();
	switch ($char_set){
		case 'utf-8':
			return $str;
			break;
		case 'gbk':
			if($sym) return iconv('utf-8', $char_set, $str);
			else return iconv($char_set, 'utf-8', $str);
			break;
		default:
	}
}

//计算文件容量
function format_bytes($size) { 
	$units = array(' B', ' KB', ' MB', ' GB', ' TB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
	return round($size, 2).$units[$i];
}


//返回随机字符串
function auto_str($length = 10){
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$password = '';
	for ( $i = 0; $i < $length; $i++ ){
		//这里提供两种字符获取方式
		//第一种是使用 substr 截取$chars中的任意一位字符
		//第二种是取字符数组 $chars 的任意元素  
		//$password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
		$password .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	return $password;
}

//视频信息获取
function video_info($file,$ffmpeg) {
	ob_start();
	passthru(sprintf($ffmpeg.' -i "%s" 2>&1', $file));
	$info = ob_get_contents();
	ob_end_clean();
	// 通过使用输出缓冲，获取到ffmpeg所有输出的内容。
	$ret = array();
	// Duration: 01:24:12.73, start: 0.000000, bitrate: 456 kb/s
	if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
		$ret['duration'] = $match[1]; // 提取出播放时间
		$da = explode(':', $match[1]); 
		$ret['seconds'] = $da[0] * 3600 + $da[1] * 60 + $da[2]; // 转换为秒
		$ret['start'] = $match[2]; // 开始时间
		$ret['bitrate'] = $match[3]; // bitrate 码率 单位 kb
	}
	
	// Stream #0.1: Video: rv40, yuv420p, 512x384, 355 kb/s, 12.05 fps, 12 tbr, 1k tbn, 12 tbc
	if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
		$ret['vcodec'] = $match[1]; // 编码格式
		$ret['vformat'] = $match[2]; // 视频格式 
		$ret['resolution'] = $match[3]; // 分辨率
		$a = explode('x', $match[3]);
		$ret['width'] = $a[0];
		$ret['height'] = $a[1];
	}
	
	// Stream #0.0: Audio: cook, 44100 Hz, stereo, s16, 96 kb/s
	if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
		$ret['acodec'] = $match[1];       // 音频编码
		$ret['asamplerate'] = $match[2];  // 音频采样频率
	}
	
	if (isset($ret['seconds']) && isset($ret['start'])) {
		$ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间
	}
	
	$ret['size'] = filesize($file); // 文件大小
	return $ret;
}
function video_info_vl($time){
	if($time=='' || $time == null) return 'E';
	$item = explode('.', $time);
	$item = explode(':', $item[0]);
	if($item[0] == '00') return $item[1].':'.$item[2];
	else return $item[0].':'.$item[1].':'.$item[2];
}
function get_second($time, $count){
	$item = explode(':', $time);
	switch (count($item)) {
		case 2:
			$second = (int)$item[0] * 60 + (int)$item[1];
			break;
		case 3:
			$second = (int)$item[0] * 3600 + (int)$item[1] * 60 + (int)$item[2];
			break;
	}
	return $rate = (int)($second / ($count + 1));
}
function video_create_dir_by_date($time, $path_init){
	$path = '.'.$path_init.date('/Y', $time);
	mkdir($path);
	$path = $path.date('/md', $time);
	mkdir($path);
	$re = date('/Y/md', $time);
	$data['path'] = $path;
	$data['re'] = $re;
	return $data;
}
function video_create_dir_by_custom($root, $dir, $id){
	$dir = explode('/', $dir);
	$root = '.'.$root;
	$root .= '/'.$dir[1];
	mkdir($root);
	$root .= '/'.$dir[2];
	mkdir($root);
	$root .= '/'.$id;
	mkdir($root);
	return $root;
}
?>