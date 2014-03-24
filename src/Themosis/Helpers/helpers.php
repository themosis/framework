<?php defined('DS') or die('No direct script access.');

/**
* Helpers functions globally available
*/

/**
* Define if the current page is a child page
*
* @param array
* @return boolean
*/
function themosis_is_subpage($parent)
{
	global $post;

	$parentPage = get_page($post->post_parent);

    if ( is_page() && $post->post_parent && $parentPage->post_name === $parent[0]) {
        return $post->post_parent;
    }

    return false;
}

/**
 * Tell wordpress we're in maintenance mode.
 * Only the user with 'administrator' role can
 * surf the site.
 *
 * @return boolean
*/
function themosisIsInMaintenanceMode()
{
	list($maintenance) = Option::get('themosis-maintenance', 'activate');

	if ($maintenance === 'yes') {

		list($user) = (User::get()->roles) ? User::get()->roles : array('');

		if ($user !== 'administrator') {

			$time = (Option::get('themosis-maintenance', 'duration')) ? Option::get('themosis-maintenance', 'duration') : 3600;

			// Set the header response
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: '.$time);

			return true;
		}
	}

	return false;
}

/**
* Convert '.' into '/' directory separators
*
* @param string
* @return string
*
*/
function themosis_convert_path($path){

	if(strpos($path, '.') !== false){

		$path = str_replace('.', DS, $path);

	} else {

		$path = trim($path);

	}

	return (string)$path;

}

/**
* Print and die a value - Used for debugging
*
* @param mixed
*
*/
function td($value){

	echo '<pre>';
	print_r($value);
	echo '</pre>';
	die();

}

/**
* Print a value
*
* @param mixed
*
*/
function tp($value){
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

/**
 * Return the application assets
 * directory path.
 *
 * @return string
*/
function themosisAssets()
{
	if (Themosis\Configuration\Application::get('rewrite')) {

		if (substr(site_url(), -1) === DS) {

			return site_url().'assets';

		}

		return site_url().DS.'assets';

	}

	return get_template_directory_uri().DS.'app'.DS.'assets';
}

/**
 * Return the application views
 * directory path.
 *
 * @return string
*/
function themosisViews()
{
	if (defined('THEMOSIS_VIEWS')) {
		return THEMOSIS_VIEWS;
	}

	return false;
}

/**
* Return the WP Query variable
*
* @return object
*/
function themosisGetTheQuery()
{
	global $wp_query;

	return $wp_query;
}

/**
 * Conditional function that checks if WP
 * is using a pretty permalink structure.
 *
 * @return boolean
*/
function themosisUsePermalink()
{
	global $wp_rewrite;

	if(!$wp_rewrite->permalink_structure == '')
	{
		return true;
	}

	return false;
}

/**
 * Helper that runs multiple add_filter
 * functions at once.
 *
 * @param array
 * @param string
*/
function themosisAddFilters($tags, $function) {
  foreach($tags as $tag) {
    add_filter($tag, $function);
  }
}

/**
 * A function that checks you're on a specified
 * admin page, post, or custom post type (edit) in order to display
 * a certain content.
 *
 * Example : Place a specific metabox for a page, a post or a one of your
 * custom post type.
 *
 * Give the post ID. Visible in the admin uri in your browser.
 *
 * @param int
 * @return boolean
*/
function themosisIsPost($id)
{
    $postId = null;

    // Get post ID WHEN EDITING THE PAGE
    if (isset($_GET['post'])) {
        $postId = $_GET['post'];
    }

    // WHEN SAVING THE PAGE
    if (isset($_POST['post_ID'])) {
        $postId = $_POST['post_ID'];
    }

    if (!is_null($postId) && is_numeric($id) && $id === (int) $postId) {
        return true;
    }

    return false;
}

/**
 * A function that returns the 'attachment_id' of a
 * media file by giving its URL.
 *
 * @param string The media/image URL - Works only for images uploaded from within wordpress
 * @return int|boolean The image/attachment_id if it exists, false if not.
*/
function themosisAttachmentIdFromUrl($url = null)
{
    /*-----------------------------------------------------------------------*/
    // Load the DB class
    /*-----------------------------------------------------------------------*/
    global $wpdb;

    /*-----------------------------------------------------------------------*/
    // Set attachment_id
    /*-----------------------------------------------------------------------*/
	$id = false;

	/*-----------------------------------------------------------------------*/
	// If there is no url, return.
	/*-----------------------------------------------------------------------*/
	if (null === $url)
		return;

	/*-----------------------------------------------------------------------*/
	// Get the upload directory paths
	/*-----------------------------------------------------------------------*/
	$upload_dir_paths = wp_upload_dir();

	/*-----------------------------------------------------------------------*/
	// Make sure the upload path base directory exists in the attachment URL,
	// to verify that we're working with a media library image
	/*-----------------------------------------------------------------------*/
	if (false !== strpos($url, $upload_dir_paths['baseurl'])) {

		/*-----------------------------------------------------------------------*/
		// If this is the URL of an auto-generated thumbnail,
		// get the URL of the original image
		/*-----------------------------------------------------------------------*/
		$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url);

		/*-----------------------------------------------------------------------*/
		// Remove the upload path base directory from the attachment URL
		/*-----------------------------------------------------------------------*/
		$url = str_replace($upload_dir_paths['baseurl'] . '/', '', $url);

		/*-----------------------------------------------------------------------*/
		// Grab the database prefix
		/*-----------------------------------------------------------------------*/
		$prefix = $wpdb->prefix;

		/*-----------------------------------------------------------------------*/
		// Finally, run a custom database query to get the attachment ID
		// from the modified attachment URL
		/*-----------------------------------------------------------------------*/
		$id = $wpdb->get_var($wpdb->prepare("SELECT {$prefix}posts.ID FROM $wpdb->posts {$prefix}posts, $wpdb->postmeta {$prefix}postmeta WHERE {$prefix}posts.ID = {$prefix}postmeta.post_id AND {$prefix}postmeta.meta_key = '_wp_attached_file' AND {$prefix}postmeta.meta_value = '%s' AND {$prefix}posts.post_type = 'attachment'", $url));

	}

	return $id;

}

/**
 * A function that checks if we are using a page template.
 *
 * @return boolean True: use of a template. False: no template.
*/
function themosisIsTemplate($name = array())
{
	$queriedObject = get_queried_object();

	if (is_a($queriedObject, 'WP_Post') && 'page' === $queriedObject->post_type) {

		// Sanitized value
		$template = Meta::get($queriedObject->ID, '_themosisPageTemplate');

		// If no template selected, just return;
		if ($template === 'none') return false;

		// Send the appropriate view
		if (isset($template) && !empty($template)) {

		    /*-----------------------------------------------------------------------*/
		    // If the page template name is defined within the routes array, handle
		    // the template
		    /*-----------------------------------------------------------------------*/
		    if (in_array($template, $name)) {

    		    return true;

		    }

		}

		return false;

	}
}
