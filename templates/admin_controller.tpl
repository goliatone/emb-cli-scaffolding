<?php defined('SYSPATH') or die('No direct script access.'); 
/**
 * Class <code>Controller_{{resource}}</code>.
 */
class Controller_{{resource}} extends {{admin_controller}} {
	{{#has_template}}
	/**
	 * @var  View  page template
	 */
	public $template = "{{template}}";
	{{/has_template}}
	{{#has_layout}}
	/**
	 * @var string $layout ID of the current layout.
	 */
	public $layout = "{{layout}}";
	{{/has_layout}}
	{{#has_theme}}
	/**
	 * @var	string $theme ID of the current theme.
	 */
	public $theme  = "{{theme}}";
	{{/has_theme}}
	{{#admin_actions}}
	public function action_{{.}}()
	{
		//crud actions here, please.
	}
	{{/admin_actions}}
}