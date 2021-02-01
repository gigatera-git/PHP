<?php
$save_dir = $_GET["filePath"];
$fileSaveName = $_GET["fileName"];
$down_file = $save_dir ."/". $fileSaveName;
$down_real_file = realpath($down_file); //정상 다운로드

//die(down_real_file);

if (isset($fileSaveName)) {
	
	if(file_exists($down_real_file)) {
		$filesize = filesize($down_real_file);

		if (is_file($down_real_file)) {
 
			//이 헤더가 바로 다운로드시키는 역할
			header("Content-Type: application/octet-stream");
			//header("Content-Type: application/force-download");
			//header("Content-type: application/x-msdownload");
			//header('Content-Type: application/x-octetstream');
			//header('Content-Type: application/pdf');
			//header("Content-Type: file/unknown");
			 
			header("Content-Disposition: attachment; filename=".$fileSaveName."");
			//한글 깨짐 방지 
			//header("Content-Disposition: attachment; filename=".iconv('UTF-8', 'CP949', $name_org));
			//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			//header("Cache-Control: public");
			 
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$filesize);
			Header("Cache-Control: cache, must-revalidate");
			header("Pragma: no-cache");
			//header("Pragma: public");
			header("Expires: 0");
			 
			 
			 
			$fh = fopen($down_real_file, "rb");
			fpassthru($fh);
			fclose($fh);
		}
		else {
			echo "해당 파일이 없습니다.";
		}

	} else {
		echo "<script>alert('파일이 없습니다1');history.back();</script>";
	}

} else {
	echo "<script>alert('파일이 없습니다2');history.back();</script>";
}
?>