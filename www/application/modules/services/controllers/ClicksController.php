<?php
class Services_ClicksController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}
	
	public function indexAction()
	{
	}
	
	public function saveAction()
	{
		$url = $this->_getParam('url');
		$toy_id = $this->_getParam('toy_id');
		
		$data = array (
			'clicks_datetime' => time()+10*60*60,
			'clicks_url' => $url,
			'clicks_toy_id' => $toy_id,
			'clicks_user_uid' => USER_UID
		);
		
		$Clicks = new Clicks();
		
		$Clicks->insert($data);

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();		
	}
}
?>