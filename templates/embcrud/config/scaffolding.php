<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	//default command options values --options
	'command_options' => array(
		//default project template
		'project' => 'embcrud'
		//directory for outputting generated stuff.
		,'output' => 'generated'
		,'module' => 'emb-cli-scaffolding'
		,'ignore' => array('scaffolding.php','config.php')
	)
	//required command arguments => resource=user
	,'required' => array('resource')
	//default template arguments, we can pass as many as we need in our templates.
	,'defaults' => array(
		"theme" => "default"
		,"front_controller" => "Controller_Frontend"
		,"front_actions" => array("index","list")
		,"admin_controller" => "Controller_Admin"
		,"admin_actions" => array("create","read","update","delete")
	),
);