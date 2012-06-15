jQuery(document).ready(function(){
	jQuery('.oglinkbox .oghead').click(function() {
		jQuery(this).next().toggle('slow');
		return false;
	}).next().hide();
});