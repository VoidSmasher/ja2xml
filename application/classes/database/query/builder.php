<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Database_Query_Builder
 * User: legion
 * Date: 08.12.15
 * Time: 17:50
 */
abstract class Database_Query_Builder extends Kohana_Database_Query_Builder {

	/**
	 * Compiles an array of JOIN statements into an SQL partial.
	 *
	 * @param   object $db Database instance
	 * @param   array  $joins join statements
	 *
	 * @return  string
	 */
	protected function _compile_join(Database $db, array $joins) {
		$statements = array();

		foreach ($joins as $join) {
			// Compile each of the join statements
			if (is_array($join->get_table()) && array_key_exists(0, $join->get_table())) {
				$table = $join->get_table();
				$statements[implode(',', $table)] = $join->compile($db);
			} else {
				$statements[] = $join->compile($db);
			}
		}

		return implode(' ', $statements);
	}

} // End Database_Query_Builder
