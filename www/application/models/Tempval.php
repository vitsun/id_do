<?php
/**
 *
 */
class Tempval
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
        $this->__setDbModel('Tempval_Db');
    }
    return $this->_dbTable;
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

	
} 