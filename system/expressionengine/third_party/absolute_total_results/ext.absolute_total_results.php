<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Absolute_total_results_ext
{
	public $settings = array();
	public $name = 'Absolute Total Results';
	public $version = '1.0.2';
	public $description = 'Adds an {absolute_total_results} tag to channel:entries, for use with pagination.';
	public $settings_exist = 'n';
	public $docs_url = 'http://barrettnewton.com';
	
	/**
	 * __construct
	 * 
	 * @access	public
	 * @param	mixed $settings = ''
	 * @return	void
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		
		$this->settings = $settings;
	}
	
	/**
	 * activate_extension
	 * 
	 * @access	public
	 * @return	void
	 */
	public function activate_extension()
	{
		$hook_defaults = array(
			'class' => __CLASS__,
			'settings' => '',
			'version' => $this->version,
			'enabled' => 'y',
			'priority' => 10
		);
		
		$hooks[] = array(
			'method' => 'channel_entries_tagdata_end',
			'hook' => 'channel_entries_tagdata_end'
		);
		
		$hooks[] = array(
			'method' => 'channel_entries_query_result',
			'hook' => 'channel_entries_query_result'
		);
		
		foreach ($hooks as $hook)
		{
			$this->EE->db->insert('extensions', array_merge($hook_defaults, $hook));
		}
	}
	
	/**
	 * update_extension
	 * 
	 * @access	public
	 * @param	mixed $current = ''
	 * @return	void
	 */
	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		$this->EE->db->update('extensions', array('version' => $this->version), array('class' => __CLASS__));
	}
	
	/**
	 * disable_extension
	 * 
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->EE->db->delete('extensions', array('class' => __CLASS__));
	}
	
	/**
	 * settings
	 * 
	 * @access	public
	 * @return	void
	 */
	public function settings()
	{
		$settings = array();
		
		return $settings;
	}
        
	/**
	 * settings
	 * 
	 * @access	public
	 * @param	string $tagdata
	 * @param	array $row
	 * @param	Channel $channel
	 * @return	string
	 */
        public function channel_entries_tagdata_end($tagdata, $row, $channel)
        {
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$tagdata = $this->EE->extensions->last_call;
		}
		
		if (version_compare(APP_VER, '2.4', '<'))
		{
			$tagdata = $this->EE->TMPL->swap_var_single('absolute_total_results', $channel->total_rows, $tagdata);
		}
		else
		{
			$tagdata = $this->EE->TMPL->swap_var_single('absolute_total_results', $channel->pagination->total_rows, $tagdata);
		}
		
		return $tagdata;
        }
        
    
    
	/**
	 * channel_entries_query_result
	 * 
	 * @access	public
	 * @param	channel $channel
	 * @param	array $query_result
	 * @return	array
	 */
	public function channel_entries_query_result($channel, $query_result)
	{
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$query_result = $this->EE->extensions->last_call;
		}
		
		if (version_compare(APP_VER, '2.4', '<'))
		{
			$channel->paginate_data = $this->EE->TMPL->swap_var_single('absolute_total_results', $channel->total_rows, $channel->paginate_data);
			$channel->paginate_data = $this->EE->TMPL->swap_var_single('absolute_results', $channel->total_rows, $channel->paginate_data);
		}
		else
		{
			$channel->pagination->template_data = $this->EE->TMPL->swap_var_single('absolute_total_results', $channel->pagination->total_rows, $channel->pagination->template_data);
			$channel->pagination->template_data = $this->EE->TMPL->swap_var_single('absolute_results', $channel->pagination->total_rows, $channel->pagination->template_data);
		}
		
		return $query_result;
	}
}

/* End of file ext.absolute_total_results.php */
/* Location: ./system/expressionengine/third_party/absolute_total_results/ext.absolute_total_results.php */