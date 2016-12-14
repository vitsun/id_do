<?php
/**
 *
 */
class Category
//class Application_Model_Category
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
        $this->__setDbModel('Category_Db');
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
		return ($res);
	}

  /**
   *
   */
	public function get_sort_categories($id=0)
	{
		if (!$this->get($id)) {
			$id = 0;
		}
		$res = $this->__getDbModel()->get_sort_categories($id);
		return ($res);
	}

} 