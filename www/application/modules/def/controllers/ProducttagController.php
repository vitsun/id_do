<?php
class ProductTagController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}
	
	public function getFilterTagsAction()
	{
		$show_view = false;
		
		$front = Zend_Controller_Front::getInstance();
		
		if ($front->getRequest()->getParam('controller') == 'category') {
			$cat_id = $front->getRequest()->getParam('cat_id');
			if ($cat_id) {
				$Category = new Category();
				$category = $Category->get($cat_id);
				
				if (trim($category['children']) == $cat_id) {
					$ProductTag = new ProductTag();
					$filter_tags = $ProductTag->get_top_popular($cat_id);
					if (count($filter_tags) > 0) {
						$this->view->filter_tags = $filter_tags;
						$show_view = true;
					}
				}
			}
		}
		
		if (!$show_view) {
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();		
		}		
	}
}
