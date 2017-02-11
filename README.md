# php-ffmpeg
###简介
php-ffmpeg是一个视频转码系统，可对视频文件进行信息获取，转码切割，生成封面等，在线转码支持多任务多线程处理，可依靠计算机高效的计算性能完成大量视频文件的任务处理。测试在一台i7-6700(8核16线程3.4GHz)个人PC下性能表现强劲，启动32个视频转换任务，CPU负载100%，效率卓越！
###部署
1. 下载ffmpeg对应系统版本的应用http://ffmpeg.org/并解压
2. 修改config.php
	1. 修改DB_DSN元素，此项为数据库配置
	2. 修改server_config
		1. ffmpeg元素为ffmpeg.exe的绝对路径
		2. web_dir元素为此项目的根目录
		3. char_set元素为系统的字符集，目前只支持gbk/utf-8
3. 浏览器访问http://hostname/index.php,输入用户名:admin,密码:123,则可登录系统
4. 浏览器访问http://hostname/index.php/Install,则可完成对数据库表的安装
