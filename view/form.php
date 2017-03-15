<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
</head>
<body>
<div class="container_fluid">
	<div class="well well-lg"><h1>Форма отправки писем</h1></div>
</div>
<div class="container">
		<iframe id="downFrame" name="downFrame" style="display:none;"></iframe>
		<form id = "form_send_mail" method="POST" target="downFrame"  enctype="multipart/form-data">
		  <div class="form-group">
			<label>Кому</label>
			<input type="email" class="form-control" name="email_to" placeholder="Введите email получателя">
		  </div>
		  <div class="form-group">
			<label>Тема</label>
			<input type="text" class="form-control" name="subject"  placeholder="Введите тему письма">
		  </div>
		  <div class="form-group">
			<label>От</label>
			<div class="row">
				
				<div class="col-md-6">
					<div class="input-group">
					  <span class="input-group-addon">Email</span>
					  <input type="email" name="email_from" class="form-control" placeholder="Введите Ваш email">
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
					  <span class="input-group-addon">Имя</span>
					  <input type="text" name="name_from" class="form-control" placeholder="Введите Ваше имя">
					</div>
				</div>
			</div>
			<div class="help-block">Введите свое имя и email, чтобы получатель мог ответить Вам</div>
		  </div>
		  <div class="form-group">
			<textarea name="message" class="form-control" rows="15"></textarea>
			
		  </div>
		  
		  <div class="form-group">
			<label>Прикрепить файлы</label>
			<input type="file" name="add_files[]">
			<div class="btn btn-default btn-xs" onclick="add_input_file(this);">Добавить файл</div>
		  </div>
		 
		  
		</form>
	
		<button id="btn_send" class="btn btn-primary" onclick="send_letter(this);" data-loading-text="Идет отправка"><i class="glyphicon glyphicon-send"></i> Отправить письмо</button>
		
		<script type="text/javascript">
		tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,|,bullist,numlist,|,forecolor,backcolor",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left"
		
		}); 
		

	</script>
</div>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>