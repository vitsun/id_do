<?php
class Services_CheckAvailableController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}

	public function indexAction()
	{
		$id = $this->_getParam('id');

		$ar_res = array(
			'type' => 1,
			'availability_code' => 0
		);


		if ($id) {
			$ar_from_api = $this->avalAPIRequest($id);
			if ($ar_from_api && $ar_from_api['error'] == 0) {
				$ar_res = $ar_from_api;
				$ar_res['type'] = 2;
			} else {
				$cont = $this->sendCurlResponse($id);
				if ($cont !== false) {
					$ar_res['availability_code'] = $this->checkContent($cont);
				}
			}
		}

 		print_r(json_encode($ar_res));

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	protected function avalAPIRequest($id)
	{
		$ar_res = array();

		$Settings = new Settings();

		$settings = $Settings->get('partner_id');
		$partner_id = $settings['s_val'];

		$settings = $Settings->get('auth_code');
		$auth_code = $settings['s_val'];

		$url_page = 'https://my-shop.ru/cgi-bin/p/info.pl';
		$post_str = "version=1.8&partner={$partner_id}&auth_method=plain&auth_code={$auth_code}&request=product&id={$id}";
		$s = GetWebPage($url_page,$RetStatus,$post_str,true);
          //print_r($s);
		if (preg_match('/<availability_code>(.*)<\/availability_code>.*<cost>(.*)<\/cost>.*<error>(.*)<\/error>/Usi',$s,$r)) {
			$ar_res['availability_code'] = ( (($r[1] == 2) or ($r[1] == 3)) ? 1 : 0 );
			$ar_res['cost'] = number_format($r[2],2,'.','');
			$ar_res['error'] = $r[3];

			$Product = new Product();
			$Product->check_prodcut_price($id,$r[2]);
		}

		if (preg_match('/<sale_cost>(.*)<\/sale_cost>.*<sale_limit>(.*)<\/sale_limit>.*<sale_percent>(.*)<\/sale_percent>/Usi',$s,$r)) {
			$ar_res['sale_cost'] = number_format($r[1],2,'.','');
			$ar_res['sale_limit'] = $r[2];
			$ar_res['sale_percent'] = $r[3];
		}

		if (preg_match('/<time_text_a>(.*)<\/time_text_a>/Usi',$s,$r)) {
			$ar_res['time_text_a'] = iconv('cp1251','utf-8',$r[1]);
		}

		return ($ar_res);
	}

	public function getlistAction()
	{
		$ar_res = array();

		$ids = $this->_getParam('ids');
		if ($ids) {
			$ar_ids = explode(",",$ids);

			foreach ($ar_ids as $key => $val) {
				$ar_ids[$key] = $val."-1";
			}

			$ids_str = implode(",",$ar_ids);

			$Settings = new Settings();

			$settings = $Settings->get('partner_id');
			$partner_id = $settings['s_val'];

			$settings = $Settings->get('auth_code');
			$auth_code = $settings['s_val'];

			$url_page = 'https://my-shop.ru/cgi-bin/p/info.pl';
			$post_str = "version=1.8&partner={$partner_id}&auth_method=plain&auth_code={$auth_code}&request=list_cart&cart={$ids_str}";
			$s = GetWebPage($url_page,$RetStatus,$post_str,true);

			if (preg_match('/<error>(.*)<\/error>/Usi',$s,$r)) {
				if ($r[1] == 0) { // no error
					if (preg_match_all('/<id>(.*)<\/id>.*<cost>(.*)<\/cost>/Usi',$s,$t)) {
						$Product = new Product();
						foreach ($t[1] as $key => $val) {
							$ar_res[] = array(
								'id' => $t[1][$key],
								'cost' => number_format($t[2][$key],2,'.','')
							);

							$Product->check_prodcut_price($t[1][$key],$t[2][$key]);
						}
					}
				}
			}
		}

		if ($ar_res) {
			print_r(json_encode($ar_res));
		} else {
			print_r(json_encode('err'));
		}

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	protected function sendCurlResponse($id)
	{
		global $part_id;
		$server = "http://p.my-shop.ru/cgi-bin/myorder.pl";
		$request = "partner={$part_id}&master=&cart={$id}-1&cartsource=get";

		/*
		$ch = curl_init($server);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt ($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
		curl_setopt ($ch, CURLOPT_PROXY,"192.168.5.1:8080");
		curl_setopt ($ch, CURLOPT_PROXYUSERPWD,"respect\prog3:128146");

		$content = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if (empty($curl_error)) {
			$res = $content;
		} else {
			$res = false;
		}
		*/

		$res = GetWebPage($server,$RetStatus,$request,true);

		return $res;
	}

	protected function checkContent($text)
	{
		$pos = empty($text);
		if ($pos===false) {
			$res = 1;
		} else {
			$res = 0;
		}

		return $res;
	}

	public function tryBuyAction()
	{
		$id = $this->_getParam('id');

		if ($id) {
			$Settings = new Settings();
			$settings = $Settings->get('partner_id');

			$res = '';

			$res .= '<html><body>';
			$res .= '<form id="frm_'.$id.'" method="POST" action="http://p.my-shop.ru/order">'
							.'<input name="action" value="cartAddItem">'
							.'<input name="partner" value="'.$settings['s_val'].'">'
							.'<input name="quantity" value="1">'
							.'<input name="item" value="'.$id.'">'
							.'</form>';
			$res .= '</body></html>';
		}

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		echo $res;
	}
}
?>