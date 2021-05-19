<? 
 require_once("$_SERVER[DOCUMENT_ROOT]/../includes/flight/Flight.php");
 require_once("$_SERVER[DOCUMENT_ROOT]/../db/dal.inc.php");
  
 function CreateUser() {
	// //Получаем имя принятого от клиента файла
	// $imgFileName=Flight::request()->data["ImageFileName"];
	// //Извлекаем из мени файла расширение
	// $ext=substr($imgFileName, 1+strpos($imgFileName,"."));
	//Создаём новый  товар
	DBCreateAdvertis(
		Flight::request()->data["ID_user"],
		Flight::request()->data["Title"],
		Flight::request()->data["Content"]
		
	);
	//Получаем его id
	$tovar_id=_DBInsertID();
	// //Список расширений файлов, разрешённых к загрузке
    // $allowed_ext=Array("png", "jpg", "gif", "bmp");
    // //Путь к католог, куда должны быть загружены файлы.$_COOKIE
    // $image_path="$_SERVER[DOCUMENT_ROOT]/mysite/images";
	// //Проверяем 
	// if(in_array(strtolower($ext), $allowed_ext))
    // {
	// 	//Сохранение принятого файла на диск сервера
	// 	file_put_contents(
	// 		//"$_SERVER[DOCUMENT_ROOT]/../images/$imgFileName",
	// 		"$image_path/$tovar_id.$ext",
	// 		base64_decode(Flight::request()->data["Image"])
	// 	);
	// }
	
 }
 Flight::route('PUT /rest/advertis',"CreateUser");
 
 function ReadUser($id) {
	Flight::json(DBGetAdvertis($id));
 }
 Flight::route('GET /rest/advertis\?id=@id',"ReadUser");
 
 function ReadUsers() {
	$data=Array();
	//$db_rec = mysql_fetch_array($res,MYSQL_ASSOC);

	while($row=DBFetchAdvertis(
		$_POST["search"]["value"],
		$_POST['order']['0']['column'],
		$_POST['order']['0']['dir'],
		$_POST['start'],$_POST["length"])) 
	{
		$data[]=Array($row["DateTime"], $row["Title"],
		'<button type="button" name="update" id="'.$row["ID"].'" class="btn btn-warning btn-xs update">Редактировать</button>',
		'<button type="button" name="delete" id="'.$row["ID"].'" class="btn btn-danger btn-xs delete">Удалить</button>');
	}


	//Отправка данных клиенту в формате JSON (JavaScript Object Notation)
	Flight::json(Array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	count($data),
			"recordsFiltered"	=>	DBCountAllAdvertis(),
			"data"				=>	$data
	));
	
 }
 Flight::route('POST /rest/advertise',"ReadUsers");
 
 function UpdateUser() {
	 DBUpdateAdvertis(
		Flight::request()->data["ID"],
		Flight::request()->data["Title"],
		Flight::request()->data["Content"]

	);
 }
 Flight::route('PATCH /rest/advertis',"UpdateUser");
 
 function DeleteUser($id) {
	 DBDeleteUser($id);
 }
 Flight::route('DELETE /rest/advertis\?id=@id',"DeleteUser"); 

 Flight::start();
