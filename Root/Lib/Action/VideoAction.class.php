<?php
class VideoAction extends Action {
	
	public $table = 'video';
	
	public function _initialize(){
		ToolsAction::login_check();
	}
	
	public function index(){
		cookie('video_url', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$admin = M($this->table);
		if(session("video_order") == "") session("video_order", 'desc');
		if(session("video_page_size") == "") session("video_page_size", 10);
		$data = VideoAction::set_field_false();
		$page = ToolsAction::return_page($admin, $data, 'Page', '条记录', session("video_page_size"));
		$this->list = ToolsAction::return_result($admin, $data, false, array('id'=>session("video_order")), $page);
		$this->show = $page->show();
		$this->sort_arr = get_sort_arr();
		$this->tab_arr = get_tab_arr();
		$this->display();
	}
	
	public function video_websize(){
		cookie('video_url', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$table = $this->_get('table');
		$this->wtable = $table;
		if(session("video_order") == "") session("video_order", 'desc');
		if(session("video_page_size") == "") session("video_page_size", 10);
		$admin = VideoAction::getfield(new Model(), $table);
		$page = ToolsAction::return_page($admin, false, 'Page', '条记录', session("video_page_size"));
		$admin = VideoAction::getfield(new Model(), $table);
		$this->list = ToolsAction::return_result($admin, false, false, array('v.id'=>session("video_order")), $page);
		$this->show = $page->show();
		$this->sort_arr = get_sort_arr();
		$this->tab_arr = get_tab_arr();
		$this->display();
	}
	
	public function getfield($Model, $table){
		$find = $this->_get('find');
		if($find == 'null' || $find == '') $find = 'vw.id is null';
		if($find == 'already') $find = 'vw.id is not null';
		if($find == 'all') $find = '1 = 1';
		
		$field = $this->_get('field');
		$this->field = $field;
		$search = $this->_get('search');
		$this->search = $search;
		
		if($field == 'id') $find = "v.id=$search";
		if($field == 'cid') $find = "vw.cid=$search";
		if($field == 'title') $find = "v.title like '%$search%'";
		if($field == 'time'){
			$search = str_replace("^^", "-", $search);
			$find = "v.time like '%$search%'";
		}
		$this->search_field = array(
			'id'	=> '本地ID',
			'cid'	=> '主站ID',
			'title'	=> '标题',
			'time'	=> '时间',
		);
		
		return $Model->table('video v')
			->join($table.' vw on v.id=vw.id')
			->field('v.*, vw.cid')
			->where($find);
	}
	
	public function video_sync(){
		$video_websize = get_video_websize();
		$table = $this->_post('table');
		foreach($video_websize as $item){
			if($item['table'] == $table){
				$websize = $item['websize'];
				$wtime = $item['wtime'];
				$process = $item['process'];
				$key = $item['key'];
				break;
			}
		}
		
		$find['v.id'] = array('in', $_POST['batch']);
		$find['v.play'] = 'T';
		$find['v.img']=array('like','OK%');
		//$find['_string'] = 'vw.id is null';
		$Model = new Model();
		$data = $Model->table('video v')
			->join($table.' vw on v.id=vw.id')
			->field('v.*, vw.cid')
			->where($find)->select();
		$time = get_server_time($websize.$wtime);
		$html = data_add($websize.$process, json_encode($data), $time, encrypt($time, $key));
		$html = json_decode($html);
		$admin = M($table);
		foreach($html as $item){
			$data['id'] = $item->id;
			$data['cid'] = $item->cid;
			$admin->add($data);
		}
		ToolsAction::return_url();
	}
	
	public function modify(){
		$admin = M($this->table);
		
		$video_dir = get_video_dir();
		$data = $admin->find($this->_post('id'));
		$vip = $this->_post('vip');
		$path = $data['dir'].'/'.$data['id'];
		
		if($vip == 'T') rename('.'.$video_dir['base'].$path, '.'.$video_dir['vip'].$path);
		if($vip == 'F') rename('.'.$video_dir['vip'].$path, '.'.$video_dir['base'].$path);
		
		$admin->create();
		$admin->save();
	}
	
	public function search(){
		$search = $this->_get('search');
		$this->search = $search;
		if(strpos($search, '^^')){
			$search = str_replace("^^", "-", $search);
			$data['time'] = array('like', '%'.$search.'%');
		} else $data['title'] = array('like', '%'.$search.'%');
		return $data;
	}
	
	public function set_field_false(){
		$field = $this->_get('field');
		$val = $this->_get('val');
		if($field != '' && $field != null && $val != '' && $val != null){
			$data[$field] = array('like', $val.'%');
			return $data;
		}
		if($data = VideoAction::search()) return $data;
		else return false;
	}
	
	public function video_play(){
		$admin = M($this->table);
		$this->v = $admin->find($this->_get('id'));
		$this->display();
	}
	
	public function video_get_vl(){
		$name = 'batch';
		$admin = M($this->table);
		$ffmpeg = get_ffmpeg();
		$video_dir = get_video_dir();
		$video_set = get_video_set();
		$web_dir = str_replace("\\", "/", get_web_dir());
		for($i=0;$i<count($_POST[$name]);$i++){
			$data = $admin->find($_POST[$name][$i]);
			$file = $web_dir.$video_dir['in'].$data['file'];
			$result = video_info($file,$ffmpeg);
			$data['vl'] = video_info_vl($result['duration']);
			$data['resolution'] = $result['resolution'];
			$data['bitrate'] = $result['bitrate'];
			$data['vip'] = ($result['seconds'] > $video_set['video_vip_second']) ? 'T' : 'F';
			$admin->save($data);
		}
		ToolsAction::return_url();
	}
	
	public function video_get_img(){
		$name = 'batch';
		$admin = M($this->table);
		$video_dir = get_video_dir();
		$video_set = get_video_set();
		
		$ffmpeg = get_ffmpeg();
		$web_dir = str_replace("\\", "/", get_web_dir());
		for($i=0;$i<count($_POST[$name]);$i++){
			$data = $admin->find($_POST[$name][$i]);
			$file = $web_dir.$video_dir['in'].$data['file'];
			$path_arr = video_create_dir_by_custom($video_dir['img'], $data['dir'], $data['id']);
			
			if($data['vip'] == 'T') video_create_dir_by_custom($video_dir['vip'], $data['dir'], $data['id']);
			else video_create_dir_by_custom($video_dir['base'], $data['dir'], $data['id']);
			$path = $web_dir.$path_arr;
			
			$second = 0;
			$img_resolution = $video_set['img_resolution'];
			$img_count = $video_set['img_count'];
			if($data['vl'] == 'F' || $data['vl'] == 'E') continue;
			$rate = get_second($data['vl'], $img_count);
			for($j = 1; $j <= $img_count; $j++){
				$second += $rate;
				if(strlen($j) == 1) $s = '0'.$j;
				else $s = $j;
				$command = sprintf($ffmpeg.' -ss %s -i "%s" -y -f  image2 -s %s -vframes 1  %s',
					$second, $file, $img_resolution,
					$path.'/img'.$s.'.jpg'
					);
				passthru($command);
			}
			$data['img'] = 'T';
			$admin->save($data);
		}
		ToolsAction::return_url();
	}
	
	public function video_get_img_for_admin(){
		$admin = M($this->table);
		$data = $admin->find($this->_post('id'));
		$video_dir = get_video_dir();
		
		$path = $video_dir['img'].$data['dir'].'/'.$data['id'];
		$dir = get_dir_scandir('.'.$path);
		
		$item = explode('|', $data['img']);
		$face = explode(':', $item[1]);
		$face = $face[1];
		$set = explode(':', $item[2]);
		$set = $set[1];
		
		$code = array();
		$code['id'] = $data['id'];
		$code['title'] = $data['title'];
		$code['face'] = $face;
		$code['set'] = $set;
		$code['path'] = $path.'/';
		$code['file'] = $dir;
		
		echo json_encode($code);
	}
	
	public function video_set_img(){
		$admin = M($this->table);
		
		$img_str = $this->_post('img_str');
		$img_str = explode(',', $img_str);
		
		$data = $admin->find($this->_post('id'));
		$video_dir = get_video_dir();
		
		$path = '.'.$video_dir['img'].$data['dir'].'/'.$data['id'];
		$dir = get_dir_scandir($path);
		foreach($dir as $key){
			$sym = true;
			foreach($img_str as $img_item){
				if($key['batch'] == $img_item) $sym = false;
			}
			if($sym) unlink($path.'/'.$key['batch']);
		}
		$data['img'] = $this->_post('img');
		if($admin->save($data)) echo 'T';
		else echo 'F';
	}
	
	public function video_get_recode(){
		$name = 'batch';
		$admin = M($this->table);
		$video_dir = get_video_dir();
		$web_dir = str_replace("\\", "/", get_web_dir());
		$code = array();
		for($i=0;$i<count($_POST[$name]);$i++){
			$data = $admin->find($_POST[$name][$i]);
			$file = $web_dir.$video_dir['in'].$data['file'];
			
			$file_path_re = $data['dir'].'/'.$data['id'].'/'.$data['play_file'];
			if($data['vip'] == 'T') $file_path = $video_dir['vip'].$file_path_re;
			else $file_path = $video_dir['base'].$file_path_re;
			
			$code[$i]['id'] = $data['id'];
			$code[$i]['title'] = $data['title'];
			$code[$i]['file_path'] = $web_dir.$file_path;
			//$code[$i]['file_path_re'] = '.'.$file_path;
			if($data['dir'] == 'F') $code[$i]['file_path'] = 'F';
			$code[$i]['file'] = $file;
			$code[$i]['bitrate'] = $data['bitrate'];
			$code[$i]['resolution'] = $data['resolution'];
			$code[$i]['vip'] = $data['vip'];
			$code[$i]['file_count'] = '0';
			$code[$i]['time_die'] = 'F';
			$code[$i]['time'] = 'F';
			$code[$i]['state'] = 'F';
		}
		$this->code = $code;
		$this->display('video_thread');
	}
	
	public function video_recode(){
		exit;
		$id = $this->_post('id');
		$file_path = $this->_post('file_path');
		$file = $this->_post('file');
		
		$ffmpeg = get_ffmpeg();
		$video_set = get_video_set();
		$video_thread = $video_set['video_thread'];
		$video_resolution = $video_set['video_resolution'];
		$video_file = $file_path.'/'.$video_set['video_file'];
		
		$command = sprintf('%s -threads %s -y -i "%s" -s %s -b:v 500k -hls_time 10 -hls_list_size 0 -c:v libx264 -b:a 64k -ar 22050 -ac 1 -c:a aac -strict -2 -f hls "%s"',
			$ffmpeg, $video_thread, $file, $video_resolution, $video_file
			);
		$result = exec($command, $arr, $return);
		
		$admin = M($this->table);
		$data['id'] = $id;
		$data['play'] = ($return == 0) ? 'T' : 'E';
		$admin->save($data);
	}
	
	public function video_set_ok(){
		$admin = M($this->table);
		$data['id'] = $this->_post('id');
		$data['play'] = 'T';
		$admin->save($data);
	}
	
	public function video_state_get(){
		$admin = M($this->table);
		$id = explode(',', $this->_post('id'));
		$data['id']=array('in', $id);
		$arr = ToolsAction::result_all($admin, $data, false, false);
		
		$video_dir = get_video_dir();
		
		
		$return = array();
		for($i = 0; $i < count($arr); $i++){
			$return[$i]['id'] = $arr[$i]['id'];
			$return[$i]['play'] = $arr[$i]['play'];
			
			if($arr[$i]['vip'] == 'T') $path = '.'.$video_dir['vip'];
			if($arr[$i]['vip'] == 'F') $path = '.'.$video_dir['base'];
			$return[$i]['count'] = count(get_dir_scandir($path.$arr[$i]['dir'].'/'.$arr[$i]['id']));
		}
		echo json_encode($return);
	}
	
	public function delete_batch(){
		$video_dir = get_video_dir();
		$name = 'batch';
		$admin = M($this->table);
		for($i=0;$i<count($_POST[$name]);$i++){
			$data = $admin->find($_POST[$name][$i]);
			unlink('.'.$video_dir['in'].$data['file']);
			deldir('.'.$video_dir['img'].$data['dir'].'/'.$data['id']);
			deldir('.'.$video_dir['base'].$data['dir'].'/'.$data['id']);
			deldir('.'.$video_dir['vip'].$data['dir'].'/'.$data['id']);
			$admin->delete($_POST[$name][$i]);
		}
		ToolsAction::return_url();
	}
	
	public function process(){
		$process = $this->_post('process');
		if($process == 'delete') VideoAction::delete_batch();
		if($process == 'video_get_vl') VideoAction::video_get_vl();
		if($process == 'video_get_img') VideoAction::video_get_img();
		if($process == 'video_get_recode') VideoAction::video_get_recode();
		if($process == 'video_sync') VideoAction::video_sync();
		
	}
	
	public function ajax_video_modify(){
		$data['id'] = $this->_post('id');
		$data[$this->_post('key')] = $this->_post('val');
		if(M($this->table)->save($data)) echo "OK";
		else echo "NOT OK";
	}
	
	public function ajax_set_session(){
		session($this->_get('key'), $this->_get('val'));
	}
}