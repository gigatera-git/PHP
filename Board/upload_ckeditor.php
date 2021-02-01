<?php
header('Content-Type: text/html; charset=UTF-8');

$save_dir = "./upload/" . date('Y-m-d');
if ( !file_exists($save_dir) ) {
	mkdir($save_dir,0777);
}

if (isset($_FILES['upload'])) {
	//$upload_file_counts = count($_FILES['upload']['name']);
	//for($i = 0; $i < $upload_file_counts; $i++){
		$fileRealName = $_FILES['upload']['name'];//[$i];
		if (trim($fileRealName)!='') {
			$fileExplodes = explode('.',$fileRealName);
			$ext = array_pop($fileExplodes);
			$fileTmpName = $_FILES['upload']['tmp_name'];//[$i];
			$fileSaveName = uniqid('');
			if(move_uploaded_file($fileTmpName,$save_dir."/".$fileSaveName.'.'.$ext)){
				$fileType = $_FILES['upload']['type'];//[$i];
				$fileSize = $_FILES['upload']['size'];//[$i];
				
				//for ckeditor
				$uploaded = 1;
				$fileName = $fileRealName;
				$url = "upload"."/".date('Y-m-d')."/".($fileSaveName.'.'.$ext);
			} else {
				//for ckeditor
				$uploaded = 0;
				$fileName = "";
				$url = "";
			}
			
			//for ckeditor
			//$uploaded = array("uploaded"=>$uploaded);
			//$fileName = array("fileName"=>$fileName);
			//$url = array("url"=>$url);

			//$phpJSON = array($uploaded, $fileName, $url);

			$phpJSON = array("uploaded"=>$uploaded, "fileName"=>$fileName, "url"=>$url);
			$output =  json_encode($phpJSON,JSON_UNESCAPED_SLASHES);
			echo $output;
		}
	//}
}
?>