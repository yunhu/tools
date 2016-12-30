<?php
define('DIR', __DIR__);
function fork($num, $func, $debug=1) {
	$ppid = posix_getpid();
	for ($i = 0; $i < $num; ++$i) {
		$pid = pcntl_fork();
		if ($pid < 0) {
			exit('error to fork!');
		} else if ($pid) {
		} else {
			if (posix_getppid() == $ppid) {
				$data['p'] = $i;
				#$data['pid'] = $ppid;
				$data['time'] = time();
				$p = '';
				if($i < 10){
					$p = '0' . $i;
				}else{
					$p = $i;
				}
				$data['file'] = DIR . '/cutdata/cut_' . $p;
				if($debug)
					echo '总共有' . $num . '个进程,第'. $i .'个进程已经启动' . "\n";
				call_user_func($func, $data);
			} else {
				exit();
			}
		}
	}
	$status = null;
	pcntl_wait($status);
}

start($argv);
function start($ar){
	$file = isset($ar[1]) ? $ar[1] : '';
	$p = isset($ar[2]) ? abs(intval($ar[2])) : '';
	$file = ltrim($file , '.');
	$file = ltrim($file , '/');
	$d = $dir  . '/'  . rtrim($file, '/') ;
	if($p >= 100){
		echo "进程最多100个，已经更新参数！\n";
		$p = 100;
	}
	if(!is_file($d)) $d = DIR . $d;
	if(!is_file($d)){
		//echo '文件地址输入错误!' . "\n";
		echo "like php index.php data.txt 2 \n";
		exit();
	}

	if ($p <1) exit('进程数不能小于1');

	$tmpdir =  DIR . '/cutdata/';
	$tmpdatadir =  DIR . '/tmpdata/';
	if(is_dir($tmpdatadir)){
		exec("rm -rf $tmpdatadir" );
	}
	if(is_dir($tmpdir)){
		exec("rm -rf $tmpdir" );
	}
	if(mkdir($tmpdatadir)){
		echo 'ok';
	}else{
		exit('文件创建失败');
	}
	if(mkdir($tmpdir)){
		echo "正在获取文件信息 \n";
//		$num = getlinenum($d);
		$num = trim(exec("cat  $d |wc -l" ));
		if($num > 1){
			$cutnum = ceil($num / $p);
			echo "正在准备切文件\n";
			exec("split -a 2 -d -l $cutnum $d  $tmpdir" . "cut_");
			fork($p, 'deal');
		}else{
			exit('empty file! exit!');
		}
	}else{
		exit('文件创建失败');
	}


}


function deal($arr){
	if(file_exists($arr['file'])){
		$dir = scandir(DIR . '/lib/');
		include_once DIR . '/' . $dir[2];
		$name = explode('.', $dir[2]);
		new $name[0]($arr['file']);
	}else{
		echo "no file {$arr['p']} , {$arr['file']}\n";
		exit();
	}
}
