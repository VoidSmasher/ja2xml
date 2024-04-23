<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 04.08.12
 * Time: 8:50
 */
$icon = '';
$comment = '';
if (!isset($request)) $request = Request::current();
if (is_array($menu_link) && array_key_exists('link', $menu_link)) {
	$icon_classes = array();
	if (array_key_exists('icon', $menu_link)) {
		$icon_classes[] = $menu_link['icon'];
		if (isset($icon_class) && !empty($icon_class)) {
			$icon_classes[] = $icon_class;
		}
	}
	if (!empty($icon_classes)) {
		$icon = '<i class="' . implode(' ', $icon_classes) . '"></i> ';
	} else {
		$icon = '';
	}

	if (array_key_exists('comment', $menu_link)){
		$comment  = Helper_Popover::get_as_string($menu_link['comment'], 'bottom');
	}

	$menu_item = array_key_exists('name', $menu_link) ? $menu_link['name'] : $menu_item;
	$menu_link = $menu_link['link'];
}
$activity = ('/' . $request->directory() . '/' . $request->controller() == $menu_link) ? ' class="active"' : '';
?>
<li<?php echo $activity; ?>>
	<a href="<?php echo $menu_link; ?>"<?php echo $comment; ?>><?php echo $icon; ?><?php echo Force_Menu_Item::update_name($menu_item); ?></a>
</li>
