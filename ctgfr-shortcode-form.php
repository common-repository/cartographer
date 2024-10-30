<div id="ctgfr-shortcode-form" style="display:none;">
	<div id="ctgfr-settings-form">
		<h1><i class="icon-compass"></i>&nbsp;&nbsp;Choose your map settings:</h1>
		<?php $locations = new WP_Query('post_type=ctgfr_locations&posts_per_page=-1'); ?>
		<?php if($locations->have_posts()) : ?>
			<div class="ctgfr-option">
				<span id="remove-all">Remove all</span>
				<label>Location(s) to Map</label>
				<select multiple id="locations-to-map" name="ctgfr_locations">
					<option value="all">MAP ALL LOCATIONS</option>
					<?php while($locations->have_posts()) : $locations->the_post(); ?>
						<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
					<?php endwhile; ?>
				</select><span class="ctgfr-clearer"></span>
			</div>
			<div class="ctgfr-option">
				<label>Center Map Location</label>
				<select id="center-map-location" name="ctgfr_center_map_location">
					<option></option>
					<option value="all">SHOW ALL LOCATIONS</option>
				<?php while($locations->have_posts()) : $locations->the_post(); ?>
					<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
				<?php endwhile; ?>
				</select><span class="ctgfr-clearer"></span>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					// remove old list
					$("#locations-to-map").chosen({
						placeholder_text_multiple: "Select Some Locations",
						width:"100%"
					});
					// remove old center list
					$('#center_map_location_chosen').remove();
					$("#center-map-location").chosen({
						placeholder_text_single: "Select a Location",
						width:"100%"
					});

					// update chosen when remove all
					$('#remove-all').on('click',function(){
					    $('#locations-to-map option').prop('selected', false);
					    $('#locations-to-map').trigger('chosen:updated').trigger('change');
					});	

					// toggle pin style
					$('.pin-styles input').on('click',function(){
						$('.pin-styles input').removeAttr('class');
						$(this).addClass('active');
					});
				});
			</script>
			<div id="more-options">
				<?php do_action('ctgfr_include_search'); ?>
				<div class="ctgfr-option">
					<label>Zoom level:</label>
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
					<span style="ctgfr-clearer"></span>
				</div>
				<span class="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Width: </label>
					<input type="number" min="0" name="ctgfr_width" value="<?php echo $this->options['width']; ?>" />
					<select name="ctgfr_width_unit">
						<option value="%" <?php if($this->options['width_unit'] == "%"){echo "selected";} ?>>%</option>
						<option value="px" <?php if($this->options['width_unit'] == "px"){echo "selected";} ?>>px</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Height: </label>
					<input type="number" name="ctgfr_height" value="<?php echo $this->options['height']; ?>" />
					<select name="ctgfr_height_unit">
						<option value="%" <?php if($this->options['height_unit'] == "%"){echo "selected";} ?>>%</option>
						<option value="px" <?php if($this->options['height_unit'] == "px"){echo "selected";} ?>>px</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Map Alignment: </label>
					<select name="ctgfr_map_alignment">
						<option value="center" <?php if($this->options['map_alignment'] == "center"){echo "selected";} ?>>Center</option>
						<option value="left" <?php if($this->options['map_alignment'] == "left"){echo "selected";} ?>>Left</option>
						<option value="right" <?php if($this->options['map_alignment'] == "right"){echo "selected";} ?>>Right</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Show Info Bubbles: </label>
					<select name="ctgfr_show_info_bubbles">
						<option value="yes" <?php if($this->options['show_info_bubbles'] == "yes"){echo "selected";} ?>>Yes</option>
						<option value="no" <?php if($this->options['show_info_bubbles'] == "no"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Disable Map UI: </label>
					<select name="ctgfr_disable_ui">
						<option value="true" <?php if($this->options['disable_ui'] == "true"){echo "selected";} ?>>Yes</option>
						<option value="false" <?php if($this->options['disable_ui'] == "false"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Show Location Listing: </label>
					<select name="ctgfr_include_list">
						<option value="yes" <?php if($this->options['include_list'] == "yes"){echo "selected";} ?>>Yes</option>
						<option value="no" <?php if($this->options['include_list'] == "no"){echo "selected";} ?>>No</option>
					</select>
				</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
				<label>Map Type: </label>
				<select name="ctgfr_map_type">
					<option value="ROADMAP" <?php if($this->options['map_type'] == "ROADMAP"){echo "selected";} ?>>Roadmap</option>
					<option value="SATELLITE" <?php if($this->options['map_type'] == "SATELLITE"){echo "selected";} ?>>Satellite</option>
					<option value="HYBRID" <?php if($this->options['map_type'] == "HYBRID"){echo "selected";} ?>>Hybrid</option>
					<option value="TERRAIN" <?php if($this->options['map_type'] == "TERRAIN"){echo "selected";} ?>>Terrain</option>
				</select>
			</div><span style="ctgfr-clearer"></span>
				<div class="ctgfr-option">
					<label>Pin Style: </label>
					<ul class="pin-styles">
						<?php for($i = 1; $i < 16; $i++) : ?>
							<li<?php if(!(($i-1) % 5)){echo ' style="clear:left;"';}?>><label><input type="radio" name="ctgfr_pin_style" value="<?php echo $i; ?>" <?php if($this->options['pin_style'] == $i){ ?>checked="checked" class="active"<?php } ?>/><img src="<?php echo $this->settings['dir']; ?>images/pin-<?php echo $i; ?>.png" /></label></li>
						<?php endfor; ?>
					</ul>
			</div></div><span style="ctgfr-clearer"></span>
			<button class="insert-map button">Insert Cartographer Shortcode</button>
		<?php else : ?>
			<h2>Oops, it appears that you haven't entered any locations yet!</h2>
			<p>Visit the <a href="<?php echo admin_url('edit.php?post_type=ctgfr_locations', 'http'); ?>">Locations page</a> to add a new location!</p>
		<?php endif; ?>
		<br/>
		<span class="ctgfr-clearer"></span>
	</div>
</div>