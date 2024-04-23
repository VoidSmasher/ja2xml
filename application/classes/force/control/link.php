<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Link
 * User: legion
 * Date: 02.08.17
 * Time: 15:34
 */
trait Force_Control_Link {

	protected $_link = '';
	protected $_back_url = false;

	/*
	 * SET
	 */

	public function link($link, $back_url = false) {
		if (!empty($link)) {
			$this->_link = Helper_Uri::auto_fill($link);
		} else {
			$this->_link = '';
		}
		$this->_back_url = $back_url;

		if (method_exists($this, 'link_attribute')) {
			$this->link_attribute('href', $this->get_link());
		}
		return $this;
	}

	public function link_external() {
		if (method_exists($this, 'link_attribute')) {
			$this->link_attribute('target', '_blank');
			$this->link_attribute('rel', 'nofollow');
		} elseif (method_exists($this, 'attribute')) {
			$this->attribute('target', '_blank');
			$this->attribute('rel', 'nofollow');
		}
		return $this;
	}

	/*
	 * GET
	 */

	public function get_link() {
		return Helper_Uri::get_link($this->_link, $this->_back_url);
	}

} // End Force_Control_Link
