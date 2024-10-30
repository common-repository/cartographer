jQuery(document).ready(function($){
	var zoom = $("input[name='ctgfr_zoom']").val();
	var zoom_default = $("input[name='ctgfr_zoom']").val();
	var width = $("input[name='ctgfr_width']").val();
	var width_default = $("input[name='ctgfr_width']").val();
	var width_unit = $("select[name='ctgfr_width_unit']").val();
	var width_unit_default = $("select[name='ctgfr_width_unit']").val();
	var height = $("input[name='ctgfr_height']").val();
	var height_default = $("input[name='ctgfr_height']").val();
	var height_unit = $("select[name='ctgfr_height_unit']").val();
	var height_unit_default = $("select[name='ctgfr_height_unit']").val();
	var map_alignment = $("select[name='ctgfr_map_alignment']").val();
	var map_alignment_default = $("select[name='ctgfr_map_alignment']").val();
	var show_info_bubbles = $("select[name='ctgfr_show_info_bubbles']").val();
	var show_info_bubbles_default = $("select[name='ctgfr_show_info_bubbles']").val();
	var disable_ui = $("select[name='ctgfr_disable_ui']").val();
	var disable_ui_default = $("select[name='ctgfr_disable_ui']").val();
	var pin_style = $("input[name='ctgfr_pin_style']").val();
	var pin_style_default = $("input[name='ctgfr_pin_style']").val();
	var include_list = $("select[name='ctgfr_include_list']").val();
	var include_list_default = $("select[name='ctgfr_include_list']").val();
	var map_type = $("select[name='ctgfr_map_type']").val();
	var map_type_default = $("select[name='ctgfr_map_type']").val();
	var locations = null;
	var center_map_location = null;
	var include_search = $("select[name='ctgfr_include_search']").val();
	var include_search_default = $("select[name='ctgfr_include_search']").val();
	var $trigger = null;
	
	// manually call ThickBox to keep content
	$(document).on('click','#add-ctgfr-map, .add-ctgfr-map',function(e){
		$trigger = $(this);
		e.preventDefault();
		tb_show('Add Cartographer Map','#TB_inline?width=640&height=700&inlineId=ctgfr-shortcode-form');
	});
	// update values on change
	$(document).on('change','#TB_ajaxContent input, #TB_ajaxContent select', function(){
		switch($(this).attr('name')){
			case 'ctgfr_zoom': 
				zoom = $(this).val();
				$('#ctgfr-settings-form .zoom-value').html(zoom);
				break;
			case 'ctgfr_width': width = $(this).val(); break;
			case 'ctgfr_width_unit': width_unit = $(this).val(); break;
			case 'ctgfr_height': height = $(this).val(); break;
			case 'ctgfr_height_unit': height_unit = $(this).val(); break;
			case 'ctgfr_map_alignment': map_alignment = $(this).val(); break;
			case 'ctgfr_show_info_bubbles': show_info_bubbles = $(this).val(); break;
			case 'ctgfr_disable_ui': disable_ui = $(this).val(); break;
			case 'ctgfr_include_list': include_list = $(this).val(); break;
			case 'ctgfr_pin_style': pin_style = $(this).val(); break;
			case 'ctgfr_locations': locations = $(this).val(); break;
			case 'ctgfr_center_map_location': center_map_location = $(this).val(); break;
			case 'ctgfr_map_type': map_type = $(this).val(); break;
			case 'ctgfr_include_search': include_search = $(this).val(); break;
		}
		
	});
	
	// create and insert shorcode and close ThickBox
	$(document).on('click','#TB_ajaxContent .insert-map',function(){
		if(locations && center_map_location){
			var shortcode = '[cartographer';
			if(zoom != zoom_default){
				shortcode += ' zoom="' + zoom + '"';
			}
			if(width != width_default || width_unit != width_unit_default){
				shortcode += ' width="' + width + width_unit + '"';
			}
			if(height != height_default || height_unit != height_unit_default){
				shortcode += ' height="' + height + height_unit + '"';
			}
			if(show_info_bubbles != show_info_bubbles_default){
				shortcode += ' show_info_bubbles="' + show_info_bubbles + '"';
			}
			if(map_alignment != map_alignment_default){
				shortcode += ' map_alignment="' + map_alignment + '"';
			}
			if(disable_ui != disable_ui_default){
				shortcode += ' disable_ui="' + disable_ui + '"';
			}
			if(include_list != include_list_default){
				shortcode += ' include_list="' + include_list + '"';	
			}
			if(pin_style != pin_style_default){
				shortcode += ' pin_style="' + pin_style + '"';
			}
			if(map_type != map_type_default){
				shortcode += ' map_type="' + map_type + '"';
			}
			if(include_search != include_search_default){
				shortcode += ' include_search="' + include_search + '"';
			}
			shortcode += ' locations="' + locations + '"';
			shortcode += ' center_map_location="' + center_map_location + '"';
			shortcode += ']';
			if($('.ctgfr-widget').length > 0){
				$($trigger).siblings('.shortcode').val(shortcode);
			}else{
				window.send_to_editor(shortcode);
			}
			$('#TB_closeWindowButton').trigger('click');
		}else{
			alert('Please select at least 1 location to map AND a location to center the map on');
		}
	});
});