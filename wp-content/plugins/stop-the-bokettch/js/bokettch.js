jQuery(function($) {
	var original_post_status = $('#original_post_status').val();

	$("#publish").click(function(){
		var private = $('#visibility-radio-private').attr( 'checked' );
		var date = new Date;
		var cur_y = date.getFullYear();
		var cur_m = ( date.getMonth() + 1 < 10 ) ? '0' + ( date.getMonth() + 1 ) : date.getMonth() + 1;

		var cur_d = date.getDate();
		var cur_h = ( date.getHours()   < 10 ) ? '0' + date.getHours()   : date.getHours();
		var cur_i = ( date.getMinutes()   < 10 ) ? '0' + date.getMinutes()   : date.getMinutes();
		var aa = $("#aa").val();
		var mm = $("#mm").val();
		var jj = $("#jj").val();
		var hh = $("#hh").val();
		var mn = $("#mn").val();

		var cur_date = cur_y + '-' + cur_m + '-' + cur_d + ' ' + cur_h + ':' + cur_i;
		var pub_date = aa + '-' + mm + '-' + jj + ' ' + hh + ':' + mn;

		if ( original_post_status != 'publish'
			&& private != 'checked'
			&& cur_date >= pub_date
		) {
			if(!window.confirm(bokettch.confilm)){
				return false;
			}
		}
	});
});