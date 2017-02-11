<?php
class InstallAction extends Action {
	public function index(){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$dir = get_video_dir();
		foreach($dir as $k => $v){
			if(mkdir('.'.$v)) echo "文件夹".$v."创建成功<br/>";
			//else echo "文件夹<font color='#FF0000'>".$v."</font>创建失败<br/>";
		}
		
		$arr = M()->query("create table video
		(
			id int not null auto_increment,
			uid int,
			cat text,
			title char(100),
			file char(100),
			md5 char(32),
			dir char(20),
			play char(1),
			play_file char(40),
			vl char(10),
			resolution char(10),
			bitrate char(15),
			img char(200),
			time date,
			vip char(1),
			primary key(id)
		)
		engine = MyISAM default charset = utf8");
		
		
		$video_websize = get_video_websize();
		//var_dump($video_websize);
		foreach($video_websize as $key){
			$arr = M()->query("create table ".$key['table'].
			"(
				id int not null,
				cid int not null,
				primary key(id)
			)
			engine = MyISAM default charset = utf8");
		}

		echo "<script>alert(\"安装成功！\");location.href='/';</script>";
	}
}