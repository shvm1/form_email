function add_input_file(el)
{
	$(el).before('<input type="file" name="add_files[]">');
}

function send_letter(btn)
{
	var form = $('form#form_send_mail');
	var to = $('input[name=email_to]', form);
	
	if(!to.val() || !isValidEmail(to.val()))
	{
		to.focus().parent().addClass('has-error');
		return false;
	}
	$(btn).button('loading');
	form.submit();
}

function success_request()
{
	if($('input[type=file]').length > 1) $('input[type=file]').slice(1).remove();
	$('form#form_send_mail').trigger('reset');
	$('#btn_send').button('reset');
	alert('Ваше сообщение отправлено!');
}

function fail_request(error)
{
	$('#btn_send').button('reset');
	alert(error);
}

function isValidEmail(email) {
    var pattern = new RegExp(/.+@.+\..+/i);
    return pattern.test(email);
    }

jQuery(function($) {
  
});