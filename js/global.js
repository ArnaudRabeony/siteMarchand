$(document).ready(function()
{
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

});