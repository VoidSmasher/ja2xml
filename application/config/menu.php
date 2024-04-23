<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 18.05.12
 * Time: 20:07
 */
$menu = array(
	'public' => array(),
	'admin' => array(
//		'articles' => array(
//			'icon' => 'fa-book',
//			'menu' => array(
//				'articles' => array(
//					'icon' => 'fa-book',
//					'link' => '/admin/articles',
//				),
//				'tags' => array(
//					'icon' => 'fa-tags',
//					'link' => '/admin/tags',
//					'i18n' => __('common.tags'),
//				),
//				'pages' => array(
//					'icon' => 'fa-file',
//					'link' => '/admin/pages',
//				),
//			),
//		),
		'settings' => array(
			'icon' => 'fa-wrench',
			'menu' => array(
				'users' => array(
					'icon' => 'fa-user',
					'link' => '/admin/users',
				),
				':divider',
				'settings' => array(
					'icon' => 'fa-wrench',
					'link' => '/admin/settings',
				),
			),
		),
		'items' => array(
			'menu' => array(
				'data_weapons' => array(
					'i18n' => 'Weapons Data',
					'icon' => 'fa-list',
					'link' => '/admin/data_weapons',
				),
				':divider',
				'weapons' => array(
					'i18n' => 'Weapons',
					'icon' => 'fa-list',
					'link' => '/admin/weapons',
				),
				'items' => array(
					'i18n' => 'Items',
					'icon' => 'fa-list',
					'link' => '/admin/items',
				),
				':divider',
				'lbe' => array(
					'i18n' => 'Load Bearing Equipment',
					'icon' => 'fa-list',
					'link' => '/admin/lbe',
				),
				':divider',
				'gun_choices' => array(
					'i18n' => 'Gun Choices',
					'icon' => 'fa-list',
					'link' => '/admin/choices_gun',
				),
				'item_choices' => array(
					'i18n' => 'Item Choices',
					'icon' => 'fa-list',
					'link' => '/admin/choices_item',
				),
				':divider',
				'merges' => array(
					'i18n' => 'Merges',
					'icon' => 'fa-list',
					'link' => '/admin/merges',
				),
				'item_transformations' => array(
					'i18n' => 'Item Transformations',
					'icon' => 'fa-list',
					'link' => '/admin/item_transformations',
				),
				'attachment_combo_merges' => array(
					'i18n' => 'Attachment Combo Merges',
					'icon' => 'fa-list',
					'link' => '/admin/attachment_combo_merges',
				),
			),
		),
		'calibres' => array(
			'menu' => array(
				'calibres' => array(
					'i18n' => 'Calibres',
					'icon' => 'fa-list',
					'link' => '/admin/calibres',
				),
				'bullets' => array(
					'i18n' => 'Bullets',
					'icon' => 'fa-list',
					'link' => '/admin/bullets',
				),
			),
		),
		'attachments' => array(
			'menu' => array(
				'data_attachments' => array(
					'i18n' => 'Attachments Data',
					'icon' => 'fa-list',
					'link' => '/admin/data_attachments',
				),
				':divider',
				'attachments_weapons' => array(
					'i18n' => 'Attachments for Weapons',
					'icon' => 'fa-list',
					'link' => '/admin/attachments_weapons',
				),
				'attachments_attachments' => array(
					'i18n' => 'Attachments for Attachments',
					'icon' => 'fa-list',
					'link' => '/admin/attachments_attachments',
				),
				':divider',
				'incompatible' => array(
					'i18n' => 'Incompatible Attachments',
					'icon' => 'fa-list',
					'link' => '/admin/incompatible',
				),
				':divider',
				'slots' => array(
					'i18n' => 'Attachment Slots',
					'icon' => 'fa-list',
					'link' => '/admin/slots',
				),
				':divider',
				'class_attachments' => array(
					'i18n' => 'Attachment Classes',
					'icon' => 'fa-list',
					'link' => '/admin/class_attachments',
				),
				'class_nasattachments' => array(
					'i18n' => 'nasAttachment Classes',
					'icon' => 'fa-list',
					'link' => '/admin/class_nasattachments',
				),
				':divider',
				'class_naslayouts' => array(
					'i18n' => 'nasLayout Classes',
					'icon' => 'fa-list',
					'link' => '/admin/class_naslayouts',
				),
			),
		),
		'charts' => array(
			'icon' => 'fa-area-chart',
			'menu' => array(
				'accuracy' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_accuracy',
				),
				'burst' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_burst',
				),
				'calibres' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_calibres',
				),
				'speed' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_speed',
				),
				'damage' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_damage',
				),
				'deadliness' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_deadliness',
				),
				'range' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_range',
				),
				'ready' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_ready',
				),
				'recoil' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_recoil',
				),
				'sp4t' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_sp4t',
				),
				'messy' => array(
					'icon' => 'fa-area-chart',
					'link' => '/admin/charts_messy',
				),
			),
		),
		'Help' => array(
			'i18n' => 'Help',
			'icon' => 'fa-question',
			'link' => '/admin/help',
		),
	),
	'user' => array(),
	'developer' => array(
		'settings' => array(
			'icon' => 'fa-cogs',
			'menu' => array(
				'applications' => array(
					'icon' => 'fa-cubes',
					'link' => '/developer/applications',
				),
				'roles' => array(
					'icon' => 'fa-list',
					'link' => '/developer/roles',
				),
				':divider',
				'settings' => array(
					'icon' => 'fa-wrench',
					'link' => '/developer/settings',
				),
			),
		),
		'translations' => array(
			'icon' => 'fa-list',
			'link' => '/developer/translations',
		),
		'documentation' => array(
			'icon' => 'fa-question-circle',
			'menu' => array(
				'form' => array(
					'icon' => 'fa-th',
					'link' => '/developer/documentation_form',
				),
				'filter' => array(
					'icon' => 'fa-th-list',
					'link' => '/developer/documentation_filter',
				),
				'list' => array(
					'icon' => 'fa-th-list',
					'link' => '/developer/documentation_list',
				),
				'icons' => array(
					'icon' => 'fa-file-text-o ',
					'link' => '/developer/documentation_icons',
				),
			),
		),
		'examples' => array(
			'icon' => 'fa-th',
			'menu' => array(
				'common' => array(
					'icon' => 'fa-th',
					'link' => '/developer/example_common',
				),
				'filter' => array(
					'icon' => 'fa-filter',
					'link' => '/developer/example_filter',
				),
				'list-jelly' => array(
					'i18n' => 'Force_List: Jelly',
					'icon' => 'fa-th-list',
					'link' => '/developer/example_list',
				),
				'list-assoc-array' => array(
					'i18n' => 'Force_List: Assoc Array',
					'icon' => 'fa-th-list',
					'link' => '/developer/example_list/assoc_array',
				),
				'list-dynamic-array' => array(
					'i18n' => 'Force_List: Dynamic Array',
					'icon' => 'fa-th-list',
					'link' => '/developer/example_list/dynamic_array',
				),
				'list-csv' => array(
					'i18n' => 'Force_List: CSV',
					'icon' => 'fa-th-list',
					'link' => '/developer/example_list/csv',
				),
				'list-csv-simple' => array(
					'i18n' => 'Force_List: CSV Simple',
					'icon' => 'fa-th-list',
					'link' => '/developer/example_list/csv_simple',
				),
				'form' => array(
					'i18n' => 'Force Form',
					'icon' => 'fa-th',
					'link' => '/developer/example_form',
				),
				'menu' => array(
					'icon' => 'fa-th-list',
					'link' => '/developer/example_menu',
				),
			),
		),
	),
);

// Проверяем права пользователя
$user = Helper_Auth::get_user();

// Если есть пользователь
if (Core_User::is_model($user)) {
	// Дополнения к main_menu, если пользователь - администратор
	if ($user->is_admin()) {
		$menu['public']['admin'] = array(
			'icon' => 'fa-user',
			'link' => '/admin',
		);
		$menu['public']['test'] = array(
			'icon' => 'fa-list-alt',
			'link' => '/test',
		);
	}
	if (Helper_Auth::get_permission('allow_change_profile')) {
		$menu['user']['change_profile'] = array(
			'i18n' => __('auth.title.personal_data'),
			'link' => '/profile',
		);
	}
	if (Helper_Auth::get_permission('allow_change_password')) {
		$menu['user']['change_password'] = array(
			'i18n' => __('auth.button.change_password'),
			'link' => '/profile/password',
		);
	}
}

return $menu;