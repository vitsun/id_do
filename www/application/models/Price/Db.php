<?php
/**
 *
 */
class Price_Db extends Zend_Db_Table_Abstract
{
  /**
   *
   */
  protected $_name = 'ln_price';
  
  /**
   *
   */
  protected $_primary = 'id_product';        

  /**
   *
   */
 	public function add($data)
 	{
 		/*
 		if ($this->insert($data)) {
 			return $this->_db->lastInsertId();
 		}
 		
 		
 		return false;
 		*/
 		
 		return $this->insert($data);
 	}
       
} 