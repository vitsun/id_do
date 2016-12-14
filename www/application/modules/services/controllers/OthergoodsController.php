<?php
function getothercmp($a,$b)
{
	if ($a['i_sort'] == $b['i_sort']) {
		return 0;
	}

	return ($a['i_sort'] > $b['i_sort'] ? 1 : -1);
}

class Services_OthergoodsController extends Zend_Controller_Action
{
	public function preDispatch()
	{
	}

	public function getotherAction()
	{
		$max_count = 10;
		$id = $this->_getParam('id');
		$ar_res = array();

		if ($id) {
			$Product = new Product();
			$Category = new Category();

			$product = $Product->get($id);
   				$cat_id = $product['categoryId'];
   				$category = $Category->get($cat_id);
   				$ar_cats = explode(",",$category['children']);

   				$toys = $Product->get_analogs($id,10);

			foreach ($toys as $toy) {
				$ar_res[] = array(
					'id' => $toy['id'],
					'name' => iconv('cp1251','utf-8',$toy['name']),
					'price' => $toy['price'],
					'url' => $this->view->url(array("action" => "detail", "controller" => "toy" ,"toy_id" => $toy['id']),null,true),
					'picture' => $toy['picture'],
					'i_sort' => ($toy['categoryId'] == $cat_id ? 1 : (in_array($toy['categoryId'],$ar_cats) ? 2 : 3))
				);
			}
		}

		usort($ar_res,'getothercmp');
		$ar_res = array_slice($ar_res,0,$max_count);
		print_r(json_encode($ar_res));
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		//return ($ar_res);
	}

	public function getimgAction()
	{
		$res = '';

		$id = $this->_getParam('id');

		if ($id) {
			$Product = new Product();
			$product = $Product->get($id);

			if ($product) {
				$res = $product['picture'];
			}
		}

		print_r($res);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
}