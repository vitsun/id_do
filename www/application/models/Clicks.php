<?php
class Clicks
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
        $this->__setDbModel('Clicks_Db');
    }
    return $this->_dbTable;
  }
  
  /**
   *
   */
  public function insert($data)
  {
  	$this->__getDbModel()->insert($data);
  }

  /**
   *
   */
  public function get_clicks($date_begin,$date_end)
  {
  	return $this->__getDbModel()->get_clicks($date_begin,$date_end);
  }
  
}
?>