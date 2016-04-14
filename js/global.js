$(document).ready(function()
{
	$(window).scroll(function()
	{
		// console.log($(window).scrollTop());
		// if ($(window).scrollTop() > 200)
		// {	
		// 	console.log("opacity--");
		// 	$('#top').fadeIn();
		// 	$(".navbar").animate({opacity: 0.7}, 'fast');
		// }
		// else
		// {	
		// 	console.log("opacity++");
		// 	$(".navbar").animate({opacity: 1}, 'fast');
		// 	$('#top').fadeOut();
		// }
	});

	$("#updateProductButton").click(function(e)
	{
		e.preventDefault();
		$.get("ajax/filteredProducts", 
			{
				nb:"ok"	
			});
	});

	$('#top').click(function() 
	{ 
		console.log('click fleche');
		$('html,body').animate({scrollTop: 0}, 'slow');
	});

	$("#importFromCsv").click(function()
	{
		// e.preventDefault();
		$("#productsFileChooser").click();
	});

	$('.client-filters').change(function()
	{
		$(".client-side").html("");
		$.get("ajax/filteredProducts", 
			{
				nb:"ok"	
			});
	});

	$('.curtain').mouseover(function()
		{
			$(this).find("img").css({ opacity: 0.4 });
			$(this).find("p").show();
		});

	$('.curtain').mouseout(function()
		{
			$(this).find("img").css({ opacity: 1 });
			$(this).find("p").hide();
		});


	// $('#createAccoundContent form').validator();
	// $('#continueShopping').click(function()
	// {
	// 	$("#shoppingCart").show();
	// })

	$(".thumbnail").mouseenter(function()
	{	
		var isConnected = $(".status").attr("data-status") == "connected" ? true : false;
		
		if(isConnected)
			$(this).find(".toCartThumbnail").show();
	});

	$(".thumbnail").mouseleave(function()
	{
		$(this).find(".toCartThumbnail").hide();
	});


	$(".toCartThumbnail,#addToBasket").click(function()
	{
			$("#basketNotification").show();
			$("#basketNotification a").show();

			if($('#basketSize')!=0)
				$('#basketSize').show();
	});

	$('.toCartThumbnail').click(function()
	{
		$(this).parent().hide();
		$(this).parent().parent().find("#thumbnailOrder").show();
	});

	$("body").on("click","#addToBasket,#thumbnailOrderButton",function(e)
	{

		var productNode= $(e.target).is('#thumbnailOrderButton') ? $(this).parent().parent().parent() : $("#displayProductContainer");
		var productId=productNode.attr("data-productid");

		var sizeNode = $(e.target).is('#thumbnailOrderButton') ? $(this).parent().find("#selectedSize") : $(this).parent().find("#sizeSelector");
		var qtyNode = $(e.target).is('#thumbnailOrderButton') ? $(this).parent().find("#selectedQty") : $(this).parent().find("#quantitySelector");

		var size = sizeNode.val();
		var qty = qtyNode.val();

		sizeNode.toggleClass("necessary");
		qtyNode.toggleClass("necessary");

		if(size!="notSelected" && qty!="notSelected")
		{
			$.get("js/ajax/updateBasket.php",
			{
				id:productId,
				size:size,
				qty:qty
			},function(response)
			{
				$("#basketSize").text(response);
			});

			if($(e.target).is('#thumbnailOrderButton'))
			{
				$(this).parent().parent().prev().show();
				$(this).parent().parent().hide();
			}
			else if($(e.target).is('#addToBasket'))
			{
				// alert("ok");
				var status=$("#displayProduct").attr("data-status");

				if(status=="connected")
					$('#modalButtons').html('<button id="continueShopping" type="button" class="btn btn-default" data-dismiss="modal">Je poursuis mes achats</button><button type="button" class="btn btn-primary"><a href="index.php?page=view/myBasket" id="toBasket" style="text-decoration:none;color:white;">Voir mon panier</a></button>');
				else
				{
					$("#modalBody").css("height","200px");
					var coForm='<form action="index.php?page=controller/check_co" method="post" class="col-md-5 col-sm-3" novalidate>'
				    +'<input type="email" class="form-control" name="email" id="email" placeholder="exemple@mail.com">'
				    +'<input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe">'	
				   	+'<button class="btn btn-default" type="submit">Connexion</button></form>';

					$("#modalBody").html("Vous devez être connecter pour ajouter cet article à votre panier."+'<div style="margin-left:180px;margin-top:30px;">'+coForm+"</div>");
				}

				$("#myModal").modal();
			}
		}
		else
		{			
			sizeNode.addClass("necessary");
			qtyNode.addClass("necessary");
		}
	});

	$("#selectedSize,#sizeSelector").change(function(e)
	{
		var size=$(this).val();
		var productId = $(e.target).is('#selectedSize') ? $(this).parent().parent().parent().parent().attr("data-productid") : $(this).parent().parent().parent().parent().parent().attr("data-productid")
		
		$.get("js/ajax/stockItems.php",
		{
			id:productId,
			size:size
		},function(response)
		{
			console.log(response);
			if($(e.target).is('#selectedSize'))
				$(e.target).parent().next().find("#selectedQty").html(response);
			else
				$(e.target).next().html(response);
		});
	})

	$("#multipleDeletionButton").click(function()
	{
		$("#customersTable .productLine").each(function()
		{
			var checkbox=$(this).find(".deleteCheckbox");
			var isChecked = checkbox.prop("checked");
			var id=checkbox.val().replace("delete","");
				// console.log("delete product : "+ id );	

			if(isChecked)
				$(this).find(".deleteButton").click();
		});
	});

	$(".removeFromBasketButton").click(function()
	{
		$(this).parent().parent().hide();

		var productId=$(this).parent().parent().attr("data-productid");
		// console.log(productId);

		$.get("js/ajax/removeFromBasket.php",
		{
			id:productId,
			size:$(this).parent().parent().find(".chosenSize").text(),
			qty:$(this).parent().parent().find(".chosenQty").text()
		},function(response)
		{
			var price = response.price;
			var nb = response.nb;
			// var price = response['price'];

			console.log(price);
			$("#basketSize").text(nb);

			if(nb==0)
			{	
				// alert("vide");
				$('#notEmptyBasket').hide();
				$('#emptyBasket').show();
				$('#basketSize').hide();
			}

			$('#totalPrice b').text("Total : "+price+" €");
		},"json");
	});

	// $('body').on('change',"#selectCategory",function()
	// {
	// 	// alert("ok");
	// 	$('#sizeRow').each(function()
	// 	{
	// 		console.log($(this).html());
	// 	});
	// });
});