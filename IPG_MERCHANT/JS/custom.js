function preloader(switch_val){
	if(switch_val == '1'){
		$('#preloader').show();
	}else{		 
		$('#preloader').fadeOut(600);
	}
}   
 
 
$(window).ready(function(){ 
	preloader(0);
});
