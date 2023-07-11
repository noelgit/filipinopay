<?php
	include('pages/requires/header.php');
?>
<div class="gap gap-md"></div>
<div class="container customContainer">
	<div class="row m0">
		<div class="col-sm-8 p0 items">  
 			<ul class="productList p0 mt30 ">
				<li class="customRow mt20">
					<div class="col-sm-3 p0">
						<a class="addToCart" product-id="2" product-name="ASUS Zenbook Pro 14" product-price="1620.00">
							<img src="<?php echo IMG; ?>zenbookpro.png" class="icon full-width img-responsive productIMG">
						</a>
					</div>
					<div class="col-sm-7 text-justify">
						<h4>ASUS Zenbook Pro 14</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<div class="col-sm-2 text-right">
						<h5>&#8369;1620.00</h5>
					</div>
				</li>

				<li class="customRow mt20">
					<div class="col-sm-3 p0">
						<a class="addToCart" product-id="2" product-name="HP Spectre x360 13" product-price="350.00">
							<img src="<?php echo IMG; ?>spectrex360.png" class="icon full-width img-responsive productIMG">
						</a>
					</div>
					<div class="col-sm-7 text-justify">
						<h4>HP Spectre x360 13</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<div class="col-sm-2 text-right">
						<h5>&#8369;350.00</h5>
					</div>
				</li>

				<li class="customRow mt20">
					<div class="col-sm-3 p0">
						<a class="addToCart" product-id="3" product-name="Dell XPS 15" product-price="2000.00">
							<img src="<?php echo IMG; ?>dellxps15.png" class="icon full-width img-responsive productIMG">
						</a>
					</div>
					<div class="col-sm-7 text-justify">
						<h4>Dell XPS 15</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<div class="col-sm-2 text-right">
						<h5>&#8369;2000.00</h5>
					</div>
				</li>
			</ul>

		</div>

		<div class="col-sm-4 mt30 paymentSummary">  
			<h3 class="mt10" id="cartNumber">My Cart(0)</h3>
			<form method="post" id="frmCart">
				<table class="table cart" id="cart">
				</table>
				<table class="table"> 
					<tr>
						<th class="text-left">Total Amount:</th>
						<th class="text-right" id="totalAmount">&#8369;0.00</th>
					</tr>
					<tr>
						<td>Name:*</td>
						<td><input type="text" class="form-control" name="name" required></td>
					</tr>
					<tr>
						<td>Email:*</td>
						<td><input type="text" class="form-control" name="email" required></td>
					</tr>
					<tr>
						<td>Contact No.:*</td>
						<td><input type="text" class="form-control" name="contact"></td>
					</tr>
				</table>
				<button type="submit" class="btn full-width btnProceed">Proceed</button>
			</form>
		</div>
	</div>
</div>




<?php
	include('pages/requires/resources.php');
?>

<script>
	$('.items').flyto({
	  item      : 'li',
	  target    : '.cart',
	  button    : '.addToCart',
	  shake 	: true
	}); 
	$('.addToCart').click(function (){
		var productId = $(this).attr('product-id');
		var productName = $(this).attr('product-name');
		var productPrice = $(this).attr('product-price');

		var cartNumber = 0;
		$(".productPrice").each(function() { 
			cartNumber += 1;
		});
		var cartData = '';
		cartData += '<tr>';
		cartData += '<td class="text-left">'+productName;
		cartData += '<input type="hidden" name="transaction['+cartNumber+'][sub_merchant_code]" value="<?php echo IPG_SUB_MERCHANT_CODE; ?>">';
		cartData += '<input type="hidden" name="transaction['+cartNumber+'][transaction_payment_for]" value="'+productName+'"></td>';
		cartData += '<td class="text-right productPrice" data-price="'+productPrice+'">'+numberWithCommas(productPrice);
		cartData += '<input type="hidden" name="transaction['+cartNumber+'][transaction_amount]" value="'+productPrice+'"></td>';
		cartData += '</tr>';
		$( "#cart").append(cartData);
		totalAmount();

	});

	function totalAmount(){ 
		var total = 0;
		var cartNumber = 0;
		$(".productPrice").each(function() { 
			total += parseFloat($(this).data('price')); 
			cartNumber += 1
		}); 
		$("#cartNumber").text("My Cart ("+cartNumber+")");
		$("#totalAmount").text(numberWithCommas(parseFloat(total).toFixed(2)));
		
	}

	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	$('#frmCart').submit( function (){
		var fd = new FormData(document.getElementById("frmCart"));
		$.ajax({
			url: BASE_URL+"pages/php/getIPGLink.php",
			type:"POST",
			data: fd, 
			cache: false, 
         	processData: false, // tell jQuery not to process the data
     		contentType: false, // tell jQuery not to set contentType
			success: function(result) {    
	 			if(result['status_code'] == '200'){ 
	 				location.href = result['redirection_link'];
	 			}else{ 
	 				alert(JSON.stringify(result));
	 				//alert(result.join("\n"));
	 				//alert(result);
	 			}
			},
			error: function() {
				return false; 
			}
		});
		return false; 
	});
</script>