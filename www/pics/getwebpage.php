<?php
	function GetWebPage($url_page,&$ar_data,$post_str="",$is_post=false,$is_proxy=true)
	{
		$ar_data = array();
		
		$ch = curl_init ($url_page);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		//curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
		curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
		//curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/temp/"."mail_coockie.txt");
		//curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/temp/"."mail_coockie.txt");
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION ,1); //0
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_COOKIE, 'fuid01=4b55eb3819e45ffc.GHz1qZGVLdiellfrdaV8oOurD-eyAQLruoiXkgwQlajZVIiK72GT1sl3vBlpr8MCD-dfUUrA7hZR_ahgXIXDZ-3EAqCx5Nfdnl4SSdbSbfPeOJCprMor9M0eB8hpEVX1;');
			
		if ($is_proxy) {
			//curl_setopt ($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
			//curl_setopt ($ch, CURLOPT_PROXY,"192.168.5.1:8080");
			//curl_setopt ($ch, CURLOPT_PROXYUSERPWD,"respect\prog3:128146");
		}
		
		if ($is_post)
		{
			curl_setopt ($ch, CURLOPT_POSTFIELDS,$post_str);
			curl_setopt ($ch, CURLOPT_POST,1);
		}
		
		$str_ret=curl_exec ($ch);
		$ar_data['ret_status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$ar_data['content_type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$ar_data['content'] = $str_ret;
		
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);		
		curl_close ($ch);
		
		if (empty($curl_error)) {
			$res = $str_ret;
		} else {
			$res = false;
		}
		
		return ($res);
	}	
?>