<?php
/**
 *
 */
class Category_Db extends Zend_Db_Table_Abstract
{
    /**
     *
     */
    protected $_name = 'ln_category';
    
    /**
     *
     */
    protected $_primary = 'id';        
       
    /**
     *
     */
    public function get_sort_categories11($id=3507)
    {
    	if ($id > 0) {
    		$children = $this->get_sort_categories_children($id,2);
    		$parents = $this->get_sort_categories_parents($id,$children);
    	}
    	return($parents);
    }
    
    /**
     *
     */
    public function get_sort_categories_parents11($parentId,$children=array())
    {
    	$parent = parent::fetchRow('id='.$parentId)->toArray();
    	$parent['children1'] = $children;
    	
    	if ($parent['parentId'] > 0) {    		
    		$parent = $this->get_sort_categories_parents($parent['parentId'],$parent);
    	}
    	
    	return ($parent);
    }
    
    /**
     *
     */
    public function get_sort_categories_children11($parentId,$levels)
    {
    	if ($levels > 0) {
	    	$children = parent::fetchAll('parentId='.$parentId)->toArray();
	    	
	    	foreach ($children as $key => $child) {
	    		//if ($child['parentId'] > 0) {
	    			$children[$key]['children1'] = $this->get_sort_categories_children($child['id'],$levels-1);
	    		//}
	    	}
	    	
	    	return ($children);
	    } else {
	    	return array();
	    }
    }





    /**
     *
     */
    public function get_sort_categories($id)
    {
    	if ($id > 0) {
    		// ---------- first version ----------------------------
    		$children = $this->get_sort_categories_children($id,2,1);
    		$parents = $this->get_sort_categories_parents($id,$children,0);
    		
    		// --------- second version ----------------------------
    		$main_parents = $this->get_sort_categories_children(0,1,$parents[0]['level']);
    		foreach ($main_parents as $key => $val) {
    			if ($val['id'] == $parents[0]['id']) {
    				array_splice($main_parents,$key,1,$parents);
    				break;
    			}
    		}
    		
    		$parents = $main_parents;
    		// ----------------------------------------------------
	    } else {
    		$parents = $this->get_sort_categories_children($id,2,1);
    	}
    	
    	return($parents);
    }
    
    /**
     *
     */
    public function get_sort_categories_parents($parentId,$children=array(),$cur_level=0)
    {
    	$res_ar = array();
    	$parent = parent::fetchRow('id='.$parentId)->toArray();
    	    	
    	$parent['level'] = $cur_level;
    	
    	$Product = new Product();
    	$c = $Product->getProductCount($parent['children']);

    	$parent['c_count'] = $c;
    	$res_ar = array_merge(array($parent),$children);
    	
    	if ($parent['parentId'] > 0) {    		
    		$res_ar = $this->get_sort_categories_parents($parent['parentId'],$res_ar,$cur_level-1);
    	}
    	
    	return ($res_ar);
    }
    
    /**
     *
     */
    public function get_sort_categories_children($parentId,$levels,$cur_level=0)
    {
    	$res_ar = array();
	    $Product = new Product();
    	
    	if ($levels > 0) {
    		//$sel = $this->select()->from(array('a' => $this->_name,)->where('parentId='.$parentId);
    		
	    	$children = parent::fetchAll('parentId='.$parentId)->toArray();
	    	//$children = parent::fetchAll($sel)->toArray();
	    	//$children = $this->getAll(array('where' => 'a.parentId='.$parentId));
	    	//print_r($children);exit;
	    	
	    	foreach ($children as $key => $child) {
	    		$child['level'] = $cur_level;
	    		
	    		$ar_children = $this->get_sort_categories_children($child['id'],$levels-1,$cur_level+1);
	    		
	    		if (count($ar_children) <= 0) {
	    			$product_count = $Product->getProductCount($child['children']);
	    			$child['c_count'] = $product_count;
	    		} else {
	    			$c = 0;
	    			foreach ($ar_children as $one_child) {
	    				$c += $one_child['c_count'];
	    			}
	    			$child['c_count'] = $c;
	    		}
	    		
	    		if ($child['c_count'] > 0) {
	    			$res_ar[] = $child;
	    		}
	    		$res_ar = array_merge($res_ar,$ar_children);
	    	}
	    }

	    return ($res_ar);
    }

} 