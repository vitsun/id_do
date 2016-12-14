<?php
class FirstOfAll_Plugin extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$module=$request->getModuleName();
		$controller=$request->getControllerName();
		$action=$request->getActionName();

		if ( ($module == 'def') && ($controller == 'category') && ($action == 'detail-list') || ($module == 'def') && ($controller == 'search') && ($action == 'begin-search')  ) {
			if ( (get_magic_quotes_gpc()) && (isset($_GET['search'])) ) {
				$_GET['search'] = stripslashes($_GET['search']);
			}

			$ar_get = array();
			$ar_get = $_GET;
			$need_redirect = false;

			if (isset($_COOKIE['pl'])) {
				$pl = $_COOKIE['pl'];
				$pl = ( ($pl > 0 && $pl < 6) ? $pl : 0);
				if ( (!isset($ar_get['pl'])) && ($pl) ) {
					$ar_get['pl'] = $pl;
					$need_redirect = true;
				}
			}

			if (isset($_COOKIE['show'])) {
				$show = $_COOKIE['show'];
				$show = (in_array($show,array('list','grid')) ? $show : 'list');
				if ( (!isset($ar_get['show'])) && ($show) ) {
					$ar_get['show'] = $show;
					$need_redirect = true;
				}
			}

			$order = '';
			if (isset($_COOKIE['order'])) {
				$order = $_COOKIE['order'];
				$ar_order = array(
					'price',
					'rating'
				);

				if ($controller == 'search') {
					$ar_order[] = 'relevance';
				}

				$order = ( (in_array($order, $ar_order)) ? $order : '' );
				if ( (!isset($ar_get['order'])) && ($order) ) {
					$ar_get['order'] = $order;
					$need_redirect = true;
				}
			}

			if ( (isset($_COOKIE['orderby'])) && ($order) ) {
				$orderby = $_COOKIE['orderby'];
				$orderby = ( (in_array($orderby, array('desc', 'asc'))) ? $orderby : 'desc' );
				if (!isset($ar_get['orderby'])) {
					$ar_get['orderby'] = $orderby;
					$need_redirect = true;
				}
			}

			if ($need_redirect) {
				$url = parse_url($_SERVER['REQUEST_URI']);
				$response=$this->getResponse();
				$response->setRedirect(DOMAIN_PATH.$url['path'].'?'.from_ar_to_url($ar_get));
			}
		}

		if ($put_id = $request->getParam('put_id')) {
			put_toy($put_id);

			$response=$this->getResponse();
			$response->setRedirect(make_new_url($_SERVER['REQUEST_URI'],array(),array('put_id')));
		}

		/* смена модуля, контроллера и действия на лету
		$request->setModuleName('new')
		    ->setControllerName('new')
		    ->setActionName('new');
		*/

	}
}
?>