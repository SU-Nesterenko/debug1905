<?php require_once("$_SERVER[DOCUMENT_ROOT]/../db/common.dal.inc.php");

//CRUD - Create Read Update Delete
//Создание новой рекламы (Create)
function DBCreateAdvertis($userid,$title,$content) {
	//Предотвращение SQL-инъекций
	$title=_DBEscString($title);
	$content=_DBEscString($content);
	//$userid=(int)$userid;
	
	
	//Выполнение запроса к БД
	_DBQuery(
		"INSERT INTO advertise(UserID,Title,Content,DateTime) 
	 	VALUES('14','$title','$content','".date("Y-m-d H:i:s")."')"
	);
}

//Получение одного пользователя (Read)
function DBGetAdvertis($id) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	//Выполнение запроса
	return _DBGetQuery("SELECT * FROM advertise WHERE ID=$id");
}

//Получение списка пользователей (Read)
function DBFetchAdvertis($search_string,$sort,$dir,$s,$l) {
	//Предотвращение SQL-инъекций
	$search_string=_DBEscString($search_string);
	$sort=(int)$sort;
	$dir=_DBEscString($dir);
	$s=(int)$s;
	$l=(int)$l;	
	
	//Формирование запроса
	$limit="LIMIT $s,$l";
	
	$where_like="";
	if(trim($search_string)!="") {
		$search_string=_DBEscString($search_string);
		$where_like="WHERE Title LIKE \"%$search_string%\"";
	}

	$order="";
	if(trim($sort)!="" && $dir!="") 
		$order="ORDER BY ".((int)$sort+2)." $dir";	
	
	//Выполнение запроса
	return _DBFetchQuery("SELECT * FROM advertise $where_like $order $limit");
}


// function DBFetchAdvertise($UserID=-1) {
// 	if($UserID==-1)
// 		$filter=""; 
// 	else
// 		$filter="AND UserID=$UserID";
// 	return _DBFetchQuery("
// 		SELECT 
// 			advertise.ID As ID,
// 			users.login As Author,
// 			users.ID As AuthorID,
// 			advertise.Title As Title,			
// 			advertise.DateTime As DateTime,
// 			advertise.Content As Content
// 		FROM advertise,users 
// 		WHERE advertise.UserID=Users.ID $filter
// 		ORDER BY DateTime DESC");
// }


//Подсчёт общего числа пользователей в базе (Read)
function DBCountAllAdvertis() { 
	return _DBRowsCount(_DBQuery("SELECT * from advertise"));
}

//Редактирование элемента (Update)
function DBUpdateAdvertis($id,$title,$content) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	$title=_DBEscString($title);
	$content=_DBEscString($content);
	
	//Выполнение запроса	
	_DBQuery("
		UPDATE advertise 
		SET Title='$title',Content='$content'
		WHERE id=$id
	");
}

//Удаление элемента (Delete)
function DBDeleteUser($id) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	
	//Выполнение запроса
	_DBQuery("DELETE FROM advertise WHERE id=$id");
}
