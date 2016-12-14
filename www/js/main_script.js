$(document).ready(function() {
	if ( (typeof toy_id != 'undefined') && (toy_id) )
	{
		checkAvailableToy(toy_id);
	}

	my_joke();

	if ( (typeof is_list != 'undefined') && (is_list == 1) ) {
		update_price_list();
	}
});

function my_joke()
{
	setTimeout(function() {
		var logo_a = $('div.logo img');
		var top_a = $('<img>').attr('src','/images/top_ajax.gif').css('position','absolute');
		var top_c = $('<img>').attr('src','/images/center_ajax.gif').css('position','absolute');
		var top_b = $('<img>').attr('src','/images/bottom_ajax.gif').css('position','absolute');

		//var p_left = logo_a.position().left;
		//var p_top = logo_a.position().top;
		var o_left = logo_a.offset().left;
		var o_top = logo_a.offset().top;

		top_a.appendTo('body');
		top_a.css('left',o_left+'px').css('top',o_top+'px');

		top_c.appendTo('body');
		top_c.css('left',o_left+1+'px').css('top',o_top+29+'px');

		top_b.appendTo('body');
		top_b.css('left',o_left+'px').css('top',o_top+59+'px');

		logo_a.attr('src','/images/logo_ajax.gif');

		top_c.animate({
			top: '+=30'
		},'slow');

		top_a.animate({
			top: '+=40'
		},'slow');

		top_b.animate({
			left: '+=14',
			top: '-=38'
		},'slow');
	},10000);
}

function checkAvailableToy(toy_id)
{
	/*
	$.get(domain_path+'/services/Checkavailable/index/id/'+toy_id,
	function(res)
	{
		var img = $('#img_available');
		if (res == 0) {
			img.attr("src",domain_path+"/images/cancel.png");
			img.attr("title","Нет в наличии");
			$("#btApply").attr("disabled",true);
		} else {
			img.attr("src",domain_path+"/images/clean.png");
			img.attr("title","Есть в наличии");
			$("#btApply").attr("disabled",false);
			$("#btApply").removeClass("b_button_disabled")
		}
	});
	*/
	$.getJSON(domain_path+'/services/Checkavailable/index/id/'+toy_id,function(data) {
		if (data['type'] == 2) {
			if (typeof data['sale_cost'] != 'undefined') {
				//$('.tovar_price_detail').html(data['sale_cost']+' руб.');
				$('.tovar_price_detail').html('<span itemprop="price">'+data['sale_cost']+'</span> руб.');
				$('<div>').html('(скидка: '+data['sale_percent']+'% на '+data['sale_limit']+' шт.)').css('font-weight','bold').css('margin-bottom','5px').insertAfter('.tovar_price_detail');
				$('<div>').html('Старая цена: <strike>'+data['cost']+' руб.</strike>').insertAfter('.tovar_price_detail');
			} else if (typeof data['cost'] != 'undefined') {
				//$('.tovar_price_detail').html(data['cost']+' руб.');
				$('.tovar_price_detail').html('<span itemprop="price">'+data['cost']+'</span> руб.');
			}

			if (typeof data['time_text_a'] != 'undefined') {
				$('<div>').html(data['time_text_a']).css('margin-bottom','10px').css('font-weight','bold').insertAfter('.tovar_available').hide().delay(2000).slideDown('slow');
				$('<div>').html('Ориентировочная дата отгрузки:').insertAfter('.tovar_available').hide().delay(2000).slideDown('slow');
			}
		}

		var img = $('#img_available');
		if (data['availability_code'] == 0) {
			img.attr("src",domain_path+"/images/cancel.png");
			img.attr("title","Нет в наличии");
			$("#btApply").attr("disabled",true);
			my_show_tooltip(img);
		} else {
			img.attr("src",domain_path+"/images/clean.png");
			img.attr("title","Есть в наличии");
			$("#btApply").attr("disabled",false);
			$("#btApply").removeClass("b_button_disabled");
			$("#btApply").parent().removeClass("b_button_disabled");
		}
	});
}

var ar_flg = new Array();

function onload_iframe(ifrm_cur,toy_id)
{
	//document.getElementById('ifrm_'+toy_id).onload='';
	//alert(document.getElementById('ifrm_'+toy_id).onload);exit;
	//ifrm_cur.onload = function() {alert(1);}
	//ifrm_cur.onload = '';
	if (ar_flg[toy_id] == 0) {
		document.getElementById('ifrm_'+toy_id).contentDocument.getElementById('frm_'+toy_id).submit();
	}
	//ifrm_cur.contentDocument.getElementById('frm_'+toy_id).submit();
	ar_flg[toy_id] = 1;
}

function post_click(toy_id)
{
	$.post(domain_path+'/services/Clicks/save',{
		'url' : window.location.href,
		'toy_id' : toy_id
	});
}

