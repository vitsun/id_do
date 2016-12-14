<?php
/**
 *
 */
class ProductTag_Db extends Zend_Db_Table_Abstract
{
  /**
   *
   */
  protected $_name = 'ln_product_tag';
  
  /**
   *
   */
  protected $_primary = array ('p_id','t_id');
	
  /**
   *
   */
 	public function get_top_popular($cat_id)
 	{
 		//$date_begin=1;
 		$db = $this->getDefaultAdapter();
 		
 		$columns = array (
 			'pt.t_id',
 			't.t_name',
 			'p_count' => 'count(p.id)'
 		);
 		
 		$select = $db->select();
 		
 		$select->from (array('pt' => $this->_name),array())
 			->columns($columns)
 			->join(array('p' => 'ln_product'),'p.id = pt.p_id',array())
 			->join(array('t' => 'ln_tag'),'t.t_id = pt.t_id',array());
 		$select->where('p.categoryId='.$cat_id);
 		$select->group(array('pt.t_id','t.t_name'));
 		$select->order('count(p.id) DESC,pt.t_id asc');
 		$select->limit(20);
 		//print_r($select->__toString());
 		//exit;
 		
 		return $db->fetchAll($select);
 	}
       
}
?>