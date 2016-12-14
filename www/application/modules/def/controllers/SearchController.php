<?php
class SearchController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}
	
	public function indexAction()
	{
		$search_text = $this->getRequest()->getParam('search');
		$this->view->search_text = ($search_text ? $search_text : "поиск");
	}
	
	public function beginSearchAction()
	{
		if ($this->getRequest()->isGet()) {
			if ($search_text = $this->_getParam('search')) {
				$this->_forward('get-toys-by-search-text','toy',null,array('search_text' => $search_text));
			} else {
				//$this->_forward('index','index',null,array());
				$this->_redirect(''); // redirect to index page
			}
		}
	}
}
?>