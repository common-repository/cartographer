<?php
	/*
	Plugin Name: Cartographer
	Plugin URI: http://kraftpress.it/plugins/cartographer
	Description: Easily add a custom Google map to your WordPress site. Multiple locations on a single map with ease.
	Version: 1.2.5
	Author: kraftpress
	Author URI: http://kraftpress.it/
	Contributors: kraftpress, buildcreate, a2rocklobster
	License: GPL
	*/

	// If this file is called directly, abort.
	if(!defined('WPINC')){die;}

	// check for session
	if(session_id() == ''){session_start();}

	class Cartographer {

		public $settings;
		public $options;

		function __construct(){

			//includes
			require_once('ctgfr-widget.php');	
			
			// helpers
			add_filter('helpers/get_path', array($this, 'helpers_get_path'), 1, 1);
			add_filter('helpers/get_dir', array($this, 'helpers_get_dir'), 1, 1);

			// actions
			add_action('init', array($this, 'init'), 1);
			add_action('admin_menu', array($this,'admin_menu'), 999);
			add_action('get_post_id', array($this, 'get_post_id'), 1, 1);
			add_action('add_meta_boxes', array($this, 'ctgfr_meta_box'), 1, 1);
			add_action('add_meta_boxes', array($this, 'ctgfr_geocode_box'), 1, 1);
			add_action('save_post', array($this, 'ctgfr_save_geocode_meta'), 1, 1);
			add_action('save_post', array($this, 'ctgfr_save_contact_meta'), 1, 1);
			add_action('admin_footer', array($this, 'add_ctgfr_popup'), 1, 1);
			add_action('admin_enqueue_scripts', array($this, 'ctgfr_styles'),1, 1);
			add_action('admin_enqueue_scripts', array($this, 'ctgfr_scripts'), 1, 1);
			add_action('wp_enqueue_scripts', array($this, 'ctgfr_styles'), 1, 1);
			add_action('wp_enqueue_scripts', array($this, 'ctgfr_scripts'), 1, 1);
			add_action('after_setup_theme', array($this, 'ctgfr_extensions'), 1, 1);
			add_action('wp_footer', array($this, 'ctfgr_clear_session'), 1, 1);

			// filters
			add_filter('get_info', array($this, 'get_info'), 1, 1);
			add_filter('get_post_types', array($this, 'get_post_types'), 1, 3);
			add_filter('get_taxonomies_for_select', array($this, 'get_taxonomies_for_select'), 1, 2);
			add_filter('get_image_sizes', array($this, 'get_image_sizes'), 1, 1);
			add_filter('media_buttons_context', array($this, 'ctgfr_media_button'), 1, 1);
			
			// shortcode
			add_shortcode('cartographer', array($this, 'render_cartographer'), 1 ,1);

			// vars
			$this->settings = array(
				'path' => apply_filters('helpers/get_path', __FILE__),
				'dir' => apply_filters('helpers/get_dir', __FILE__),
				'hook' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ),
				'version' => '0.1',
				'upgrade_version' => '0.1'
			);

			// defaults
			$this->options = array(
				'zoom' => 8,
				'width' => 100,
				'width_unit' => '%',
				'height' => 400,
				'height_unit' => 'px',
				'map_alignment' => 'center',
				'show_info_bubbles' => 'yes',
				'disable_ui' => 'no',
				'include_list' => 'no',
				'pin_style' => 1,
				'map_type' => 'road',
				'include_search' => 'yes',
				'map_style' => ''
			);
		}
		
		function init(){
			
			// Create location post type
			$labels = array(
			    'name' => __('Locations', 'ctgfr'),
				'singular_name' => __( 'Location', 'ctgfr'),
			    'add_new' => __('Add New' , 'ctgfr'),
			    'add_new_item' => __('Add New Location' , 'ctgfr'),
			    'edit_item' =>  __('Edit Location' , 'ctgfr'),
			    'new_item' => __('New Location' , 'ctgfr'),
			    'view_item' => __('View Location', 'ctgfr'),
			    'search_items' => __('Search Locations', 'ctgfr'),
			    'not_found' =>  __('No Locations found', 'ctgfr'),
			    'not_found_in_trash' => __('No Locations found in Trash', 'ctgfr'), 
			);
			
			register_post_type('ctgfr_locations', array(
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'_builtin' =>  false,
				'capability_type' => 'page',
				'hierarchical' => true,
				'rewrite' => false,
				'query_var' => "ctgfr_locations",
				'supports' => array(
					'title',
					'editor',
					'thumbnail',
					'revisions'
				),
				'show_in_menu'	=> false,
			));

			// set defaults
			$this->ctgfr_set_options();
		}
	
		function ctgfr_set_options(){
			// set updated options
			if(get_option('ctgfr_zoom') != ''){$this->options['zoom'] = get_option('ctgfr_zoom');}
			if(get_option('ctgfr_width') != ''){$this->options['width'] = get_option('ctgfr_width');}
			if(get_option('ctgfr_width_unit') != ''){$this->options['width_unit'] = get_option('ctgfr_width_unit');}
			if(get_option('ctgfr_height') != ''){$this->options['height'] = get_option('ctgfr_height');}
			if(get_option('ctgfr_height_unit') != ''){$this->options['height_unit'] = get_option('ctgfr_height_unit');}
			if(get_option('ctgfr_map_alignment') != ''){$this->options['map_alignment'] = get_option('ctgfr_map_alignment');}
			if(get_option('ctgfr_show_info_bubbles') != ''){$this->options['show_info_bubbles'] = get_option('ctgfr_show_info_bubbles');}
			if(get_option('ctgfr_disable_ui') != ''){$this->options['disable_ui'] = get_option('ctgfr_disable_ui');}
			if(get_option('ctgfr_include_list') != ''){$this->options['include_list'] = get_option('ctgfr_include_list');}
			if(get_option('ctgfr_pin_style') != ''){$this->options['pin_style'] = get_option('ctgfr_pin_style');}
			if(get_option('ctgfr_map_type') != ''){$this->options['map_type'] = get_option('ctgfr_map_type');}
			if(get_option('ctgfr_outer_bounds') != ''){$this->options['outer_bounds'] = get_option('ctgfr_outer_bounds');}
			if(get_option('ctgfr_include_search') != ''){$this->options['include_search'] = get_option('ctgfr_include_search');}
			$this->options['map_style'] = get_option('ctgfr_map_style');
		}
		
		function ctgfr_scripts() {
			// register cartographer scripts
			wp_enqueue_script('ctgfr-chosen', $this->settings['dir'] . 'js/chosen_v1.1.0/chosen.jquery.min.js', array('jquery'), $this->settings['version'], false );
			wp_enqueue_script('ctgfr-js', $this->settings['dir'] . 'js/ctgfr.js', array('jquery'), date('YmdHis'), false );
			if(!is_admin()){
				wp_enqueue_script('ctgfr-googlemaps', '//maps.googleapis.com/maps/api/js?v=3&sensor=false', array('jquery'), $this->settings['version'], false );
			}
		}
		
		function ctgfr_styles() {
			// register cartographer styles
			wp_enqueue_style('ctgfr-font-awesome', $this->settings['dir'] . 'css/fonts.css', false, $this->settings['version']); 
			wp_enqueue_style('ctgfr-chosen', $this->settings['dir'] . 'js/chosen_v1.1.0/chosen.css', false, $this->settings['version']); 
			wp_enqueue_style('ctgfr-css', $this->settings['dir'] . 'css/ctgfr.css', false, $this->settings['version']); 
		}
		
		function ctgfr_geocode_address($address) {
 
			$string = str_replace (" ", "+", urlencode($address));
			$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $details_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = json_decode(curl_exec($ch), true);
			// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
			if ($response['status'] != 'OK') {
				//$error = new WP_Error();
				//$error->add(__('geocode_fail', 'Address failed to geocode, please check that it is entered correctly and try again.', 'ctgfr'));
				return null;
			}
		 
		    $longitude = $response['results'][0]['geometry']['location']['lng'];
		    $latitude = $response['results'][0]['geometry']['location']['lat'];
			$location = '('.$latitude.','.$longitude.')';
			 
		    return $location;
		}
						
		function ctgfr_save_geocode_meta($post_id){
			// Check if our nonce is set.
			if(!isset($_POST['geocoder_meta_nonce'])){return;}
			// Verify that the nonce is valid.
			if(!wp_verify_nonce($_POST['geocoder_meta_nonce'], 'save_latlng')){return;}
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
			// Check the user's permissions.
			if(isset($_POST['post_type']) && 'crtfr_locations' == $_POST['post_type']){
				if(!current_user_can('edit_page', $post_id)){return;}
				if(!current_user_can('edit_post', $post_id)){return;}
			}
			/* OK, it's safe for us to save the data now. */
			$ctgfr_geocoder_address = sanitize_text_field($_POST['ctgfr_geocoder_address']);
			$ctgfr_geocoder_latlng = $this->ctgfr_geocode_address($ctgfr_geocoder_address);
			// Update the meta field in the database.
			update_post_meta($post_id, 'ctgfr_geocoder_address', $ctgfr_geocoder_address);
			update_post_meta($post_id, 'ctgfr_geocoder_latlng', $ctgfr_geocoder_latlng);
			do_action('ctgfr_meta_update', $post_id, $_POST);
		}

		function ctgfr_save_contact_meta($post_id){
			// Check if our nonce is set.
			if(!isset($_POST['contact_meta_nonce'])){return;}
			// Verify that the nonce is valid.
			if(!wp_verify_nonce($_POST['contact_meta_nonce'], 'save_contact')){return;}
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
			// Check the user's permissions.
			if(isset($_POST['post_type']) && 'crtfr_locations' == $_POST['post_type']){
				if(!current_user_can('edit_page', $post_id)){return;}
				if(!current_user_can('edit_post', $post_id)){return;}
			}
			/* OK, it's safe for us to save the data now. */
			$ctgfr_email = sanitize_text_field($_POST['ctgfr_email']);
			$ctgfr_phone = sanitize_text_field($_POST['ctgfr_phone']);
			$ctgfr_website = sanitize_text_field($_POST['ctgfr_website']);

			// Update the meta field in the database.
			update_post_meta($post_id, 'ctgfr_email', $ctgfr_email);
			update_post_meta($post_id, 'ctgfr_phone', $ctgfr_phone);
			update_post_meta($post_id, 'ctgfr_website', $ctgfr_website);
		}

	    function ctgfr_meta_box(){
	    	add_meta_box('ctgfr-contact-information', 'Contact Information', array($this, 'meta_box_contact_html'), 'ctgfr_locations', 'normal', 'high');
		}    

		function meta_box_contact_html(){
			include_once('ctgfr-contact-metabox.php');
		}	

		function ctgfr_geocode_box(){
	    	add_meta_box('ctgfr-geocoder', 'Address Geocoder', array($this, 'meta_box_geocode_html'), 'ctgfr_locations', 'normal', 'high');
		}	

		function meta_box_geocode_html(){
			include_once('ctgfr-geocoder-metabox.php');
		}
		
		function admin_menu(){
			add_menu_page(__("Cartographer",'ctgfr'), __("Cartographer",'ctgfr'), 'manage_options', 'cartographer', array( $this, 'ctgfr_settings_callback'));
			add_submenu_page('cartographer',__("Edit Locations",'ctgfr'), __("Edit Locations",'ctgfr'), 'manage_options', 'edit.php?post_type=ctgfr_locations');
			add_submenu_page('cartographer',__("Add New Location",'ctgfr'), __("Add New Location",'ctgfr'), 'manage_options', 'post-new.php?post_type=ctgfr_locations');
		}

		function ctgfr_settings_callback(){
			$path = $this->settings['path'];
			include_once($path . 'ctgfr-settings.php');
		}

		function ctgfr_media_button($context){
			if(get_post_type() != 'ctgfr_locations'){
				$context .= '<a class="button" id="add-ctgfr-map" title="Add Cartographer Map"><span class="icon-compass"></span>&nbsp;&nbsp;Add Cartographer Map</a>';
			}
			return $context;
		}

		function add_ctgfr_popup(){
			include('ctgfr-shortcode-form.php');
		}

		function render_cartographer($atts){
			ob_start();

			// check for multiple map instances
			if(isset($_SESSION['map_num'])){$map_num = $_SESSION['map_num'];}
			else{$map_num = 1;}

			$center_latlng = apply_filters('ctgfr_get_center', '', $this, $map_num, $_POST);
			if($center_latlng){
				$center_lat = $center_latlng[0];
				$center_lng = $center_latlng[1];
			}else{
				$center_lat = 0;
				$center_lng = 0;
			}

			$radius = apply_filters('ctgfr_get_radius', '', $map_num, $_POST);
			
			// check if all locations
			if($atts['locations'] == 'all'){
				$args = array(
					'post_type' => 'ctgfr_locations',
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);	
			}else{
				$location_ids = explode(',',$atts['locations']);
				$args = array(
					'post_type' => 'ctgfr_locations',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'post__in' => $location_ids
				);	
			}

			$args = apply_filters('ctgfr_after_location_args', $args, $map_num, $_POST);
	
			$results = false;
			// load all addresses into this for javascript to read from for map
			?>
			<div id="ctgfr-map-info-<?php echo $map_num; ?>" style="display:none;">
				<?php $locations = new WP_Query($args); ?>
				<?php if($locations->have_posts()) : ?>
					<?php $found_locations = array(); ?>
					<?php while($locations->have_posts()) : $locations->the_post(); ?>	
						<?php $post_id = get_the_ID(); ?>
						<?php 
							if($post_id == $atts['center_map_location']){
								$latlng = explode(',',str_replace('(', '', str_replace(')','', get_post_meta($post_id,'ctgfr_geocoder_latlng',true))));
								$center_lat = $latlng[0];
								$center_lng = $latlng[1];
							}
						?>
						<?php if($center_latlng) : ?>
							<?php if($distance = apply_filters('ctgfr_check_proximity', '', $center_latlng, get_post_meta($post_id,'ctgfr_geocoder_latlng',true), $map_num, $_POST)) : ?>
								<div class="google-address">
									<div><span class="title"><?php the_title(); ?></span><span class="address"><?php echo get_post_meta($post_id,'ctgfr_geocoder_address',true); ?></span><span class="email"><?php echo get_post_meta($post_id,'ctgfr_email',true); ?></span><span class="phone"><?php echo get_post_meta($post_id,'ctgfr_phone',true); ?></span><span class="website"><?php echo get_post_meta($post_id,'ctgfr_website',true); ?></span><span class="latlng"><?php echo get_post_meta($post_id,'ctgfr_geocoder_latlng',true); ?></span><span class="content"><?php the_content(); ?></span></div>
								</div>
								<?php $found_locations[$post_id] = $distance; ?>
								<?php $results = true; ?>
							<?php endif; ?>
						<?php else : ?>
							<div class="google-address">
								<div><span class="title"><?php the_title(); ?></span><span class="address"><?php echo get_post_meta($post_id,'ctgfr_geocoder_address',true); ?></span><span class="email"><?php echo get_post_meta($post_id,'ctgfr_email',true); ?></span><span class="phone"><?php echo get_post_meta($post_id,'ctgfr_phone',true); ?></span><span class="website"><?php echo get_post_meta($post_id,'ctgfr_website',true); ?></span><span class="latlng"><?php echo get_post_meta($post_id,'ctgfr_geocoder_latlng',true); ?></span><span class="content"><?php the_content(); ?></span></div>
							</div>
							<?php $results = true; ?>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
			
			<div id="ctgfr-map-wrap-<?php echo $map_num; ?>" class="ctgfr-map-wrap">
				<?php 
					// check for default
					$switch = ($atts['map_alignment'] ? $atts['map_alignment'] : get_option('ctgfr_map_alignment'));
					switch($switch){
						case 'center': $style = 'clear:both; margin-left:auto; margin-right:auto;'; break;
						case 'left': $style = 'float:left; margin-right:0.5em;'; break;
						case 'right': $style = 'float:right; margin-left:0.5em;'; break;
					}
				?>

				<?php $width = ($atts['width'] ? $atts['width'] : get_option('ctgfr_width') . get_option('ctgfr_width_unit')); ?>
				<?php $height = ($atts['height'] ? $atts['height'] : get_option('ctgfr_height') . get_option('ctgfr_height_unit')); ?>
				<style type="text/css">
					#ctgfr-map-canvas-<?php echo $map_num; ?>{
						<?php echo $style; ?>
						width: <?php echo $width; ?>;
						height: <?php echo $height; ?>;
					}
					.ctgfr-no-results-<?php echo $map_num; ?>{
						text-align: center;
						<?php echo $style; ?>
						width: <?php echo $width; ?>;
						height: <?php echo $height; ?>;
						background: #ccc;
						margin-bottom:20px;
					}
					.ctgfr-no-results-<?php echo $map_num; ?> p {
						margin-top:24%;
					}
					.ctgfr-map-canvas img {
						max-width: none;
					}
				</style>

				<?php do_action('ctgfr_before_map_canvas', $map_num, $atts); ?>
				<?php if($results) : ?>
					<div id="ctgfr-map-canvas-<?php echo $map_num; ?>" class="ctgfr-map-canvas"></div>
					<span class="ctgfr-clearer"></span>
					<?php if($atts['include_list'] == 'yes') : ?>
						<?php $i = 1; ?>
						<?php $locations = apply_filters('ctgfr_check_found_locations', $locations, $found_locations, $args); ?>
						<?php if($locations->have_posts()) : ?>
							<ul class="ctgfr-location-list">
							<?php while($locations->have_posts()) : $locations->the_post();?>	
								<li <?php echo $i%2 == 0 ? 'class="even" ' : ''; $i++; ?>>
									<h3><?php the_title(); ?>  <a target="_blank" href="https://maps.google.com?saddr=<?php echo htmlentities($_POST['ctgfr_address_search']); ?>&daddr=<?php echo get_post_meta(get_the_ID() ,'ctgfr_geocoder_address',true); ?>">
										<em><?php echo apply_filters('ctgfr_get_distance', '', $found_locations, get_the_ID()); ?>(directions)</em></a></h3>
									<div class="location-meta">
										<address><?php echo get_post_meta(get_the_ID() ,'ctgfr_geocoder_address',true); ?></address>
										<?php if($email = get_post_meta(get_the_ID() ,'ctgfr_email',true)) : ?>
											<address>email: <?php echo $email; ?></address>
										<?php endif; ?>
										<?php if($phone = get_post_meta(get_the_ID() ,'ctgfr_phone',true)) : ?>
											<address>phone: <?php echo $phone; ?></address>
										<?php endif; ?>
										<?php if($website = get_post_meta(get_the_ID() ,'ctgfr_website',true)) : ?>
											<?php preg_replace('/^https?:\/\//', '', $website); ?>
											<address>website: <a target="_blank" href="//<?php echo $website; ?>"><?php echo $website; ?></a></address>
										<?php endif; ?>
									</div>
									<div><?php the_content(); ?></div>
								</li>
							<?php endwhile; ?>
							</ul>
						<?php endif; ?>
					<?php endif; ?>
					<span class="ctgfr-clearer"></span>
					<?php 
						// check for defaults
						$zoom = ($atts['zoom'] ? $atts['zoom'] : get_option('ctgfr_zoom'));
						$disable_ui = ($atts['disable_ui'] ? $atts['disable_ui'] : get_option('ctgfr_disable_ui'));
						$map_type = ($atts['map_type'] ? $atts['map_type'] : get_option('ctgfr_map_type'));
						$pin_style = ($atts['pin_style'] ? $atts['pin_style'] : get_option('ctgfr_pin_style'));
						$show_info_bubbles = ($atts['show_info_bubbles'] ? $atts['show_info_bubbles'] : get_option('ctgfr_show_info_bubbles'));
					?>
					
					<script type="text/javascript">
						jQuery(document).ready(function($){	
							// setup map options
							var mapOptions = {
								zoom: <?php echo $zoom; ?>,
								disableDefaultUI: <?php echo $disable_ui; ?>,
								center: new google.maps.LatLng(<?php echo $center_lat; ?>,<?php echo $center_lng; ?>),
								mapTypeId: google.maps.MapTypeId.<?php echo $map_type; ?>,
								<?php if($this->options['map_style'] != '') : ?>
								styles: <?php echo html_entity_decode($this->options['map_style']); ?>
								<?php endif; ?>
							}
							
							// make map
							var map = new google.maps.Map(document.getElementById("ctgfr-map-canvas-<?php echo $map_num; ?>"),mapOptions);
							
							// setup vars
							var LatLngList = new Array();	
							var infowindows = [];			
							// map addresses
							$('#ctgfr-map-info-<?php echo $map_num; ?> .google-address div').each(function(){
								var latlng = $('.latlng', this).text();
								if(latlng.length > 0){
									latlng = latlng.replace('(', '').replace(')', '');
									var lat = latlng.split(',')[0];
									var lng = latlng.split(',')[1];
									var latlng = new google.maps.LatLng(lat,lng);
									var title = $('.title', this).text();
									var address = $('.address', this).text();
									var content = $('.content', this).text();
									var email = $('.email', this).text();
									var phone = $('.phone', this).text();
									var website = $('.website', this).text();

									// assign pin based on hierarchy
									var image = '<?php echo $this->settings['dir']; ?>images/pin-<?php echo $pin_style; ?>.png';
							
									var marker = new google.maps.Marker({
										map: map,
										position: latlng,
										icon : image
									});	
									if('<?php echo $show_info_bubbles; ?>' == 'yes'){
										var data = "<strong>"+title+"</strong><br/><div class='ctgfr-location-address'><address>"+address+"<br/>";
										if(phone){data += phone+" | ";}
										if(email){data += email+" | ";}
										if(website){data += website;}
										data += "</div>"+content;
									
									    var infowindow = new google.maps.InfoWindow({
									     	content: data
									    });
									    infowindows.push(infowindow);
										google.maps.event.addListener(marker, 'click', function() {
											// close all first
									    	for(var i=0;i<infowindows.length;i++){
											    infowindows[i].close();
											}
											infowindow.open(map,marker);
									    });
									}
									// boundary object
									LatLngList.push(latlng);
								}
							});
							
							// make bounds if necessary
							if('<?php echo $atts['center_map_location']; ?>' == 'all'){
								//  Create a new viewpoint bound
								var bounds = new google.maps.LatLngBounds();
								
								//  Go through each...
								for (var i = 0; i < LatLngList.length; i++) {
									//  And increase the bounds to take this point
									bounds.extend(LatLngList[i]);
								}
								
								//  Fit these bounds to the map
								map.fitBounds(bounds);
							}
							<?php echo apply_filters('ctgfr_draw_radius', '', $center_latlng, $radius); ?>
						});
					</script>
				<?php else : ?>
					<div class="ctgfr-no-results-<?php echo $map_num; ?>"><p>Sorry, there were no locations found that match your search criteria.</p></div>
				<?php endif; ?>
			</div><?php
			$map_num++; $_SESSION['map_num'] = $map_num;
			wp_reset_postdata();
			return ob_get_clean(); 
	    }
	    
		/* Plugin cross promotion area */
		function cross_promotions($plugin) {
			include('kp-cross-promote.php');
		}

		function ctgfr_extensions(){
			do_action('ctgfr_extensions');
		}

		function ctfgr_clear_session(){
			session_destroy();
		}

		function helpers_get_path($file){
	        return trailingslashit(dirname($file));
	    }
	    
	    function helpers_get_dir($file){
	        $dir = trailingslashit(dirname($file));
	        $count = 0;
	        
	        // sanitize for Win32 installs
	        $dir = str_replace('\\' ,'/', $dir); 
	        
	        // if file is in plugins folder
	        $wp_plugin_dir = str_replace('\\' ,'/', WP_PLUGIN_DIR); 
	        $dir = str_replace($wp_plugin_dir, plugins_url(), $dir, $count);
	        
	        if($count < 1){
		        // if file is in wp-content folder
		        $wp_content_dir = str_replace('\\' ,'/', WP_CONTENT_DIR); 
		        $dir = str_replace($wp_content_dir, content_url(), $dir, $count);
	        }
	       
	        if($count < 1){
		        // if file is in ??? folder
		        $wp_dir = str_replace('\\' ,'/', ABSPATH); 
		        $dir = str_replace($wp_dir, site_url('/'), $dir);
	        }
	        
	        return $dir;
	    }
	}
	$ctgfr = new Cartographer();
?>