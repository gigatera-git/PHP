<?php
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');

$db = new DB();
$conn = $db->_conn;

//-----------------------------------------------------
$idx = trim($_POST["idx"]);
$Page = trim($_POST["Page"]);
$SearchOpt = trim($_POST["SearchOpt"]);
$SearchVal = trim($_POST["SearchVal"]);

if ($idx=="") { die("<li>글번호가 없습니다</li>"); }
if ($Page=="") { die("<li>페이지 번호가 없습니다</li>"); }

//-----------------------------------------------------
$referer = $_SERVER['HTTP_REFERER']; //echo $referer;
$URIs = parse_url($referer);
$uname = trim($_POST["uname"]);
$title = trim($_POST["title"]);
$pwd = trim($_POST["pwd"]);
$contents = trim($_POST["contents"]);
$mod_ip = $_SERVER["REMOTE_ADDR"];

if ($URIs["host"]!="localhost" || $URIs["path"]!="/board/mod.php") {
	die("<li>(".$reg_ip.")에서 비정상 접근이 감지되었습니다</li>");
}

if ($uname=="") { die("<li>작성자가 없습니다</li>"); }
if ($title=="") { die("<li>제목이 없습니다</li>"); }
if ($pwd=="") { die("<li>비밀번호가 없습니다</li>"); }
if ($contents=="") { die("<li>내용이 없습니다</li>"); }

$qry = "";
$qry = "select count(*) from tbl_board where idx=".$idx." and pwd=password('".$pwd."');";
$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
if ($result) {
	$row = mysqli_fetch_array($result);
}
@mysqli_free_result($result);
if ($row[0]<1) { die("<li>글 비밀번호가 일치하지 않습니다</li>"); }

$qry = "";
$qry = "update tbl_board set ";
$qry .= "title='".$title."', ";
$qry .= "contents='".$uname."', ";
$qry .= "mod_ip='".$mod_ip."', ";
$qry .= "mod_date=now() ";
$qry .= "where idx=".$idx.";";
if (!mysqli_query($conn,$qry)) {
	die(mysqli_error($conn));
} 


$bidx = $idx;

//-----------------------------------------------------------------
$attachDels = $_POST["attachDels"];  //첨부삭제 체크시 처리 (board/저장파일명 으로 전달됨)
//echo "attachDels : " . $attachDels . "<br>";
//echo "attachDels[0] : " . $attachDels[0] . "<br>";
if (isset($attachDels)) {
	for ($i=0;$i<count($attachDels);$i++) {
		$attachpath = $attachDels[$i];
		$attachrealpath = realpath("upload/".$attachpath);
		$attachpfile = explode('/',$attachpath);
		$attach = array_pop($attachpfile);
		//echo "attach : " . $attach . "<br>";
		
		if (unlink($attachrealpath)) {
			$qry = "";
			$qry = "delete from tbl_board_upload where fileSaveName='".$attach."';";
			if (!mysqli_query($conn,$qry)) {
				echo(mysqli_error($conn));
			}
		}
	}
}
//die("--------------a------------------");



//-----------------------------------------------------------------

if ($bidx>0) { //file upload
	
	$qry = "";

	$save_dir = "./upload/" . date('Y-m-d');
	if ( !file_exists($save_dir) ) {
		mkdir($save_dir,0777);
	}
	
	if (isset($_FILES['files'])) {
		$upload_file_counts = count($_FILES['files']['name']);
		for($i = 0; $i < $upload_file_counts; $i++){
			$fileRealName = $_FILES['files']['name'][$i];
			if (trim($fileRealName)!='') {
				$fileExplodes = explode('.',$fileRealName);
				$ext = array_pop($fileExplodes);
				$fileTmpName = $_FILES['files']['tmp_name'][$i];
				$fileSaveName = uniqid('board_');
				//echo "a : " . $save_dir."/".$fileSaveName.'.'.$ext . "<br>";
				if(move_uploaded_file($fileTmpName,$save_dir."/".$fileSaveName.'.'.$ext)){
					$fileType = $_FILES['files']['type'][$i];
					$fileSize = $_FILES['files']['size'][$i];

					$qry .= "('".$bidx."','".$fileRealName."','".($fileSaveName.'.'.$ext)."','".$fileType."','".$fileSize."','".$reg_ip."',now()),";
				} else {
					echo "$i번째 파일 업로드 실패<br />";
				}
			}
		}
		if ($qry!="") {
			$qry = "insert into tbl_board_upload(bidx,fileRealName,fileSaveName,fileType,fileSize,reg_ip,reg_date) VALUES " . $qry;
			$qry = substr_replace($qry,";",-1,1);
			//echo $qry;

			if (!mysqli_query($conn,$qry)) {
				$res = 2;
				die(mysqli_error($conn));
			}
		}
	}

}
?>

<!doctype html>
<html lang="ko">
 <head>
  <meta charset="UTF-8">
  <title>글쓰기</title>
  <script language="javascript" type="text/javascript" src="./config/js/jquery-3.1.0.js"></script>
  <script language="javascript" type="text/javascript" src="./config/js/extend.js"></script>
  <script language="javascript" type="text/javascript">
	$(document).ready(function(){
		setTimeout(function(){
			location.href = "view.php?idx=<?=$idx?>&Page=<?=$Page?>&SearchOpt=<?=$SearchOpt?>&SearchVal=<?=$SearchVal?>";
		},1500);
	});
  </script>
 </head>
	<body>
		<?php
		switch($res) {
			case 0:
				echo "<li>정상적으로 수정되었습니다</li>";
				break;
			case 1:
				echo "<li>수정처리중 에러가 발생하였습니다. 관리자에게 문의하세요</li>";
				break;
			case 2:
				echo "<li>파일수정처리중 에러가 발생하였습니다. 관리자에게 문의하세요</li>";
				break;
		}
		echo "<li>잠시후 상세페이지로 이동합니다</li>";
		?>
	</body>
</html>