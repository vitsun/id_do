<?php
/**
 *
 */
class Clicks_Db extends Zend_Db_Table_Abstract
{
  /**
   *
   */
  protected $_name = 'ln_clicks';
  
  /**
   *
   */
  protected $_primary = 'clicks_id';

  /**
   *
   */
 	public function insert11($data)
 	{ 		
 		return $this->insert($data);
 	}
 	
  /**
   *
   */
 	public function get_clicks($date_begin,$date_end)
 	{
 		//$date_begin=1;
 		$db = $this->getDefaultAdapter();
 		
 		$columns = array (
 			'a.*',
 			'toy_name' => 'b.name',
 			'toy_picture' => 'b.picture',
 			'toy_price' => 'b.price'
 		);
 		
 		$select = $db->select();
 		
 		$select->from (array('a' => $this->_name),array())
 			->columns($columns)
 			->join(array('b' => 'ln_product'),'b.id = a.clicks_toy_id',array());
 		$select->where('a.clicks_datetime between '.$date_begin.' and '.$date_end);
 		$select->order('a.clicks_datetime DESC');
 		//print_r($select->__toString());
 		//exit;
 		
 		return $db->fetchAll($select);
 	}
       
}
?>