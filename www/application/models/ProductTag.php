<?php
/**
 *
 */
class ProductTag
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
        $this->__setDbModel('ProductTag_Db');
    }
    return $this->_dbTable;
  }
	
	public function get_top_popular($cat_id)
	{
		return $this->__getDbModel()->get_top_popular($cat_id);
	}	
} 