<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class Scout
{
	/**
	* All engine converters commands
	*/
	private static $_converters = array(
			'loop',
			'endloop',
			'include',
			'echos',
			'comments',
			'conditional_openings',
			'conditional_closings',
			'else'
		);

	/**
	* Parse the view and return a converted view ready for output
	*
	* @param string
	* @return string
	*/
	public static function parse($path){

		$value = static::convert_all(file_get_contents($path));

		return $value;

	}

	/**
	* Used to launch all engine converters. Return a converted content
	* with valid data.
	*
	* @param string
	* @return string
	*/
	private static function convert_all($value){

		foreach (static::$_converters as $converter) {

			$signature = "convert_{$converter}";
			$value = static::$signature($value);

		}

		return $value;

	}

	/**
	 * Allow the user to write PHP comments in HTML context
	 *
	 * @param  string
	 * @return string
	 */
	private static function convert_comments($value)
	{
		return preg_replace("/(\{\*)(.+)?(\n)?(\*\})/s", "<?php /* $2 */ ?>", $value);
	}

	/**
	 * Used to convert {{ $value }} statements into PHP echo $value
	 *
	 * @param  string
	 * @return string
	 */
	private static function convert_echos($value)
	{
		return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
	}

	/**
	* Used to convert "@loop(some params)" into a valid WP_Query
	* object. Start a Wordpress loop using the given params.
	* CAREFUL - KEEP THE FIRST WHITESPACE in the replace part
	*
	* @param string
	* @return string
	*/
	private static function convert_loop($value){

		$value = preg_replace("/(\s*)@loop(\s*)?\((.+)\)/", ' <?php $themosisQuery = new WP_Query('."$3".'); if($themosisQuery->have_posts()){ while($themosisQuery->have_posts()){ $themosisQuery->the_post(); ?> ', $value);

		return $value;

	}

	/**
	* Used to convert "@endloop" into a valid closing Wordpress loop.
	* Reset the loop query
	*
	* @param string
	* @return string
	*/
	private static function convert_endloop($value){

		$value = preg_replace("/(\s*)@endloop(\s*)?/", " <?php } } wp_reset_query(); ?> ", $value);

		return $value;

	}

	/**
	* Used to include other template file.
	* Keep variables scope of included files.
	*
	* @param string
	* @return @string
	*/
	private static function convert_include($value){

		preg_match_all("/(\s*)@include(\s*)?\((\'|\")(.+)(\'|\")\)/", $value, $matches);

		// Parse the group values. Check the path value.
		// If it's a subdirectory, convert '.' into "/"
		foreach ($matches[0] as $include) {

			preg_match('/\((?:\'|\")(.+)(?:\'|\")\)/', $include, $variable);

			$filePath = themosis_convert_path($variable[1]);	

			$search = '/(\s*)@include(\s*)?\((\'|\")(.+)(\'|\")\)/';

			$replace = "<?php include('".$filePath.EXT."'); ?>";

			$scout = preg_replace($search, $replace, $include);

			$value = str_replace($include, $scout, $value);

		}

		return $value;

	}

	/**
	 * Used for conditional statements.
	 *
	 * @param  string 
	 * @return string
	 */
	private static function convert_conditional_openings($value)
	{
		$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
	}

	/**
	 * Used to close conditional statements.
	 *
	 * @param  string
	 * @return string
	 */
	private static function convert_conditional_closings($value)
	{
		$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';

		return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
	}

	/**
	 * Used for the "else" conditional statement
	 *
	 * @param  string
	 * @return string
	 */
	private static function convert_else($value)
	{
		return preg_replace('/(\s*)@(else)(\s*)/', '$1<?php $2: ?>$3', $value);
	}
}