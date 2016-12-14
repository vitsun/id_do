var tmb_iwidth = 100; // current
var tmb_iheight = 100; // current

$(document).ready(function() {
	if ( (typeof toy_id != 'undefined') && (toy_id) ) {
		get_other_goods_pict(toy_id);
		//alert(1);
	}
});

function get_other_goods_pict(toy_id)
{
	$.getJSON(domain_path+'/services/othergoods/getother/id/'+toy_id, function(data) {
		var items = [];
		var descr = 'Похожие товары';

		$.each(data, function(key, val) {
		  items.push('<li class="good_addon_li_pict"><div class="good_addon_img_pict"><a href="'+val['url']+'"><img id="pre_' + val['id'] +'" src='+domain_path+'/images/preloader.png border=0><img id="add_img_' + val['id'] +'" style="border:0px;display: none;" wwidth="80" src="' + (val['picture'] ? val['picture'] : domain_path+'/images/no_photo.png') + '" title="' + val['name'] + '" onload="cor_w_h($(this));"></a></div><div class="good_addon_name_pict"><a href="'+val['url']+'">'+ val['name'] +'</a></div><div class="good_addon_price_pict">'+ val['price'] + ' руб.</div></li>');
		});

		if (items.length > 0) {
			ul = $('<ul/>', {
				'class': 'my-new-list',
				html: items.join('')
			});

			$('#good_addon').append('<div class="analog_title">'+descr+'</div>').append(ul);
			$('<a>').attr('href','#').html('Развернуть').insertAfter('#good_addon').addClass('open_title').click(
				function () {
					if ($(this).html() == 'Развернуть') {
						$('#good_addon').addClass('good_addon_o');
						$('#otsh').hide();
						$(this).html('Свернуть');
					} else {
						$('#good_addon').removeClass('good_addon_o');
						$('#otsh').show();
						$(this).html('Развернуть');
					}
					return false;
				});
			$('<div>').attr('id','otsh').addClass('open_title_shadow').insertAfter('#good_addon');
		}
	});
}

function cor_w_h(tmb_img)
{
	var tmb_w = tmb_img.width();
	var tmb_h = tmb_img.height();
	if (tmb_w !=0 && tmb_h !=0) {
		if (tmb_w > tmb_h) {
			var new_h = tmb_iwidth*tmb_h/tmb_w;
			var new_w = tmb_iwidth;
		} else {
			var new_h = tmb_iheight;
			var new_w = tmb_iheight*tmb_w/tmb_h;
		}

		tmb_img.css('width',new_w+'px');
		tmb_img.css('height',new_h+'px');
	}

	//alert(tmb_img.attr('id').split('_')[2]);
	ar = tmb_img.attr('id').split('_');
	$("#pre_" + ar[2]).hide();//.css('display','none');
	tmb_img.css('display','');

	var div_price = tmb_img.parent().parent().parent().find("div.good_addon_price");
	div_price.css('margin-top',((new_h-div_price.height())/2)+'px');
	//tmb_img.parent().parent().css('color','green');
}