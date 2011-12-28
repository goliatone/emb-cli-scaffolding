<?php defined('SYSPATH') or die('No direct script access.');
/**
 * description...
 *
 * *
 */
class Model_comment extends ORM {

    /**
     * Name of the database to use
     *
     * @access	protected
     * @var		string	$_db default [default]
     */
    protected $_db = 'default';

    /**
     * Table name to use
     *
     * @access	protected
     * @var		string	$_table_name default [singular model name]
     */
    protected $_table_name = comment;

    /**
     * Column to use as primary key
     *
     * @access	protected
     * @var		string	$_primary_key default [id]
     */
    protected $_primary_key = 'id';

    protected $_filters = array(TRUE => array('trim' => NULL));

    protected $_rules = array(
        'id'		=> array(Array('not_empty'), Array('range' => array(':value',-2147483648, 2147483647)), ),
    );

    protected $_labels = array(
        'id'	=> 'Id',
    );
	
	//protected $_belongs_to = array('user' => array());
	
	protected $_has_many = array(
	    'categories' => array(
	        'model'   => 'category',
	        'through' => 'categories_messages',
	    ),
	);
	
    public function lists(array $params, & $pagination = NULL, $calc_total = TRUE)
    {
        $pagination instanceOf Pagination OR $pagination = new Pagination;

        // Customize where from params
        //$this->where('', '', );

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->count_all();

            if($pagination->total_items === 0)
                return array();
        }

        // Customize order by from params
        if(isset($params['orderby']))
            $this->order_by(key($params['orderby']), current($params['orderby']));

        return $this->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();
    }

} // END Model_Message
