<?php
class CategoryController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}
	
	public function indexAction()
	{
		$categories = new Category();
		
		//$this->view->categories = array ('1','2','3','4');
		//$this->view->categories = $categories->fetchAll();
		
		//$this->_helper->actionStack('detail-list');
		//$this->_forward('detail-list');
		//$this->rrr();
		$front = Zend_Controller_Front::getInstance();

		
		
		if ($front->getRequest()->getParam('controller') == 'toy') {
			$toy_id = $front->getRequest()->getParam('toy_id');
			$Product = new Product();
			$toy = $Product->get($toy_id);
			$cat_id = $toy['categoryId'];
			$this->view->current_id = $cat_id;
			$this->view->p_type = 'toy';
		} else {
			$cat_id = $front->getRequest()->getParam('cat_id');
			$cat_id = ($cat_id ? $cat_id : 0);
			$this->view->current_id = $cat_id;
			$this->view->p_type = 'category';
		}
		//print_r($cat_id);exit;
		$this->view->categories = $categories->get_sort_categories($cat_id);
	}
		
	public function detailListAction()
	{		
		//$this->_forward('get-top-toys','toy');
		$cat_id = $this->_getParam('cat_id');
		$this->_forward('get-toys-from-category','toy',null,array('categoryId' => $cat_id));
	}
}
