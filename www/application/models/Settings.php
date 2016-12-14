<?php
/**
 *
 */
class Settings
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
        $this->__setDbModel('Settings_Db');
    }
    return $this->_dbTable;
  }

	/**
	*
	*/
	public function validate($action='add')
	{
		//	                 
		return $this;
	}
	
	/**
	*
	*/
	protected function _prepare($action = '')
	{
		//
		//$this->_data['s_val'] = serialize($this->_data['s_val']);
		//
		return $this->_data;
	}

  /**
   *
   */
  public function set($id,$val)
  {
  	$this->__getDbModel()->update(array('s_val' => $val),array("s_key='".$id."'"));
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
	public function get_all_settings()
	{
		$settings = $this->__getDbModel()->fetchAll();//getAll();
		$res = array();
		foreach ($settings as $key => $val) {
			$res[$val['s_key']] = $val['s_val'];
		}
		
		return ($res);
	}
	
} 