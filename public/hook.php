<?php
class categoryviewsHooks{
	
	public function __construct() {
		register_activation_hook( CV_PATH.CV_PLUGIN_FILE, array($this,'CV_default_option_value' ));
		register_uninstall_hook ( CV_PATH.CV_PLUGIN_FILE, array(__CLASS__,'CV_delete_option_value'));
		add_filter('pre_set_site_transient_update_plugins',  array($this,'CV_update_option_value'));
	}

	/**
	 *  This function is called when the plugin is activated.
	 *
	 *	@since    			1.0.0
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 */
	public function CV_default_option_value() {
		$default_values=array(
				'version'=>CV_VERSION,
		);
		add_option('CV_settings',$default_values);
		update_option('cvbw_activation_date', time());
	}

	/**
	 *  This function is called when the plugin is uninstalled.
	 *
	 *	@since    			1.0.0
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 */
	public static function CV_delete_option_value() {
		delete_option('CV_settings');
	}

	/* Check update hook Start */
	function CV_update_option_value($transient)
	{
		if (empty($transient->checked)) {
			return $transient;
		}
		update_option('cvbw_activation_date', time());
		return $transient;
	}   
	/* Check update hook End */
}
new categoryviewsHooks();
?>