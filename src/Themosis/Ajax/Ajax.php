<?php
namespace Themosis\Ajax;

use Themosis\Configuration\Application;
use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Ajax
{
	/**
	 * JS namespace
	*/
	private static $namespace;

	/**
	 * Ajax url js property
	*/
	private static $url;

	public function __construct()
	{
		static::$namespace = (Application::get('namespace')) ? Application::get('namespace') : 'themosis';

		if (Application::get('rewrite')) {

			static::$url = (Application::get('ajaxurl')) ? home_url().DS.'ajax'.DS.Application::get('ajaxurl').EXT : '';

		} else {

			static::$url = (Application::get('ajaxurl')) ? admin_url().Application::get('ajaxurl').EXT : '';

		}

		Action::listen('wp_head', $this, 'install')->dispatch();
	}

	/**
	* Handle the Ajax response. Run the appropriate
	* action hooks used by wordpress in order to perform
	* POST ajax request securely.
	* Developers have the option to run ajax for the
	* Front-end, Back-end either users are logged in or not
	* or both.
	*
	* @param string
	* @param string - 'no', 'yes', 'both'
	* @param closure
	*/
	public static function run($action, $logged, $closure)
	{
		if (is_string($action) && is_callable($closure)) {

			// Front-end ajax for non-logged users
			// Set $logged to FALSE
			if ($logged === 'no') {
				add_action('wp_ajax_nopriv_'.$action, $closure);
			}

			// Front-end and back-end for logged users
			if ($logged === 'yes') {
				add_action('wp_ajax_'.$action, $closure);
			}

			// Front-end and back-end for both logged in or out users
			if ($logged === 'both') {
				add_action('wp_ajax_nopriv_'.$action, $closure);
				add_action('wp_ajax_'.$action, $closure);
			}

		} else {
			throw new AjaxException("Invalid parameters for the Ajax::run method.");
		}
	}

	/**
	 * Set the global ajax variable
	*/
	public static function set()
	{
		return new static();
	}

	/**
	 * Install the Ajax global variable
	*/
	public static function install()
	{	
		$datas = apply_filters('themosisGlobalObject', array());

		?>
		<script type='text/javascript'>
  
  			//<![CDATA[
			var <?php echo(static::$namespace); ?> = {
				ajaxurl: '<?php echo(static::$url); ?>',
				<?php
					if (!empty($datas)) {
						foreach ($datas as $key => $value) {
							echo $key.": ".json_encode($value).",";
						}
					}
				?>
			};
			//]]>

		</script>
		<?php
	}
}

?>