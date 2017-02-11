<?php
$config_jacob =  array(
	'DB_DSN'=>'mysql://root:root@localhost:3306/php_ffmpeg',
	'DB_PREFIX'=>'',
	'TMPL_L_DELIM'=>'<{',
	'TMPL_R_DELIM'=>'}>',
	'SHOW_PAGE_TRACE'=>true,//开启页面Trace
	'VAR_FILTERS'=>'stripslashes,strip_tags',
	'SESSION_OPTIONS'=> array(
		'expire'=>'1800'
	),
	'URL_PATHINFO_DEPR'=>'-',
	
	'server_config'=>array(
		'ffmpeg'		=> '"F:\Program Files\ffmpeg-20170208-3aae1ef-win64-static\bin\ffmpeg.exe"',//ffmpeg程序路径
		'web_dir'		=> 'E:\web\php-ffmpeg',//网站目录
		'user'			=> array(
			'admin'=>array(
				'id'	=> 1,
				'pwd'	=>'322c60d13c392355542da225be060545',//2825527
				'allow'	=>'T',
			),
		),
		'char_set' => 'gbk',//utf-8,gbk
		'video_websize'	=> array(
			/*array(
				'name'		=> '新北美',
				'table'		=> 'video_www_xinbeimei_com',
				'websize'	=> 'http://v.us567.com',
				'wtime'		=> '/get_time',
				'process'	=> '/postup',
				'key'		=> "2d0d4809e6bdb6f4db3e547f27b1873c".'127.0.0.1',
			),
			array(
				'name'		=> '新北美2',
				'table'		=> 'video_www_xinbeimei_com2',
				'websize'	=> 'http://www.xinbeimei.com',
				'wtime'		=> '/get_time',
				'process'	=> '/postup',
				'key'		=> '322c60d13c392355542da225be060545',
			),
			array(
				'name'		=> '新北美3',
				'table'		=> 'video_www_xinbeimei_com3',
				'websize'	=> 'http://www.xinbeimei.com',
				'wtime'		=> '/get_time',
				'process'	=> '/postup',
				'key'		=> '322c60d13c392355542da225be060545',
			),*/
		),
		'video_dir'		=> array(
			'temp'	=> '/video',
			'in'	=> '/video_in',
			'root'	=> '/uploads',
			'img'	=> '/uploads/video_img',
			'base'	=> '/uploads/video_m3u8',
			'vip'	=> '/uploads/video_vip',
		),
		'video_set'		=> array(
			'img_resolution'	=> '300x169',//图片分辨率
			'img_count'			=> '12',//图片生成数量
			'img_select_count'	=> '6',//图片选择数量
			'video_vip_second'	=> '1200',//vip视频单位秒
			'video_bitrate'		=> '300',//码率临界点
			'video_bitrate_vip'	=> '400',//VIP码率
			'video_timeout'		=> '60',//视频文件生成超时时间
			'video_single_second'	=> '15',//单个视频秒数
			'video_thread_html'	=> '8',//前端JS控制同时任务数量
			'video_thread'		=> '2',//命令使用线程数
			'video_resolution'		=> '640x360',//视频分辨率
			'video_resolution_vip'	=> '640x360',//视频分辨率
			'video_file'		=> '%s.m3u8',//视频文件名
			'video_file_key'	=> 'abcdefghijk0987654321',//文件名加密KEY
		),
		'sort_arr'	=> array(
			array(
				'id'	=> '1',
				'name'	=> "分类一"),
			array(
				'id'	=> '2',
				'name'	=> "分类二"),
		),
		'tab_arr'	=> array(
			array(
				'id'	=> '3',
				'name'	=> "标签一"),
			array(
				'id'	=> '4',
				'name'	=> "标签二"),
		),
	),
);

return $config_jacob;
?>