<?require_once("$_SERVER[DOCUMENT_ROOT]/../db/dal.inc.php");?>
<?php define(CLASS_SUCCESS, "is-valid");
	define(CLASS_ERROR, "is-invalid");
	//Обработчик нажатия на кнопку "Сохранить" в модальном окне #action
	if(isset($_POST["add"])) {
		// Получение значений из элементов управления
		$f_title=mysqli_real_escape_string($cms_db_link,$_POST["f_title"]);
		$f_content=mysqli_real_escape_string($cms_db_link,$_POST["f_content"]);
		if(isset($_POST["f_ID"]))
			$f_ID=(int)$_POST["f_ID"];
		//if($f_ID == "")
    	//	echo("Добавление");
		//else echo("Редактирование");
		$isvalid=Array(
			"f_title"=>CLASS_SUCCESS,
			"f_content"=>CLASS_SUCCESS
		);
		// Проверка заполнения поля "Заголовок"
		if (trim($f_title)=="")
		{
			$isvalid["f_title"]=CLASS_ERROR;
		}
		// Проверка заполнения поля "Текст объявления"
		if (trim($f_content)=="")
		{
			$isvalid["f_content"]=CLASS_ERROR;
		}
		if (!in_array("is-invalid", $isvalid))
		{
			$errmsg="";
			try {
				if($f_ID == "")
					DBCreateAdvertis($f_title,$f_title,$f_content);// $f_title - ID пользоывтеля указан статически
				else
					DBUpdateAdvertis($f_ID,$f_title,$f_content);
			header("Location:$_SERVER[PHP_SELF]?success");
			}catch(Exception $ex){
				$errmsg=$ex->getMessage();
			}
		}
	}//add

	if(isset($_POST["#action"])) {
		$f_name=mysqli_real_escape_string($cms_db_link,$_POST["f_name"]);
		$f_price=(double)$_POST["f_price"];
		$f_quantity=(int)$_POST["f_quantity"];
		$f_year=(int)$_POST["f_year"];
		

		$errmsg="";
		try {
			
			DBCreateTovar($f_name,$f_price,$f_quantity, $f_year, $f_country, $f_description, $id, $f_req, $f_ram);
			
			//Редирект (перенаправление) для предотвращения дублирования
			//информации в БД
			header("Location: $_SERVER[PHP_SELF]?success");
		}catch(Exception $ex){
			$errmsg=$ex->getMessage();
		}
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" /> -->
		<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
		<!-- <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>		 -->
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" />
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->

		<!-- Наша собственная библиотека my_input_validator-->
		<script src="my_input_validator.js"></script>
	  
		<script type="text/javascript">
			//Объектный подход	
			$(function() {
			  // Регулярные выражения для проверки введённых значений
			  var reg_pCena = /^[0-9]+(\.[0-9]{2})?$/;
			  var reg_pKol = /^[0-9]+$/;
			  var reg_pGod = /^[0-9]+$/;
			  var reg_pProc = /^[0-9]+$/;
			  var reg_pPamyat = /^[0-9]+$/;
			  
			  //Создание экземпляра объекта формы
			  var my_form = new InputForm("#user_form");
			  
			  //Создание экземпляров объекта полей ввода
			  var f_name = new InputField("#f_name",my_form);
			  var f_price = new InputField("#f_price",my_form);
			  var f_quantity = new InputField("#f_quantity",my_form);
			  var f_year = new InputField("#f_year",my_form);

			  //Подписка на callback-функцию beforesubmit, объявленную в 
			  //классе InputForm, и вызываемую при попытке отправки формы
			  my_form.beforesubmit = function() {
				  //Собственно процесс валидации				
				  f_name.validate(f_name.v()=="");
				  f_price.validate(!reg_pCena.test(f_price.v()));
				  f_quantity.validate(!reg_pKol.test(f_quantity.v()));
				  f_year.validate(!reg_pGod.test(f_year.v()));

			  }	
		  });
		  $(function() {
				var dataTable = $('#advertis_data').DataTable({
						"language": {"url":"http://cdn.datatables.net/plug-ins/1.10.20/i18n/Russian.json"},
						"processing":true,
						"serverSide":true,
						"order":[],
						"ajax":{
							url:"/rest/advertise",
							type:"POST"
						},
						"columnDefs":[
							{
								"targets":[2, 3], // Столбцы, по которым не нужна сортировка
								"orderable":false,
							},
						],
				});	

				dataTable.ajax.reload();
				
				$(document).on('submit', '#user_form', function(event){
					event.preventDefault();					
					
					//data.Nazvanie (получить значение селектора)
					  var advertise_info = {
						"ID_user":$("#f_ID").val(),
					  	"Title":$("#f_title").val(),
					  	"Content":$("#f_content").val(),
					  	
					  }
					
					

					if($("#f_image")[0].files.length>0){
						//alert("Привет1");
						var ImageFile = $("#f_image")[0].files[0];
						read = new FileReader();
						read.readAsBinaryString(ImageFile);
						read.onloadend=function(){
							advertise_info.ImageFileName=ImageFile.name;
							advertise_info.Image=window.btoa(read.result);
							send_form_data();
						}
					}
					else {
						//alert("Привет2");
						send_form_data();
					}
					

					//Функция отправки данных
					function send_form_data(){
						var method="PUT";
						if($("#userModal #operation").val()==1) {
							method="PATCH";
							advertise_info.ID = $("#advertis_id").val();						
						}		
							
					
						$.ajax({
								url:"/rest/advertis",
								method: method,
								data: JSON.stringify(advertise_info),
								headers: {
									"Content-type":"application/json"
								},
								success:function(data)
								{									
									$('#user_form')[0].reset();
									$('#userModal').modal('hide');
									dataTable.ajax.reload();
								}
						});
					}
				});
				
				$(document).on('click', '.update', function(event){
					//Режим редактирования (кнопка Редактировать)
					var id = $(this).attr("id");// ID строки					
					
					
					$.ajax({
								url:"/rest/advertis?id="+id,
								method:'GET',
								dataType: "json",								
								success:function(data)
								{
									dataTable.ajax.reload();
									$(f_title).val(data.Title);
									$(f_content).val(data.Content);
									$(f_ID).val(id);
									//Заголовок окна
									//$('.modal-title').text("Редактировать компьютер");
									
									//Вывод принятых с сервера данных в поля формы
									// $("#userModal #f_name").val(data.Nazvanie);
									// $("#userModal #f_price").val(data.Cena);
									// $("#userModal #f_quantity").val(data.Kol);
									// $("#userModal #f_year").val(data.God);
									// $("#userModal #f_country").val(data.Strana);
									// $("#userModal #f_description").val(data.Opisanie);
									// $('#userModal #advertis_id').val(id);
									// $("#userModal #f_req").val(data.Proc);
									// $("#userModal #f_ram").val(data.Pamyat);									
									
									// //Флаг операции (1 - редактирование)
									// $("#userModal #operation").val("1");
									
									// //Текст на кнопке
									// $("#userModal #action").val("Сохранить изменения");
									
									// //Отобразить форму
									// $('#userModal').modal('show');									
								}
							});
					
					event.preventDefault();
				});
				
				// Кнопка "Добавить" на странице
				$("#add").click(function() {
					//Режим добавления (кнопка Добавить)
					
					//Заголовок окна
					$('.modal-title').text("Добавить рекламу");
					//Текст на кнопке (в модальном окне)
					$("#userModal #action").val("Добавить");
					//Флаг операции (0- добавление)
					$("#userModal #operation").val("0");
				});
				
				$(document).on("click",".delete",function() {
					//Режим удаления (кнопка Удалить)
					var advertis_id = $(this).attr("id");// ID строки				
					
					if(confirm("Действительно удалить?"))
					{
						$.ajax({
							url:"/rest/advertis?id="+advertis_id,
							method:"DELETE",							
							success:function(data)
							{								
								dataTable.ajax.reload();
							}
						});
					}
					else
					{
						return false;	
					}
				});				
			});
	  	</script>
	</head>
	<body>
		<div class="container box">
			<!-- Рекламодатель -->
			<form method="POST" action="" enctype="multipart/form-data">
				<div class="form-group">
					Заголовок:<br/>
					<input id="f_title" name="f_title" type="text" size="50" placeholder="Заголовок" class="form-control form-control-lg <?=$isvalid["f_title"]?>" value="<?=$f_title?>"/><br/>			
					<div class="invalid-feedback">
						<div>Поле "Заголовок" не заполнено</div>
					</div>
					Текст объявления:<br/>
					<textarea id="f_content" name="f_content" placeholder="Текст объявления" class="form-control form-control-lg <?=$isvalid["f_content"]?>">  <?=$_POST["f_content"]?></textarea><br/>			
						<div class="invalid-feedback">
							<div>Поле "Текст объявления" не заполнено</div>
						</div>
						<input id="f_ID" name="f_ID" type="hidden" value="<?=$_POST["f_ID"]?>"/>
						</div><br/>
					<div style="color: #F00;"><?=$errmsg?></div><br/>
					<!--<input name="Go" type="Submit" class="btn btn-secondary btn-block" value="Сохранить"/>-->
					<br />
					<div align="center">
						<!-- <button type="Submit" id="add" name="add" class="btn btn-info btn-lg">Добавить</button> -->
						<input type="submit" class="btn btn-info btn-lg" value="Добавить" name="add" id="add">
						<button type="button" id="add2" data-toggle="modal" data-target="#userModal" class="btn btn-info btn-lg">Добавить</button>
					</div>
				</div>	
			</form>
		</div>
		<div class="container">
			<h1>Реклама</h1>
			<!-- <div class="table-responsive"> -->
			<div class="col-md-12 col-lg-12 col-sm-12">
				<br /><br />
				<table id="advertis_data" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="10%">Дата публикации</th>
							<th width="10%">Заголовок</th>
							<th width="10%"></th>
							<th width="10%"></th>
						</tr>
					</thead>
				</table>				
			</div>
		</div>
		
		

		

		<div id="userModal" class="modal fade">
			<div class="modal-dialog">
				<form method="post" id="user_form" enctype="multipart/form-data">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Добавить рекламу</h4>
						</div>
						<div class="modal-body">
							<label for="f_title_form">Заголовок</label>
							<input type="text" name="f_title_form" id="f_title_form" class="form-control" />
							<div class="invalid-feedback">
								<div>Поле Заголовок не заполнено</div>
							</div>
							
							<br/>
							<label for="f_content_form">Текст объявления</label>
              				<textarea class="form-control" id="f_content_form" name="f_content_form"></textarea>


							
						</div>
							<div class="modal-footer"><!-- Подвал модальной формы -->
							<input type="hidden" name="tovar_id" id="tovar_id" />
							<input type="hidden" name="operation" id="operation" />
							<input type="submit" name="action" id="action" class="btn btn-success" value="Добавить" />
							<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
						</div>
					</div>
				</form>
			</div>
		
		
		</div>












	</body>
</html>