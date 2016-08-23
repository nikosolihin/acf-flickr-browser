<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_flickr_browser') ) :


class acf_field_flickr_browser extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'flickr_browser';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Flickr Browser', 'acf-flickr_browser');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'content';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array(
			'flickr_api_key'              => '',
			'flickr_user_id'              => ''
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('flickr_browser', 'error');
		*/

		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-flickr_browser'),
		);


		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/

		$this->settings = $settings;


		// do not delete!
    parent::__construct();
	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/

		acf_render_field_setting( $field, array(
			'required'  => true,
			'label'			=> __('Flickr API Key','acf-flickr_browser'),
			'instructions'	=> __('Find or register your API key at','acf-flickr_browser') . ' <a href="http://www.flickr.com/services/apps/" target="_blank">http://www.flickr.com/services/apps</a>. Alternatively, you can set the constant <strong>FLICKR_BROWSER_API_KEY</strong>.',
			'type'			=> 'text',
			'name'			=> 'flickr_api_key'
		));

		acf_render_field_setting( $field, array(
			'required'  => true,
			'label'			=> __('Flickr User ID','acf-flickr_browser'),
			'instructions'	=> __('Find your User ID at','acf-flickr_browser') . ' <a href="http://idgettr.com/" target="_blank">http://idgettr.com/</a>. Alternatively, you can set the constant <strong>FLICKR_BROWSER_USER_ID</strong>.',
			'type'			=> 'text',
			'name'			=> 'flickr_user_id',
		));

		acf_render_field_setting( $field, array(
			'label'        => __('Selection Type','acf-flickr_browser'),
			'type'         => 'radio',
			'layout'       => 'horizontal',
			'name'         => 'flickr_limit',
			'choices'      => array(
				'1' => 'Single',
				'9999' => 'Multiple',
			),
		));

		acf_render_field_setting( $field, array(
			'label'        => __('Image Sizes','acf-flickr_browser'),
			'instructions' => __('The preferred size of the photos.','acf-flickr_browser') . ' <a href="https://www.flickr.com/services/api/misc.urls.html#yui_3_11_0_1_1471928354490_315" target="_blank">Learn more.</a>',
			'type'         => 'checkbox',
			'name'         => 'flickr_sizes',
			'layout'			 => 'horizontal',
			'choices' 		 => array(
				'url_q' => 'Square 150',
				'url_t' => 'Thumbnail',
				'url_s' => 'Small 240',
				'url_n' => 'Small 320',
				'url_m' => 'Medium 500',
				'url_z' => 'Medium 640',
				'url_c' => 'Medium 800',
				'url_l' => 'Large 1024',
				'url_h' => 'Large 1600',
				'url_o' => 'Original'
			),
		));

		acf_render_field_setting( $field, array(
			'label'        => __('Extras','acf-flickr_browser'),
			'instructions' => __('Extra metadatas to fetch for each photo.','acf-flickr_browser') . ' <a href="http://librdf.org/flickcurl/api/flickcurl-searching-search-extras.html" target="_blank">Learn more.</a>',
			'type'         => 'checkbox',
			'name'         => 'flickr_extras',
			'layout'			 => 'horizontal',
			'choices' 		 => array(
				'description' => 'Description',
				'license' => 'License',
				'date_upload' => 'Upload Date',
				'date_taken' => 'Date Taken',
				'owner_name' => 'Owner Name',
				'original_format' => 'Original Format',
				'last_update' => 'Last Update',
				'o_dims' => 'Original Dimensions',
				'geo' => 'Geo',
				'tags' => 'Tags',
				'views' => 'Number of views',
				'media' => 'Media'
			),
		));
	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {
		if(defined('FLICKR_BROWSER_API_KEY')) {
			$field['flickr_api_key'] = FLICKR_BROWSER_API_KEY;
		}
		if(defined('FLICKR_BROWSER_USER_ID')) {
			$field['flickr_user_id'] = FLICKR_BROWSER_USER_ID;
		}
?>

		<script id="SidebarPartial" type="x-tmpl-mustache">
			{{#children}}
				<li class="sidebar-item" {{#id}}data-id="{{id}}"{{/id}}>
			    <div class="sidebar-link">
			      <svg class="sidebar-icon" aria-hidden="true" role="presentation" title="{{title}}">
			        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#{{icon}}"></use>
			      </svg>
			      <p>{{title}}<br><span>3 Photos</span></p>
			    </div>
		      <ul>
						{{>recursive_partial}}
					</ul>
				</li>
			{{/children}}
		</script>

		<script id="AlbumTemplate" type="x-tmpl-mustache">
			<div class="fb-album" data-album-id="{{albumId}}">
			{{#photos}}
				<div class="fb-photo" data-id="{{id}}"
				{{!--SQUARE--}}
					{{#url_sq}}data-sq="{{url_sq}}"{{/url_sq}}
					{{#url_q}}data-q="{{url_q}}"{{/url_q}}
					{{#url_t}}data-t="{{url_t}}"{{/url_t}}
				{{!--SMALL--}}
					{{#url_s}}data-s="{{url_s}}"{{/url_s}}
					{{#url_n}}data-n="{{url_n}}"{{/url_n}}
				{{!--MEDIUM--}}
					{{#url_m}}data-m="{{url_m}}"{{/url_m}}
					{{#url_z}}data-z="{{url_z}}"{{/url_z}}
					{{#url_c}}data-c="{{url_c}}"{{/url_c}}
				{{!--LARGE--}}
					{{#url_l}}data-l="{{url_l}}"{{/url_l}}
					{{#url_h}}data-h="{{url_h}}"{{/url_h}}
				{{!--ORIGINAL--}}
					{{#url_o}}data-o="{{url_o}}"{{/url_o}}
				{{!--EXTRAS--}}
					{{#description}}data-description="{{description._content}}"{{/description}}
					{{#license}}data-license="{{license}}"{{/license}}
					{{#dateupload}}data-dateupload="{{dateupload}}"{{/dateupload}}
					{{#datetaken}}data-datetaken="{{datetaken}}"{{/datetaken}}
					{{#ownername}}data-ownername="{{ownername}}"{{/ownername}}
					{{#originalformat}}data-originalformat="{{originalformat}}"{{/originalformat}}
					{{#originalwidth}}data-originalwidth="{{originalwidth}}"{{/originalwidth}}
					{{#originalheight}}data-originalheight="{{originalheight}}"{{/originalheight}}
					{{#lastupdate}}data-lastupdate="{{lastupdate}}"{{/lastupdate}}
					{{#geo}}data-geo="{{geo}}"{{/geo}}
					{{#tags}}data-tags="{{tags}}"{{/tags}}
					{{#views}}data-views="{{views}}"{{/views}}
					{{#media}}data-media="{{media}}"{{/media}}

				style="background-image:url({{url_sq}})">
					<span>{{title}}</span>
				</div>
			{{/photos}}
			</div>
		</script>

		<script id="PreviewTemplate" type="x-tmpl-mustache">
			<div class="fb-selected-item" data-photo-id="{{photoId}}">
				<img src="{{url_sq}}" />
			</div>
		</script>

		<div style="height: 0; width: 0; position: absolute; visibility: hidden">
		  <!-- inject:svg --><svg xmlns="http://www.w3.org/2000/svg"><symbol id="archive" viewBox="0 0 20 20"><path d="M13.98 2H6.02s-.996 0-.996 1h9.955c0-1-.996-1-.996-1zm2.988 3c0-1-.995-1-.995-1H4.027s-.995 0-.995 1v1h13.936V5zm1.99 1l-.588-.592V7H1.63V5.408L1.04 6S.03 6.75.268 8c.236 1.246 1.38 8.076 1.55 9 .185 1.014 1.216 1 1.216 1H16.97s1.03.014 1.216-1c.17-.924 1.312-7.754 1.55-9 .234-1.25-.188-1.408-.778-2zM14 11.997C14 12.55 13.55 13 12.997 13H7.003C6.45 13 6 12.55 6 11.997V10h1v2h6v-2h1v1.997z"/></symbol><symbol id="check" viewBox="0 0 24 32"><path d="M20 6L8 18l-4-4-4 4 8 8 16-16-4-4z"/></symbol><symbol id="folder-open" viewBox="0 0 32 32"><path d="M26 30l6-16H6L0 30zM4 12L0 30V4h9l4 4h13v4z"/></symbol><symbol id="folder" viewBox="0 0 32 32"><path d="M14 4l4 4h14v22H0V4z"/></symbol><symbol id="images" viewBox="0 0 20 20"><path d="M17.125 6.17L15.08.535c-.152-.416-.596-.637-.99-.492L.492 5.006C.098 5.15-.1 5.603.052 6.02l2.155 5.94V8.777c0-1.438 1.148-2.607 2.56-2.607H8.36l4.285-3.008 2.48 3.008h2zM19.238 8H4.768a.762.762 0 0 0-.763.777v9.42c0 .444.343.803.762.803h14.47c.42 0 .763-.36.763-.803v-9.42A.761.761 0 0 0 19.238 8zM18 17H6v-2l1.984-4.018 2.768 3.436 2.598-2.662 3.338-1.205L18 14v3z"/></symbol><symbol id="upload" viewBox="0 0 24 24"><path d="M14.016 12.984h3L12 8.015l-5.016 4.969h3v4.031h4.031v-4.031zm5.343-2.953C21.937 10.219 24 12.375 24 15a5.021 5.021 0 0 1-5.016 5.016H6c-3.328 0-6-2.672-6-6 0-3.094 2.344-5.625 5.344-5.953C6.61 5.672 9.094 3.985 12 3.985c3.656 0 6.656 2.578 7.359 6.047z"/></symbol></svg><!-- endinject -->
		</div>

		<div class="fb-bar">
			<div class="fb-selected">
				<div class="fb-selected-control">
					<span class="fb-counter">0 Photos Selected</span>
					<a href="" class="fb-deselect">Deselect All</a>
				</div>
				<div class="fb-preview"></div>
			</div>
			<div class="fb-buttons">
				<span class="fb-notice">Only 1 image is allowed</span>
				<a href="https://www.flickr.com/photos/organize" class="fb-button" target="_blank">
					<svg aria-hidden="true" role="presentation" title="Organize Flickr Photos">
					  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#archive"></use>
					</svg>
					Organize
				</a>
				<a href="https://www.flickr.com/photos/upload/" class="fb-button" target="_blank">
					<svg aria-hidden="true" role="presentation" title="Organize Flickr Photos">
					  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#upload"></use>
					</svg>
					Upload
				</a>
			</div>
		</div>
		<div class="flickr-browser">
			<div class="fb-sidebar"></div>
			<div class="fb-browser">
				<div class="fb-loader">
					<img src="<?php echo "{$this->settings['url']}assets/images/oval.svg" ?>" />
				</div>
				<div class="photo-browser-welcome">
					<h1>Select a collection or album on the left</h1>
				</div>
			</div>
		</div>

		<input hidden disabled type="text" id="fb-api-key" value="<?php echo esc_attr($field['flickr_api_key']) ?>" />

		<input hidden disabled type="text" id="fb-user-id" value="<?php echo esc_attr($field['flickr_user_id']) ?>" />

		<?php
			if (is_array($field['flickr_sizes'])) {
				$sizes = implode($field['flickr_sizes'], ",") . ",url_sq";
			} else { $sizes = 'url_sq'; }
			if (is_array($field['flickr_extras'])) {
				$extras = implode($field['flickr_extras'], ",");
			} else { $extras = ''; }
			$flickr_extras = $sizes . "," . $extras;
		?>

		<input hidden disabled type="text" id="fb-extras" value="<?php echo esc_attr($flickr_extras) ?>" />

		<input hidden disabled type="text" id="fb-limit" value="<?php echo esc_attr($field['flickr_limit']) ?>" />

		<textarea hidden id="fb-result-panel" rows="10" name="<?php echo esc_attr($field['name']) ?>"><?php echo esc_attr($field['value']) ?></textarea>

		<?php
	}

	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function input_admin_enqueue_scripts() {
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];

		// register & include Mustache
		wp_register_script( 'acf-flickr_browser-mustache', "https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.2.1/mustache.min.js", array(), false );
		wp_enqueue_script('acf-flickr_browser-mustache');

		// register & include JS
		wp_register_script( 'acf-input-flickr_browser', "{$url}assets/js/input.js", array('acf-input', 'acf-flickr_browser-mustache'), $version, true );
		wp_enqueue_script('acf-input-flickr_browser');

		// register & include CSS
		wp_register_style( 'acf-input-flickr_browser', "{$url}assets/css/input.css", array('acf-input'), $version );
		wp_enqueue_style('acf-input-flickr_browser');

	}


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	// function load_value( $value, $post_id, $field ) {
	// 	return json_decode($value, true);
	// }

	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_head() {



	}

	*/


	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/

   	/*

   	function input_form_data( $args ) {



   	}

   	*/


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_footer() {



	}

	*/


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_enqueue_scripts() {

	}

	*/


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_head() {

	}

	*/



	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	//
	// function update_value( $value, $post_id, $field ) {
	//
	// 	return $value;
	//
	// }



	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {
			return $value;
		}

		if ((int)esc_attr($field['flickr_limit']) == 1) {
			// Single limit
			$val = json_decode($value, true);
			return reset($val);
		} else {
			// Multiple limit
			return json_decode($value, true);
		}
	}


	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/

	/*

	function validate_value( $valid, $value, $field, $input ){

		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}


		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-flickr_browser'),
		}


		// return
		return $valid;

	}

	*/


	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/

	/*

	function delete_value( $post_id, $key ) {



	}

	*/


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	// function load_field( $field ) {
	// 	return $field;
	// }


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function update_field( $field ) {

		return $field;

	}

	*/


	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/

	/*

	function delete_field( $field ) {



	}

	*/


}


// initialize
new acf_field_flickr_browser( $this->settings );


// class_exists check
endif;

?>
