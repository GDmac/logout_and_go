<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Logout and Go
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Plugin
 * @author      GDmac
 * @link        https://github.com/GDmac
 */

$plugin_info = array(
	'pi_name'       => 'Logout and Go',
	'pi_version'    => '1.0Beta',
	'pi_author'     => 'GDmac',
	'pi_author_url' => 'https://github.com/GDmac',
	'pi_description'=> 'Logout and Go',
	'pi_usage'      => Logout_and_go::usage()
);


class Logout_and_go {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->EE =& get_instance();

		$go = $this->EE->TMPL->fetch_param('go');
		if( $go == '')
		{
			echo "No parameter set to go somewhere";
			return;
		}

		// Kill the session and cookies		
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->where('ip_address', $this->EE->input->ip_address());
		$this->EE->db->where('member_id', $this->EE->session->userdata('member_id'));
		$this->EE->db->delete('online_users');		

		$this->EE->session->destroy();

		// be nice, call logout hook for others 
		$edata = $this->EE->extensions->call('member_member_logout');
		if ($this->EE->extensions->end_script === TRUE) return;

		//redirect to website or template
		$url = parse_url( $go );

		if($url['scheme'] == '') $go = $this->EE->functions->create_url($go);

		$this->EE->functions->redirect($go);

		exit;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>
Simple #eecms plugin to logout a user and redirect somewhere.
You can redirect to another template or another website.

Parameters: go (required)

Examples:

{exp:logout_and_go go="group/template"}

{exp:logout_and_go go="group/template/extra/segments"}

{exp:logout_and_go go="http://example.com"}


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.logout_and_go.php */
/* Location: /system/expressionengine/third_party/logout_and_go/pi.logout_and_go.php */