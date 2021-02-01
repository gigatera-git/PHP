<?php
function chContent($CheckValue, $tag) {

	$Content = "";
	if ((int)$tag==0) {
		$Content = htmlspecialchars($CheckValue);
	} else {
		$Content = str_replace('\n\r','<br>',$CheckValue);
	}

	return $Content;
}

function IsImage($fileRealName) {
	$res = false;

	$fileExplodes = explode('.',$fileRealName);
	$ext = array_pop($fileExplodes);
	if(preg_match("/\.(jpe|jpeg|jpg|gif|bmp|png)$/i",$ext)) {
		$res = true;
	}

	return $res;
}
?>