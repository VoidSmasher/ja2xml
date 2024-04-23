<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Database_Query_Builder_Join
 * User: legion
 * Date: 08.12.15
 * Time: 17:53
 */
class Database_Query_Builder_Join extends Kohana_Database_Query_Builder_Join {

	public function get_table() {
		return $this->_table;
	}

} // End Database_Query_Builder_Join
