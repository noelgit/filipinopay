function preloader(switch_val){
	if(switch_val == '1'){
		$('#preloader').show();
		
	}else{
		 
		$('#preloader').fadeOut(600);
	}
}   
 
$(".updateCheckBoxText").on("click", function(){
	var updateCheckBox = $("#updateCheckBox").prop('checked');
	if(updateCheckBox == true){
		$("#updateCheckBox").prop('checked', false);	
	}else{
		$("#updateCheckBox").prop('checked', true);
	} 
});
 
$(".paymentCheckBoxText").on("click", function(){
	var paymentCheckBox = $("#paymentCheckBox").prop('checked');
	if(paymentCheckBox == true){
		$("#paymentCheckBox").prop('checked', false);	
	}else{
		$("#paymentCheckBox").prop('checked', true);
		$('#paymentCheckBox').popover('hide');   
	} 
});

$.fn.digits = function(){ 
    return this.each(function(){ 
        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
    })
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}


$(window).ready(function(){ 
	preloader(0);
});
