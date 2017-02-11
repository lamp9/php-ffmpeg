<?php
ini_set('max_execution_time', '0');
//set_time_limit(0);
$id = $_POST['id'];
$file_path = $_POST['file_path'];
$file = $_POST['file'];
$vip = $_POST['vip'];
$bitrate = $_POST['bitrate'];
$resolution = $_POST['resolution'];

include "config.php";
$set = $config_jacob['server_config'];
$ffmpeg = $set['ffmpeg'];
$video_set = $set['video_set'];

$video_thread = $video_set['video_thread'];
$video_bitrate = ($vip == 'T') ? $video_set['video_bitrate_vip'] : $video_set['video_bitrate'];
$video_resolution = ($vip == 'T') ? $video_set['video_resolution_vip'] : $video_set['video_resolution'];
$video_single_second = $video_set['video_single_second'];
$video_file = $file_path;

$resolution_arr = explode('x', $resolution);
$resolution_arr2 = explode('x', $video_resolution);

if(is_numeric($resolution_arr[0])){
	if($resolution_arr[0] < $resolution_arr2[0]) $video_resolution = $resolution;
}

if($video_bitrate > $bitrate) $video_bitrate = '';
else $video_bitrate = '-b:v '.$video_bitrate.'k';

$command = sprintf('%s -threads %s -y -i "%s" -s %s %s -hls_time %s -hls_list_size 0 -c:v libx264 -b:a 64k -ar 22050 -ac 1 -c:a aac -strict -2 -f hls "%s"',
	$ffmpeg, $video_thread, $file, $video_resolution, $video_bitrate, $video_single_second, $video_file
	);
//passthru($command);
//echo $command;exit;
$result = exec($command, $arr, $return);
$play = ($return == 0) ? 'T' : 'E';
mysql_connect("localhost","root","") or die ('数据库连接失败！');
mysql_query('use vodxbm');
mysql_query("update video set play='$play' where id=$id");
mysql_close();
?>
