<style type="text/css">
	.settings-wrap {padding-right:20px;}
	h1 {background: #70b92f; margin-bottom:30px; width:100%;}
	h2 i {float:right;}
	h2 {border-bottom:1px solid #ccc; color:#70b92f; padding-bottom:10px;cursor:pointer;}
	.ctgfr-settings {float:left; width:60%;}
	.ctgfr-settings label {display:block; float:left; width:200px;}
	.ctgfr-settings input[type="text"] {clear:right; display:block; float:right; width:350px;}
	.ctgfr-settings form > div {float:left; clear:left; margin-bottom:10px; max-width: 600px;}
	.ctgfr-settings input[type="submit"] {float:left; display:block; clear:left; margin-top:40px;}
	.kp-cross-promote-area {top:110px;}
</style>
<?php 
	if($_POST['ctgfr_submit'] == 'y'){		

		update_option('ctgfr_zoom', sanitize_text_field($_POST['ctgfr_zoom']));
		$this->options['zoom'] = get_option('ctgfr_zoom');

		update_option('ctgfr_width', sanitize_text_field($_POST['ctgfr_width']));
		$this->options['width'] = get_option('ctgfr_width');

		update_option('ctgfr_width_unit', sanitize_text_field($_POST['ctgfr_width_unit']));
		$this->options['width_unit'] = get_option('ctgfr_width_unit');

		update_option('ctgfr_height', sanitize_text_field($_POST['ctgfr_height']));
		$this->options['height'] = get_option('ctgfr_height');
	
		update_option('ctgfr_height_unit', sanitize_text_field($_POST['ctgfr_height_unit']));
		$this->options['height_unit'] = get_option('ctgfr_height_unit');

		update_option('ctgfr_map_alignment', sanitize_text_field($_POST['ctgfr_map_alignment']));
		$this->options['map_alignment'] = get_option('ctgfr_map_alignment');

		update_option('ctgfr_show_info_bubbles', sanitize_text_field($_POST['ctgfr_show_info_bubbles']));
		$this->options['show_info_bubbles'] = get_option('ctgfr_show_info_bubbles');

		update_option('ctgfr_disable_ui', sanitize_text_field($_POST['ctgfr_disable_ui']));
		$this->options['disable_ui'] = get_option('ctgfr_disable_ui');

		update_option('ctgfr_include_list', sanitize_text_field($_POST['ctgfr_include_list']));
		$this->options['include_list'] = get_option('ctgfr_include_list');

		update_option('ctgfr_pin_style', sanitize_text_field($_POST['ctgfr_pin_style']));
		$this->options['pin_style'] = get_option('ctgfr_pin_style');
	
		update_option('ctgfr_map_type', sanitize_text_field($_POST['ctgfr_map_type']));
		$this->options['map_type'] = get_option('ctgfr_map_type');

		update_option('ctgfr_include_search', sanitize_text_field($_POST['ctgfr_include_search']));
		$this->options['include_search'] = get_option('ctgfr_include_search');

		update_option('ctgfr_map_style', htmlentities(stripslashes($_POST['ctgfr_map_style'])));
		$this->options['map_style'] = get_option('ctgfr_map_style');

	}
?>
<div class="settings-wrap">
	<h1><img src="<?php echo plugins_url('/images/cartographer-banner.png', __FILE__); ?>" alt="Cartographer"/></h1>
	<div class="ctgfr-settings">
		<h2>How to use this plugin: <i class="icon-caret-down"></i></h2>
		<div style="display:none;">
			<h4>General Usage</h4>
			<ol>
				<li>You've already found and installed Cartographer so the hard part is done!!</li>
				<li>Now its time to create some <a href="post-new.php?post_type=ctgfr_locations">new locations</a>.</li>
				<li>Enter the location details and don't forget to enter the address (and contact info), save the location post.</li>
				<li>Edit any page/post and click on the "Add Cartographer Map" button above the Main content editor.</li>
				<li>Select the settings for the map you wish to display, and click "Insert Cartographer Shortcode", save the post.</li>
				<li>All done! View your new map on the page/post with the shortcode you just generated.</li> 
			</ol>
			<br/>
			<h4>To use any of the data from Cartographer in your custom template, reference below.</h4>
			<ul>
				<li>Post type: <code>&lt;?php $locations = new WP_Query(array('post_type' => 'ctgfr_locations')); ?&gt;</code></li>
				<li>Address meta: <code>&lt;?php $address = get_post_meta($post_id, 'ctgfr_geocode_address', true); ?&gt;</code></li>
				<li>LatLng meta: <code>&lt;?php $latlng = get_post_meta($post_id, 'ctgfr_geocode_latlng', true); ?&gt;</code></li>
				<li>Email meta: <code>&lt;?php $email = get_post_meta($post_id, 'ctgfr_email', true); ?&gt;</code></li>
				<li>Phone meta: <code>&lt;?php $phone = get_post_meta($post_id, 'ctgfr_phone', true); ?&gt;</code></li>
				<li>Website meta: <code>&lt;?php $website = get_post_meta($post_id, 'ctgfr_website', true); ?&gt;</code></li>
			</ul>
		</div>
		<br/>
		<h2>Default options: <i class="icon-caret-up"></i></h2>
		<div>
			<h4>These settings will be the default values when the shortcode builder form is displayed.</h4>
			<form method="post" action="" id="ctgfr-settings-form" >
				<div class="ctgfr-option">
					<label>Zoom level</label>
					<div class="zoom-wrap">
						<span class="min">min</span>
						<div class="control">
							<span class="zoom-value">
								<?php echo $this->options['zoom']; ?>
							</span>
							<input type="range" name="ctgfr_zoom" value="<?php echo $this->options['zoom']; ?>" max="19" min="0" step="1"/>
						</div>
						<span class="max">max</span>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$("input[name='ctgfr_zoom']").change(function(){
								$('#ctgfr-settings-form .zoom-value').html($(this).val());
							});
						});
					</script>
				</div><span style="ctgfr-clearer"></span>
				<?php do_action('ctgfr_include_search'); ?>
				<div class="ctgfr-option">
					<label>Width </label>
					<input type="number" min="0" name="ctgfr_width" value="<?php echo $this->options['width']; ?>" />
					<select name="ctgfr_width_unit">
						<option value="%" <?php if($this->options['width_unit'] == "%"){echo "selected";} ?>>%</option>
						<option value="px" <?php if($this->options['width_unit'] == "px"){echo "selected";} ?>>px</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Height </label>
					<input type="number" name="ctgfr_height" value="<?php echo $this->options['height']; ?>" />
					<select name="ctgfr_height_unit">
						<option value="%" <?php if($this->options['height_unit'] == "%"){echo "selected";} ?>>%</option>
						<option value="px" <?php if($this->options['height_unit'] == "px"){echo "selected";} ?>>px</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Map Alignment </label>
					<select name="ctgfr_map_alignment">
						<option value="center" <?php if($this->options['map_alignment'] == "center"){echo "selected";} ?>>Center</option>
						<option value="left" <?php if($this->options['map_alignment'] == "left"){echo "selected";} ?>>Left</option>
						<option value="right" <?php if($this->options['map_alignment'] == "right"){echo "selected";} ?>>Right</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Show Info Bubbles </label>
					<select name="ctgfr_show_info_bubbles">
						<option value="yes" <?php if($this->options['show_info_bubbles'] == "yes"){echo "selected";} ?>>Yes</option>
						<option value="no" <?php if($this->options['show_info_bubbles'] == "no"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Disable Map UI </label>
					<select name="ctgfr_disable_ui">
						<option value="true" <?php if($this->options['disable_ui'] == "true"){echo "selected";} ?>>Yes</option>
						<option value="false" <?php if($this->options['disable_ui'] == "false"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Show Location Listing </label>
					<select name="ctgfr_include_list">
						<option value="yes" <?php if($this->options['include_list'] == "yes"){echo "selected";} ?>>Yes</option>
						<option value="no" <?php if($this->options['include_list'] == "no"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Map Type </label>
					<select name="ctgfr_map_type">
						<option value="ROADMAP" <?php if($this->options['map_type'] == "ROADMAP"){echo "selected";} ?>>Roadmap</option>
						<option value="SATELLITE" <?php if($this->options['map_type'] == "SATELLITE"){echo "selected";} ?>>Satellite</option>
						<option value="HYBRID" <?php if($this->options['map_type'] == "HYBRID"){echo "selected";} ?>>Hybrid</option>
						<option value="TERRAIN" <?php if($this->options['map_type'] == "TERRAIN"){echo "selected";} ?>>Terrain</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Pin Style </label>
					<ul class="pin-styles">
						<?php for($i = 1; $i < 16; $i++) : ?>
							<li<?php if(!(($i-1) % 5)){echo ' style="clear:left;"';}?>><label><input type="radio" name="ctgfr_pin_style" value="<?php echo $i; ?>" <?php if($this->options['pin_style'] == $i){ ?>checked="checked" class="active"<?php } ?>/><img src="<?php echo $this->settings['dir']; ?>images/pin-<?php echo $i; ?>.png" /></label></li>
						<?php endfor; ?>
					</ul>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					Map Styling <span class="description">(leave blank for default styles)</span>
					<textarea name="ctgfr_map_style" rows="8" cols="80"><?php echo $this->options['map_style']; ?></textarea>
					<br/><span class="description">Paste Javascript style array here. A good resource for styles can be found at <a target="_blank" href="http://snazzymaps.com/">snazzymaps.com</a></span>
				</div><span style="ctgfr-clearer"></span>
				<input type="hidden" name="ctgfr_submit" value="y" />
				<input type="submit" class="button-primary" value="Save Options" />
			</form>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$("h2").on('click',function(){
						$(this).next().slideToggle('fast');
						$('i',this).toggleClass('icon-caret-down icon-caret-up');
					});
				});
			</script>
		</div>
	</div>
	<div class="kp-cross-promote-area">
		<?php $this->cross_promotions('cartographer'); ?>
	</div>
</div>