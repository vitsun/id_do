<?php
/**
 *
 */
class Product_Db extends Zend_Db_Table_Abstract
{
    /**
     *
     */
    protected $_name = 'ln_product';

    /**
     *
     */
    protected $_primary = 'id';

    /**
     *
     */
    public function getProductCount($categoryId)
    {
    	$select = $this->select()->from(array($this->_name),array('c_count' => 'count(id)'))->where('categoryId in ('.$categoryId.')');

    	$res = $this->fetchAll($select)->toArray();

    	return ($res[0]['c_count']);
    }

    /**
     *
     */
    public function getAll($options,&$rPaginator)
    {
    	$select = $this->select()->from($this->_name);

    	if (isset($options['where'])) {
    		$select->where($options['where']);
    	}

    	if (isset($options['order']) && ($options['order'] != 'relevance') ) {
    		$select->order($options['order'].' '.$options['orderby']);
    	} else if (isset($options['match'])) {
	    	$select->order($options['match'].' desc');
    	}

    	if (isset($options['t'])) {
    		$in = implode(',',array_keys($options['t']));
    		$select->where('id in (select p_id from ln_product_tag where t_id in ('.$in.'))');
    	}

    		$select->limit(300);

			$rPaginator = Zend_Paginator::factory($select);
			$rPaginator->setCurrentPageNumber($options['page']);
			$rPaginator->setItemCountPerPage($options['perPage']);

    	return $this->fetchAll($select)->toArray();
    }

    /**
     *
     */
    public function getAnalog($id,$count)
    {
    	$db = $this->getDefaultAdapter();
    	$s = "SELECT * FROM ln_product p join (";
    	$s .= "select t1.p_id,count(t1.t_id) as c_tags from ln_product_tag t1 join ln_product_tag t2 on t1.t_id=t2.t_id and t2.p_id={$id} and t1.p_id<>{$id} group by t1.p_id";
    	$s .= ") tags on p.id=tags.p_id ";
    	$s .= "order by tags.c_tags DESC ";
    	$s .= "limit {$count}";

		$stmt = $db->prepare($s);
		//$stmt->bindValue('placeholder', '2006-01-01');
		$stmt->execute();

		return ($stmt->fetchAll());
	}

	public function get_all_by_fetch_each()
	{
		return $this->getDefaultAdapter()->query('select * from ln_product order by id');
	}

}