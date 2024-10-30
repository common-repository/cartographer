<?php global $post; ?>
<?php wp_nonce_field('save_latlng', 'geocoder_meta_nonce'); ?>
<div style="overflow:hidden; width:100%">
    <div id="ctgfr-fields">
        <p>
            <label for="ctgfr-geocoder-address">Address</label><br />
            <input type="text" name="ctgfr_geocoder_address" id="ctgfr-geocoder-address" value="<?php echo get_post_meta($post->ID, 'ctgfr_geocoder_address', true); ?>" />
        </p>
        <span class="description">TIP: You can drag and drop the map marker to choose a new address.</span>
        <div id="ctgfr-preview"></div>
        <input type="hidden" name="ctgfr_geocoder_latlng" id="ctgfr-geocoder-latlng" value="<?php echo get_post_meta($post->ID, 'ctgfr_geocoder_latlng', true); ?>" />
        <?php echo apply_filters('ctgfr_after_meta','',$post->ID); ?>
    </div>

    <script src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($){ 
            var latlngstr = $('#ctgfr-geocoder-latlng').val();
            var latlng = latlngstr.substring(1, latlngstr.length-1).split(',');
            var thislatlng = new google.maps.LatLng(latlng[0],latlng[1]);
            var mapOptions = {
                zoom: 15,
                disableDefaultUI: true,
                center: thislatlng
            }
            var map = new google.maps.Map(document.getElementById("ctgfr-preview"),mapOptions);
            var marker = new google.maps.Marker({
                position: thislatlng,
                map: map,
                draggable:true,
                title:"Drag me!"
            });
            // get new pin latlng
            var geocoder= new google.maps.Geocoder();
            google.maps.event.addListener(marker, 'dragend', function() {
                geocodePosition(marker.getPosition());
            });
            function geocodePosition(pos) {
                geocoder.geocode({
                    latLng: pos
                }, function(responses) {
                    if(responses && responses.length > 0) {
                        $('#ctgfr-geocoder-address').val(responses[0].formatted_address);
                    }else{
                       alert('Cannot determine address at this location.');
                    }
                });
            }

            <?php echo apply_filters('ctgfr_add_meta_polygon','',$post->ID); ?>
        });
    </script>
</div>