<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
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


$ffmpeg = '"I:\ffmpeg\bin\ffmpeg.exe"';//命令入口
$file_name = $_POST['file'];
$char_set = 'gbk';
$file_name = iconv('utf-8', $char_set, $file_name);


$file_path = 'I:\videoBT\img/';
$file = $file_path.$file_name;//文件路径
$img_resolution = '740x416';//图片分辨率
$path = 'I:\videoBT\img';//图片保存路径
$count = 10;//图片数

$video_info = video_info($file,$ffmpeg);
$vl = video_info_vl($video_info['duration']);

$rate = get_second($vl, $count);
for($j = 1; $j <= $count; $j++){
	$second += $rate;
	if(strlen($j) == 1) $s = '0'.$j;
	else $s = $j;
	/*$command = sprintf($ffmpeg.' -ss %s -i "%s" -y -f  image2 -s %s -vframes 1  %s',
		$second, $file, $img_resolution,*/ //控制图片大小
	$command = sprintf($ffmpeg.' -ss %s -i "%s" -y -f  image2 -vframes 1  %s',
		$second, $file,
		$path.'/'.$file_name.$s.'.jpg'
		);
	passthru($command);
}

echo '<a href=img.html style=" padding:10px 20px; background:#0066FF; border:1px solid #0066FF; color:#FFFFFF; font-size:16px; font-weight:bold;">返回</a>';


?>