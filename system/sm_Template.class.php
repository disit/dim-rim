<?php

define ("SM_PATH_TPL","templates/");
define ("SM_PATH_JS","js/");
define ("SM_PATH_CSS","css/");

class sm_Template implements sm_Module
{
	static protected $instance=null;
	protected $tpl;
	
	function __construct() {
		// add the main template
		//$this->newTemplate('main', 'main.tpl.html');
		$this->tpl = new smTemplate();
		
	}
	
	function sm_Template() {
		// add the main template
		//$this->newTemplate('main', 'main.tpl.html');
		$this->tpl = new smTemplate();
		
	}

	/**
	 * Adds a css file to the list which will be added to the template when display() is called
	 *
	 * @param string $template_id
	 * @param string $filename
	 * @param string $path uses default set in config if non specified
	 */
	public function addCSS($filename, $template_id = 'main', $path = SM_PATH_CSS) {
		$this->tpl->addCSS($filename, $template_id , $path);
	}
	
	/**
	 * Adds a javascript file to the list which will be added to the template when display() is called
	 *
	 * @param string $template_id
	 * @param string $filename path to file or CSS code to be added inline
	 * @param string $path uses default set in config if non specified
	 */
	public function addJS($filename, $template_id = 'main', $path = SM_PATH_JS) {
		$this->tpl->addJS($filename, $template_id , $path);
	}
	
	function getTemplateVars($id)
	{
		$template = $this->tpl->getTemplate($id);
		if (!isset($template)) {
			// file does not exist
			trigger_error('Template not found with id: '.$id);
			return false;
		}
		$tplVars=array();
		if(preg_match_all("/\{([a-zA-Z0-9\-_]+)\}/", $template,$matches))
		{
			foreach($matches[1] as $var)
			{
				$tplVars[$var]="";
			}
		}
		return array_keys($tplVars);
	}
	
	public function __call($method,$args)
	{
		//return $this->form->$method($args[0]);
		return call_user_func_array(array($this->tpl,$method),$args);
	}
	
	static function getInstance()
	{
		if(self::$instance==null)
		{
			//$c=__CLASS__;
			self::$instance = new sm_Template();
		}
		return self::$instance ;
	}
	
	static public function install($db)
	{
		 return true;
	}
	
	static public function uninstall($db)
	{
	 	return true;
	}
	
	
}

?>