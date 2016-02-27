<?php
class Config
{
	public $site_name = '{site_name}';
	public $role_user = [
		'0'=>'2',
		];
	public $enable_site = '0';
	public $offline_mess = '1';
	public $text_offline = '';
	public $editor_admin = 'tinymce';
	public $editor_site = '';
	public $calendar = '';
	public $captcha = '';
	public $format_date = 'd.m.Y';
	public $page_number = '10';
	public $meta_robots = 'index, follow';
	public $meta_desc = '';
	public $meta_keys = '';
	public $meta_author = '';
	public $show_author = '0';
	public $error_reporting = 'development';
	public $path_log = '@root/temp/log';
	public $debug = '1';
	public $viewstyle = '1';
	public $viewposition = '1';
	public $log_enable = '1';
	public $log_maxsize = '7';
	public $time_cache = '1200';
	public $ses_name = 'SID';
	public $ses_time = '17';
	public $ses_path = '@root/temp/session';
	public $timezone = '{timezone}';
	public $charset = 'utf-8';
	public $language = '{language}';
	public $fromname = '{fromname}';
	public $mailfrom = '{mailfrom}';
	public $prefix = 'php';
	public $enab_prefix = '1';
	public $enable_cache = '1';
	public $autolang = '1';
	public $gzip = '1';
	public $useFileTransport = '1';
	public $enableCsrfValidation = '1';
	public $logWrite = [
		'0'=>'db',
		'1'=>'error',
		'2'=>'cache',
		'3'=>'exp',
		'4'=>'request',
		];
	public $database = [
		'default'=>[
		'encoding'=>'{encoding}',
		'type'=>'{type}',
		'host'=>'{host}',
		'bdname'=>'{dbname}',
		'dbprefix'=>'{prefix}',
		'user'=>'{user}',
		'password'=>'{password}',
		'connect'=>'1',
		]];
	}
?>
