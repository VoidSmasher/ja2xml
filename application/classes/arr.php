<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: arr
 * User: legion
 * Date: 08.12.15
 * Time: 18:06
 */
class Arr extends Kohana_Arr {

	/**
	 * Merges one or more arrays recursively and preserves all keys.
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = Arr::merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
	 *
	 * @param   array $a1 initial array
	 * @param   array $a2,... array to merge
	 *
	 * @return  array
	 */
	public static function merge(array $a1, array $a2) {
		$result = array();
		for ($i = 0, $total = func_num_args(); $i < $total; $i++) {
			// Get the next array
			$arr = func_get_arg($i);

			// Is the array associative?
			$assoc = Arr::is_assoc($arr);

			foreach ($arr as $key => $val) {
				if (isset($result[$key])) {
					if (is_array($val) AND is_array($result[$key])) {
						// Arrays are merged recursively
						$result[$key] = Arr::merge($result[$key], $val);
					} else {
						if ($assoc) {
							// Associative values are replaced
							$result[$key] = $val;
						} elseif (!in_array($val, $result, TRUE)) {
							// Indexed values are added only if they do not yet exist
							$result[] = $val;
						}
					}
				} else {
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}

	public static function is_array_filled(&$a) {
		return ($a && is_array($a) && count($a)) ? count($a) : 0;
	}

} // End arr
