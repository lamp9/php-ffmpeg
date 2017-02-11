<?php
class IndexAction extends Action {
	
	//实例初始化
	public function _initialize() {
		ToolsAction::login_check();
	}
	
	public function index(){
		$this->redirect('jump');
	}
	
	//文件、目录操作
	public function process(){
		$arr = $this->_post('batch');
		$count = count($arr);
		$process = $this->_post('process');
		if($process == 'move'){
			session('move_path', session('path'));
			session('move_file', $arr);
			session('process', "<li><a href=\"javascript:jump_move(true)\">文件移动确认</a></li><li><a href=\"javascript:jump_move(false)\">文件移动取消</a></li>");
		}
		if($process == 'rename') re_name($this->_post('name_o'), $this->_post('name'));
		if($process == 'del') del($count, $arr);
		if($process == 'in'){
			
			$video_dir = get_video_dir();
			$video_set = get_video_set();
			$admin = M('video');
			$time = time();
			for($i = 0; $i < $count; $i++){
				$set = explode(".",$arr[$i]);
				$item = explode('-', $set[0]);
				$path_arr = video_create_dir_by_date($time, $video_dir['in']);
				
				$from = return_char_set_str('.'.$video_dir['temp'].'/'.$arr[$i], true);
				$to = $time."-".$i.'.'.$set[1];
				
				$find['md5'] = $md5 = md5_file($from);
				$result = $admin->where($find)->select();
				if(is_array($result) && count($result)>0){
					unlink($from);
					continue;
				}
				if(rename($from, $path_arr['path'].'/'.$to)){
					$data['file'] = $path_arr['re'].'/'.$to;
					$data['md5'] = $md5;
					$data['title'] = $item[0];
					$data['cat'] = $item[1];
					$data['uid'] = $item[2];
					$data['time'] = date("Y-m-d", $time);
					$data['dir'] = $path_arr['re'];
					$data['play'] = 'F';
					$data['play_file'] = sprintf($video_set['video_file'], md5($data['file'].$video_set['video_file_key']));
					$data['vl'] = 'F';
					$data['resolution'] = 'F';
					$data['img'] = 'F';
					$data['vip'] = 'F';
					$admin->add($data);
				}
			}
		}
		IndexAction::jump();
	}
	
	//文件、目录移动初始化
	public function jump_move(){
		if($this->_post("continue") == "T"){
			$count = count($_SESSION['move_file']);
			for($i = 0; $i < $count; $i++){
				$name = return_char_set_str($_SESSION['move_file'][$i], true);
				rename(
					return_char_set_str(session('move_path'), true)."/".$name,
					return_char_set_str(session('path'), true)."/".$name
				);
			}
		}
		session('move_path', null);
		session('move_file', null);
		session('process', null);
		IndexAction::jump();
	}
	
	//目录直接跳转
	public function jump(){
		$video_dir = get_video_dir();
		$this->list = get_dir_scandir(return_char_set_str('.'.$video_dir['temp'], true));
		$this->sort_arr = get_sort_arr();
		$this->tab_arr = get_tab_arr();
		$this->display('index');
	}
	
	//目录加子目录跳转
	public function jump_dir(){
		session('path', session('path').'/'.$this->_post('dir'));
		IndexAction::jump();
	}
	
	public function ajax_set_session(){
		session($this->_get('key'), $this->_get('val'));	
	}
}