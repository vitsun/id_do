<?php
class Admin_IndexController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$this->_helper->layout->setLayoutPath(APPLICATION_PATH."/layouts/admin/scripts");
	}
	
	public function indexAction()
	{
		//$this->_helper->layout->setLayoutPath(APPLICATION_PATH."/layouts/admin/scripts");
		if ($this->isAuth()) {
			$this->_forward('show-clicks');
		} else {
			$this->_forward('login');
		}
	}
	
	public function loginAction()
	{
		if ($this->getRequest()->isPost()) {
			$Settings = new Settings();
			$settings = $Settings->get('admin');
			
			$pass = $this->_getParam('pass');
			
			if ($settings['s_val'] == md5($pass)) {
				setcookie('pass',md5($pass));
				$this->_redirect('admin');
			}
		}
	}
	
	protected function isAuth()
	{
		$Settings = new Settings();
		$settings = $Settings->get('admin');
		
		if (@$_COOKIE['pass'] == $settings['s_val'])
			return true;
		else
			return false;
	}
	
	public function showClicksAction()
	{
		$period_type = $this->_getParam('period_type');
		$period_type = ($period_type ? $period_type : 1); // today as default
		
		$this->view->period_type = $period_type;
		$this->view->ar_periods = array('1' => 'Сегодня',
																		'2' => 'Вчера',
																		'3' => 'Позавчера',
																		'4' => 'За неделю',
																		'5' => 'За 2 недели');

		switch ($period_type) {
			case 1 : {
				$t = time()+10*60*60;
				$date_begin = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 00:00:01');
				$date_end = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 23:59:59');
				break;
			}
			case 2 : {
				$t = time()+10*60*60-24*60*60;
				$date_begin = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 00:00:01');
				$date_end = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 23:59:59');
				break;
			}
			case 3 : {
				$t = time()+10*60*60-24*60*60*2;
				$date_begin = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 00:00:01');
				$date_end = strtotime(date('Y',$t).'/'.date('m',$t).'/'.date('d',$t).' 23:59:59');
				break;
			}
			case 4 : {
				$t1 = time()+10*60*60-24*60*60*7;
				$t2 = time()+10*60*60;
				$date_begin = strtotime(date('Y',$t1).'/'.date('m',$t1).'/'.date('d',$t1).' 00:00:01');
				$date_end = strtotime(date('Y',$t2).'/'.date('m',$t2).'/'.date('d',$t2).' 23:59:59');
				break;
			}
			case 5 : {
				$t1 = time()+10*60*60-24*60*60*7*2;
				$t2 = time()+10*60*60;
				$date_begin = strtotime(date('Y',$t1).'/'.date('m',$t1).'/'.date('d',$t1).' 00:00:01');
				$date_end = strtotime(date('Y',$t2).'/'.date('m',$t2).'/'.date('d',$t2).' 23:59:59');
				break;
			}
			case 6 : {
				break;
			}
		}
		
		$Clicks = new Clicks();
		$clicks = $Clicks->get_clicks($date_begin,$date_end);
		
		$this->view->date_begin = $date_begin;
		$this->view->date_end = $date_end;
		$this->view->clicks = $clicks;
		
		//echo date('d.m.Y H:i:s',$date_begin)."<br>";
		//echo date('d.m.Y H:i:s',$date_end);
		//exit;
	//SELECT date(from_unixtime( 1314262160 ) )
																		
	}
}
?>