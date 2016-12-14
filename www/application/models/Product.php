<?php
/**
 *
 */
class Product
{
  protected $_dbTable;

  /**
   *
   */
  protected function __setDbModel($dbTable)
  {
    if (is_string($dbTable)) {
        $dbTable = new $dbTable();
    }
    if (!$dbTable instanceof Zend_Db_Table_Abstract) {
        throw new Exception('Invalid table data gateway provided');
    }
    $this->_dbTable = $dbTable;
    return $this;
  }

  /**
   *
   */
  public function __getDbModel()
  {
    if (null === $this->_dbTable) {
        $this->__setDbModel('Product_Db');
    }
    return $this->_dbTable;
  }

  /**
   *
   */
  public function validate($action = 'add')
  {
    //$this->_validation['address']  = strlen($this->_data['address']);

    return $this;
  }

  /**
   *
   */
	public function get($id)
	{
		$res = $this->__getDbModel()->find($id);

		if (0 == count($res)) {
	    return false;
    }

		return($res->current()->toArray());
	}

  /**
   *
   */
	public function fetchAll()
	{
		$args = func_get_args();

		$res = $this->__getDbModel()->fetchAll(@$args[0],@$args[1],@$args[2],@$args[3]);
		//$res = $this->__getDbModel()->fetchAll(null,null,10,0);
		return ($res);
	}

  /**
   *
   */
	public function getProductCount($categoryId)
	{
		$fr = Zend_Controller_Front::getInstance();
		$mn = $fr->getParam('bootstrap')->getPluginResource('cachemanager')->getCacheManager();
		$dbCache = $mn->getCache('database');
		$cahce_name = 'prcount_'.md5($categoryId);

		if(!$res = $dbCache->load($cahce_name)) {
			$res = $this->__getDbModel()->getProductCount($categoryId);
			$dbCache->save($res, $cahce_name);
		}

		return ($res);
	}

  /**
   *
   */
	public function get_products_by_category($categoryId,$options,&$rPaginator)
	{
		//return $this->__getDbModel()->fetchAll('categoryId='.$categoryId)->toArray();
		$options['where'] = (isset($options['where']) ? $options['where'] . " and " : "") . "categoryId=".$categoryId;
		return $this->__getDbModel()->getAll($options,$rPaginator);
	}

  /**
   *
   */
	public function get_new_products_by_category($categoryId,$count)
	{
		$fr = Zend_Controller_Front::getInstance();
		$mn = $fr->getParam('bootstrap')->getPluginResource('cachemanager')->getCacheManager();
		$dbCache = $mn->getCache('database');
		$cahce_name = 'prnewincat_'.md5($categoryId).'_'.$count;

		if(!$res = $dbCache->load($cahce_name)) {
			$res = $this->__getDbModel()->fetchAll('categoryId in ('.$categoryId.')',array('new ASC','(rating*price) DESC'),$count)->toArray();
			$dbCache->save($res, $cahce_name);
		}

		return ($res);
	}

  /**
   *
   */
	public function get_new_products($count)
	{
		$fr = Zend_Controller_Front::getInstance();
		$mn = $fr->getParam('bootstrap')->getPluginResource('cachemanager')->getCacheManager();
		$dbCache = $mn->getCache('database');
		$cahce_name = 'prnewmain_'.$count;

		if(!$res = $dbCache->load($cahce_name)) {
			$res = $this->__getDbModel()->fetchAll(null,array('new ASC','(rating*price) DESC'),$count)->toArray();
			$dbCache->save($res, $cahce_name);
		}

		return ($res);
	}

  /**
   *
   */
	public function get_products_by_search_text($search_text,$options,&$rPaginator)
	{
		$search_text = $this->__getDbModel()->getDefaultAdapter()->quote($search_text);
		//$options['where'] = (isset($options['where']) ? $options['where'] . " and " : "") . "name like '%".$search_text."%'";
		$options['where'] = (isset($options['where']) ? $options['where'] . " and " : "") . "match(name,description) against(".$search_text.")";
		//$options['match'] = "match(name,description) against('".$search_text."' in boolean mode)";
		return $this->__getDbModel()->getAll($options,$rPaginator);

		//return $this->__getDbModel()->fetchAll("name like '%".$search_text."%'")->toArray();
	}

  /**
   *
   */
	public function get_put_products($ar_put)
	{
		return $this->__getDbModel()->fetchAll("id in (".implode(",",$ar_put).")",'new ASC')->toArray();
	}

  /**
   *
   */
	public function get_all_new_products()
	{
		$ar = array();

		$Tempval = new Tempval();
		$last_update = $Tempval->get('last-update');
		if ( ($last_update['val']) && ( ($razn = floor((time()-$last_update['val'])/60/60/24)) <= 10) ) {
			foreach ($this->__getDbModel()->fetchAll('new between 0 and '.(10-$razn),'new ASC',200)->toArray() as $item) {
				$ar[] = $item['id'];
			}
		}

		return $ar;
	}

  /**
   *
   */
	public function get_popular_products()
	{
		$ar = array();

		foreach ($this->__getDbModel()->fetchAll(null,'rating DESC',100)->toArray() as $item) {
			$ar[] = $item['id'];
		}

		return $ar;
	}

  /**
   *
   */
	public function get_analogs($id,$count)
	{
		return $this->__getDbModel()->getAnalog($id,$count);
	}

  /**
   *
   */
	public function check_prodcut_price($id,$new_price)
	{
		$toy = $this->get($id);
		if ( ($toy) && ($toy['price'] != $new_price) ) {
			$this->__getDbModel()->update(array(
				'price' => $new_price
			),'id='.$id);
		}
	}

	public function get_all_by_fetch_each()
	{
		return $this->__getDbModel()->get_all_by_fetch_each();
	}
}