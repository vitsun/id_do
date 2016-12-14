$(document).ready(function() {
	if ( (typeof toy_id != 'undefined') && (toy_id) )
	{
		show_comments_form();
	}
});

function show_comments_form()
{
	$.get(domain_path+'/services/comments/get_form/id/'+toy_id+'?nocache='+Math.ceil(Math.random()*1000000),
	function (res) {
		$("#comments_form").html(res);
		$("#b_submit").click(function() {send_comment();});
	});
}

function send_comment()
{
	var f_form = $("#comm_form");
	var comm_name = f_form.find('input[name=comm_name]').val();
	var comm_text = f_form.find('textarea[name=comm_text]').val();
	if ( ($.trim(comm_name) != '') && ($.trim(comm_text) != '') ) {
		$.post(domain_path+'/services/comments/send_form', {'toy_id' : toy_id, 'comm_name' : comm_name, 'comm_text' : comm_text}, function (res) {
			if (res == 0) {
				alert('Ошибка при добавлении отзыва');
			} else {
				$("div[class=comm_div]").css('display','');
				$("<div>").insertAfter("div[class=comm_title]").addClass('comm_one').html(res);
				//f_form.find('input[name=comm_name]').val('');
				f_form.find('textarea[name=comm_text]').val('');
			}
		});
	} else {
		alert('Не заполнено одно из полей');
	}
}