function try_buy(toy_id)
{
	post_click(toy_id);

	var st_bt_buy = $('#'+toy_id+'_bt_buy');
	var st_div = $('#'+toy_id+'_st');
	st_div.html('<img src="'+domain_path+'/images/loading.gif" class="img_st_in_list">');
	//+Math.ceil(Math.random()*1000000)+

	st_bt_buy.attr('disabled','disabled');
	st_bt_buy.addClass('b_button_disabled');
	st_bt_buy.parent().addClass('b_button_disabled');

	//$.get(domain_path+'/services/Checkavailable/index/id/'+toy_id+'?nocache='+Math.ceil(Math.random()*1000000),function(res) {
	$.getJSON(domain_path+'/services/Checkavailable/index/id/'+toy_id+'?nocache='+Math.ceil(Math.random()*1000000),function(data) {
		//res = 0;

		if (data['availability_code'] == 0) {
			st_div.html('<img id="img_av_'+toy_id+'" src="'+domain_path+'/images/cancel.png" title="Нет в наличии" class="img_st_in_list">');
			my_show_tooltip($("#img_av_"+toy_id));
		} else {
			ar_flg[toy_id] = 0;
			ifrm = $('<iframe id="ifrm_'+toy_id+'" src="'+domain_path+'/services/Checkavailable/try-buy/id/'+toy_id+'" onload="onload_iframe(this,'+toy_id+');" style="display:none;"></iframe>');
			$('body').append(ifrm);

			st_div.html('<a href="'+domain_path+'/shop/basket"><img src="'+domain_path+'/images/basket.png" title="Добавлен в корзину" class="img_st_in_list"></a>');
			setTimeout(function(){
				$('#myShopOnelineCartIframe').attr('src',$('#myShopOnelineCartIframe').attr('src'));
			},1000);
		}

		/*
		if (res == 0) {
			st_div.html('<img src="'+domain_path+'/images/cancel.png" title="Нет в наличии" class="img_st_in_list">');
		} else {
			var frm = $('<form method=POST action="http://p.my-shop.ru/order" style="display:none;">');
			frm.html('<input name="action" value="cartAddItem">');
			frm.append('<input name="partner" value="4336">');
			frm.append('<input name="quantity" value="1">');
			frm.append('<input name="item" value="'+toy_id+'">');
			$('body').append(frm);
			frm.submit();
		}
		*/

		st_bt_buy.removeClass("b_button_disabled");
		st_bt_buy.parent().removeClass("b_button_disabled");
		st_bt_buy.removeAttr('disabled');
		//$.post('http://p.my-shop.ru/order',{action : "cartAddItem",partner : "4336",quantity : "1",item : toy_id});
	});
	//alert(toy_id);
}


function try_put(toy_id)
{
	//post_click(toy_id);
	//return false;exit;

	var st_bt_put = $('#'+toy_id+'_bt_put');
	var st_div = $('#'+toy_id+'_put_st');
	st_div.html('<img src="'+domain_path+'/images/loading.gif" class="img_st_in_list">');

	$.get(domain_path+'/Put/put-toy/toy_id/'+toy_id+'?nocache='+Math.ceil(Math.random()*1000000),
	function(res)
	{
		//res = 0;

		if (res == 0) {
			st_div.html('<img id="img_av_'+toy_id+'" src="'+domain_path+'/images/cancel.png" title="Не удалось отложить" class="img_st_in_list">');
			my_show_tooltip($("#img_av_"+toy_id));
		} else {
			st_div.html('<a href="'+domain_path+'/put/list"><img src="'+domain_path+'/images/put.png" title="Все отложенные" class="img_st_in_list"></a>');
			//st_bt_put.css('visibility','hidden');
			//st_bt_put.remove();
			st_bt_put.html('<span class="put_text_disabled">Отложено</span>');
			$("#li_put").css('display','');
		}
	});

	return true;
}

function test_all_put()
{
	$.each($('.tovar_box'),function(index,item) {
		$.each($(item).find('input'),function (ind,it) {
			if (it && it.type == 'checkbox') {
				if (it.checked) {
					$(item).addClass('checked_tovar');
				} else {
					$(item).removeClass('checked_tovar');
				}
			}
		});
	});

	return true;
}

function check_all_put(is_ch)
{
	//return false;exit;
	$.each($('.tovar_box'),function(index,item) {
		$.each($(item).find('input'),function (ind,it) {
			if (it && it.type == 'checkbox') {
				if (is_ch) {
					it.checked = true;
					$(item).addClass('checked_tovar');
				} else {
					it.checked = false;
					$(item).removeClass('checked_tovar');
				}
			}
		});
	});

	return true;
}

function check_one_put(it)
{
	if (it.checked) {
		$(it).parents('.tovar_box').addClass('checked_tovar');
	} else {
		$(it).parents('.tovar_box').removeClass('checked_tovar');
	}
}

function try_buy_from_put()
{//Math.floor(Math.random()*201)+
	if ( (typeof ar_to_basket != 'undefined') && (ar_to_basket) && (ar_to_basket.length > 0) ) {
		for (var i=0;i<ar_to_basket.length;i++) {
			setTimeout('try_buy('+ar_to_basket[i]+')',600+1500*i);
		}
	}
}

function uncheck_filters()
{
	$.each($('.add_filters'),function(index,item) {
		$.each($(item).find('input'),function (ind,it) {
			if (it && it.type == 'checkbox') {
				it.checked = false;
			}
		});
	});

	return true;
}

function update_price_list()
{
	var s='';
	$.each($('.tovar_price'),function(key,val) {
		var ar = $(val).attr('id').split('_');
		s += ar[2]+',';
	});

	if (s != '') {
		s = s.substr(0,s.length-1);

		$.getJSON(domain_path+'/services/Checkavailable/getlist/ids/'+s,function(data) {
			if (data != 'err') {
				$.each(data,function(key,val) {
					$('#lt_price_'+val['id']).html(val['cost']+' руб.');
				});
			}
		});
	}
}

function my_show_tooltip(my_target)
{
	//$("#img_available")
	my_target.qtip({
		content: "Нет в наличии",
		position: {
			corner: {
				tooltip: 'bottomMiddle',
				target: 'topMiddle'
			}
		},
		show: {
		/*
			when: {
				target: $("#img_available"),
				event: 'mouseover'
			},*/
			ready: true
		},
		hide: {
			delay: 3000
		},
		style: {
			border: {
				width: 3,
				radius: 3
			},
			padding: 10,
			textAlign: 'center',
			tip: true,
			name: 'cream'
		},
		api: {
			onRender: function() {
				setTimeout(this.hide, 5000); /// hide after a second
			}
		}
	});
}