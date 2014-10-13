<?php
/*
function Index()
{*/

	//global $user_data, $this->model, $ip, $lang, $this->config_data, $base_path, $base_url, $cookie_path, $this->arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $this->config_data, $this->text_url;

class IndexSwitchClass extends ControllerSwitchClass 
{

	public function index()
	{

		ob_start();
		
		settype($_GET['IdPage'], 'integer');
		
		$sql_text='where IdPage='.$_GET['IdPage'];

		if($_GET['IdPage']==0 && $this->text_url=='')
		{
			settype($this->config_data['index_page'], 'integer');
			$_GET['IdPage']=$this->config_data['index_page'];
			$sql_text='where IdPage='.$_GET['IdPage'];
			
		}
		else
		if($this->text_url!='' && $_GET['IdPage']==0)
		{
		
			$sql_text='where `name_'.$_SESSION['language'].'`="'.$this->text_url.'"';
		
		}
		
		$cont_index_page='';

		$this->arr_block='';

		$this->arr_block=select_view(array('pages', 'page_'.$_GET['IdPage']));

		$header_js_pages='';

		//Load page...

		load_model('pages');
		
		$query=$this->model['page']->select($sql_text, array('name', 'text'));

		list($name_page, $text)=webtsys_fetch_row($query);
		
		$name_page=$this->model['page']->components['name']->show_formatted($name_page);
		$text=$this->model['page']->components['text']->show_formatted($text);

		if($text!='')
		{
			
			echo load_view(array($name_page, $text), 'content');
		}

		$cont_index_page.=ob_get_contents();

		ob_end_clean();
		
		ob_start();
		
		$arr_arr_options=array();
		$arr_property_path=array();
		$arr_property=array();
	
		$query_prop=$this->model['property_page']->select('where idpage='.$_GET['IdPage'].' order by order_page ASC', array('IdProperty_page', 'property', 'options'));

		while(list($idprop, $property, $ser_options)=webtsys_fetch_row($query_prop))
		{
			$arr_arr_options[$idprop]=unserialize($ser_options);
			
			$arr_property_check=explode('|', $property);
			
			$arr_property_path[$idprop]=$arr_property_check[0];
			
			$arr_property[$idprop]=basename($arr_property_check[1]);
			

		}
		
		foreach($arr_arr_options as $idprop => $arr_options)
		{
		
			$property_path=$arr_property_path[$idprop];
			$property=$arr_property[$idprop];
		
			include_once($base_path.'modules/'.$property_path.'/property/php/'.$property);
			
			$func_property=str_replace('.php', '', $property);
			
			if(function_exists($func_property))
			{
				echo $func_property($arr_options);
			}
			
		}

		$cont_index_page.=ob_get_contents();

		ob_end_clean();

		echo load_view(array($name_page, $cont_index_page, $this->block_title, $this->block_content, $this->block_urls, $this->block_type, $this->block_id, $this->config_data, $header_js_pages), $this->arr_block);
	
	}
}

?>
