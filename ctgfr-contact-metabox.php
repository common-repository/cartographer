<?php global $post; ?>
<?php wp_nonce_field('save_contact', 'contact_meta_nonce'); ?>
<div style="overflow:hidden; width:100%">
    <div id="ctgfr-fields">
        <p>
            <label for="ctgfr-email">Email</label><br/>
            <input type="text" name="ctgfr_email" id="ctgfr-email" value="<?php echo get_post_meta($post->ID, 'ctgfr_email', true); ?>" />
        </p> 
        <p>
            <label for="ctgfr-phone">Phone</label><br/>
            <input type="text" name="ctgfr_phone" id="ctgfr-phone" value="<?php echo get_post_meta($post->ID, 'ctgfr_phone', true); ?>" />
        </p>   
        <p>
            <label for="ctgfr-website">Website</label><br/>
            <input type="text" name="ctgfr_website" id="ctgfr-website" value="<?php echo get_post_meta($post->ID, 'ctgfr_website', true); ?>" />
        </p> 
    </div>
</div>