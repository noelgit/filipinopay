function preloader(switch_val){
	if(switch_val == '1'){
		$('#preloader').show();
		
	}else{
		 
		$('#preloader').fadeOut(600);
	}
}   

//---------------------------
//Terms and Conditions Button   
//---------------------------
$("#TaCScrollContent").on('scroll', chk_scroll);

function chk_scroll(e) {
    var elem = $(e.currentTarget);
    if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
       	$(".TaCScrollBTN").fadeOut();
    }
}

$(".TaCScrollBTN").click(function() {
    $('#TaCScrollContent').animate({
        scrollTop: $("#btnAgreeTAC").offset().top
    }, 2000);
});

$("#btnAgreeTAC").click( function(){
   	$.ajax({
      	url: BASE_URL+"PAGES/PHP/TERMS-AND-CONDITIONS/session.php",
		success: function(result) {   
			if(result){
				window.location.replace(BASE_URL+"PAYMENT-HOME-PAGE");
			}else{
				//window.location.replace(BASE_URL+"PAYMENT-HOME-PAGE");
				alert("Something went wrong, please call the administrator.");
			}
		},
  	}); 
});

//---------------------------
//Payment Options Link
//---------------------------
$(".optionsLink").on("click", function(){
	$(this).find( ".paymentOptionsCheck" ).prop('checked', 'checked');
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


$(window).ready(function(){ 
	preloader(0);
});
