<?php
	require ('getwebpage.php');
	require ('mod.db.php');
	
	function write_log($s)
	{
		$f = fopen('log.txt','a');
		fwrite($f,date('d.m.Y').' '.$s."\n");
		fclose($f);
	}
		
	function get_x_y()
	{
		$x = 1000;
		$y = 1000;
		
		// toy_45x67
		//$_GET['file_name'] = 'toy_45x67';
		if (isset($_GET['file_name'])) {
			if (preg_match('/_([0-9]+)x([0-9]+)$/iU',$_GET['file_name'],$r)) {
				$x = $r[1];
				$y = $r[2];
			}
		}
		
		return (array('x' => $x, 'y' => $y));
	}
	
	function main_f()
	{
		if (isset($_SERVER['HTTP_REFERER'])) {
			$ar = get_words($_SERVER['HTTP_REFERER']);
			connect();
			
			if (count($ar) > 0) {
				foreach (array_reverse($ar,true) as $key => $val) {
					$sres = sql("select picture, name from ln_product where name like '%".$key."%' and picture is not null order by rating desc limit 0,1");
					if ($ar_res = mysql_fetch_assoc($sres)) {
						//write_log($key);
						get_picture($ar_res['picture']);
						break;
					}
				}
			}
			
			disconnect();
		}
	}
	
	function get_encoding_type($ar_data)
	{
		$res = '';
		
		//$ar_data['content_type'] = 'Content-Type	text/html; charsett=utf-8';
		//$ar_data['content'] = '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">';
		
		if ( preg_match ("~charset=(.+)[?\ ]*~i",$ar_data['content_type'],$r)) {
			$res = trim ($r[1]);
		/*} elseif ( preg_match ("~<meta[ \r\n\t]{1}[?>]*charset[?=]*="."([? \"'>\r\n\t#]+)[ '\"\n\r\t]*[?>]*>~is", $ar_data['content'], $r)) {*/
		} elseif ( preg_match ("/<meta.+Content\-Type.+charset=(.+)['\"]{1}/isU", $ar_data['content'], $r)) {
			$res = trim ($r[1]);
		}
		
		return ($res);
	}
	
	function get_words($ref)
	{
		$ar = array();
		
		//$s = implode('',file('3.html'));
		//$s = GetWebPage($ref,$ar_data,"",false,false);
		$s = GetWebPage($ref,$ar_data);
		$source_encoding = get_encoding_type($ar_data);
		
		if ($source_encoding == '') {
			write_log('error encoding: '.$ref);
		} elseif ($source_encoding !== 'windows-1251') {
			$s = iconv($source_encoding,'windows-1251',$s);
		}
		
		for ($ih=1;$ih<4;$ih++) {
			if (preg_match_all('/<h'.$ih.'.*>(.+)<\/h'.$ih.'>/isU',$s,$r)) {
				foreach ($r[1] as $key => $val) {
					$s_h = str_replace('</',' </',$val);
					$s_h = strip_tags($s_h);
					
					if (preg_match_all('/([à-ÿÀ-ÿ]{4,20})(?:(\s)||($))/',$s_h,$r_h)) {
						foreach ($r_h[1] as $k => $v) {
							$ar[$r_h[1][$k]] = (isset($ar[$r_h[1][$k]]) ? $ar[$r_h[1][$k]] : 1000) + 1;
						}
					}
				}
			}
		}
		
		$s = preg_replace('/<a.+href=.+>.+<\/a>/isU','',$s);
		$s = str_replace('</',' </',$s);
		$s = strip_tags($s);
					
		if (preg_match_all('/([à-ÿÀ-ÿ]{4,20})(?:(\s)||($))/',$s,$r)) {
			//write_log('sss');exit;
			//print_r($r[1]);
			foreach ($r[1] as $key => $val) {
				$ar[$r[1][$key]] = (isset($ar[$r[1][$key]]) ? $ar[$r[1][$key]] : 0) + 1;
			}
			asort($ar,SORT_NUMERIC);
		}
		
		return ($ar);
	}

	function MakeOutputImageFile($fname,$tumbax,$ar_attrib)
	{
		$im = imagecreatetruecolor($tumbax, $ar_attrib['y']*$tumbax/$ar_attrib['x']);
		$color3 = imagecolorallocate($im, 255, 255, 255);
		imagefill ($im, 2, 2, $color3);
		imagecopyresampled ($im,$ar_attrib['imsource'],0,0,0,0,$tumbax,$ar_attrib['y']*$tumbax/$ar_attrib['x'],$ar_attrib['x'],$ar_attrib['y']);
		ImageJPEG ($im,$fname);
		ImageDestroy ($im);
	}
	
	function get_picture($url)
	{
		//$url = 'http://my-shop.ru/_files/product/2/43/428102.jpg';
		//$url = 'http://my-shop.ru/_files/product/2/48/478234.jpg';
		$file_name = 'files/'.uniqid();
		
		$s = GetWebPage($url,$ar_data);
		
		if ($ar_data['ret_status'] ==  200) {
			$f = fopen($file_name,'wb');
			fwrite($f,$s);
			fclose($f);
			
			$img = getimagesize($file_name);
			switch ($img[2]) {
				case 1 : $imsource = ImageCreateFromGif($file_name);break;
				case 2 : $imsource = ImageCreateFromJpeg($file_name);break;
				case 3 : $imsource = ImageCreateFromPng($file_name);break;
			}
			
			$ar_x_y_new = get_x_y();
			$x_new = $ar_x_y_new['x'];
			$y_new = $ar_x_y_new['y'];
			//write_log($x_new.'-'.$y_new);
			
			if ( ($x_new < $img[0]) || ($y_new < $img[1]) ) {
				$x_del = $x_new / $img[0];
				$y_del = $y_new / $img[1];
				
				if ($x_del < $y_del) {
					$del = $x_del;
				} else {
					$del = $y_del;
				}
				
				$small_file_name = $file_name."_1";
				MakeOutputImageFile($small_file_name,floor($del*$img[0]),array('x' => $img[0],'y' => $img[1],'imsource' => $imsource));
				
				if (file_exists($small_file_name)) {
					$s_new = implode('',file($small_file_name));
					unlink($small_file_name);
					header("Content-type: {$img['mime']}");
					echo $s_new;
				}
			} else {
				header("Content-type: {$img['mime']}");
				echo $s;
			}
	    
			unlink($file_name);
		}
	}
	
	//get_picture('ss');
	main_f();
	//print_r(get_words('http://lyntik.ru/index.php?act=browse&topcat=5&cat=3185&page=1'));
	//print_r(get_x_y());
?>