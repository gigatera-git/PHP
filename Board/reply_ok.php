<?php
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');

$db = new DB();
$conn = $db->_conn;


$referer = $_SERVER['HTTP_REFERER']; //echo $referer;
$URIs = parse_url($referer);
$uname = trim($_REQUEST["uname"]);
$title = trim($_REQUEST["title"]);
$pwd = trim($_REQUEST["pwd"]);
$pwd2 = trim($_REQUEST["pwd2"]);
$contents = trim($_REQUEST["contents"]);
$reg_ip = $_SERVER["REMOTE_ADDR"];

$ref = trim($_REQUEST["ref"]);
$re_step = trim($_REQUEST["re_step"]);
$re_lvl = trim($_REQUEST["re_lvl"]);

//$uname = iconv("UTF-8", "UTF-8", $uname);
//$title = iconv("UTF-8", "UTF-8", $title);
//$contents = iconv("UTF-8", "UTF-8", $contents);

//echo "uname : " . $uname . "<br>";
//echo "title : " . $title . "<br>";
//echo "contents : " . $contents . "<br>";

if ($URIs["host"]!="localhost" || $URIs["path"]!="/board/reply.php") {
	die("<li>(".$reg_ip.")에서 비정상 접근이 감지되었습니다</li>");
}

if ($uname=="") { die("<li>작성자가 없습니다</li>"); }
if ($title=="") { die("<li>제목이 없습니다</li>"); }
if ($pwd=="") { die("<li>비밀번호가 없습니다</li>"); }
if ($pwd2=="") { die("<li>비밀번호 확인이 없습니다</li>"); }
if ($pwd!=$pwd2) { die("<li>비밀번호와 비밀번호 확인이 다릅니다</li>"); }
if ($contents=="") { die("<li>내용이 없습니다</li>"); }

if ($ref=="") { die("<li>ref가 없습니다</li>"); }
if ($re_step=="") { die("<li>re_step가 없습니다</li>"); }
if ($re_lvl=="") { die("<li>re_lvl이 없습니다</li>"); }


$qry = "";
$qry = "Update tbl_board SET re_step=re_step+1 where ref=".$ref." AND re_step > ".$re_step.";";
if (!mysqli_query($conn,$qry)) {
	die(mysqli_error($conn));
}

$bidx = 0; //for file
$res = 0;
$qry = "";
$qry = "";
$qry = "insert into tbl_board(uname,title,pwd,contents,ref,re_step,re_lvl,reg_ip,reg_date) ";
$qry .= "values('".$uname."','".$title."',PASSWORD('".$pwd."'),'".$contents."',".$ref.",".($re_step+1).",".($re_lvl+1).",'".$reg_ip."',now()); ";
if (!mysqli_query($conn,$qry)) {
	$res = 1;
	die(mysqli_error($conn));
} else {
	$bidx = mysqli_insert_id($conn);
}
@mysqli_free_result($result);

//echo "bidx : " . $bidx . "<br>";
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
			location.href = "list.php?Page=<?=$Page?>&SearchOpt=<?=$SearchOpt?>&SearchVal=<?=$SearchVal?>";
		},5000);
	});
  </script>
 </head>
	<body>
		<?php
		switch($res) {
			case 0:
				echo "<li>정상적으로 등록되었습니다</li>";
				break;
			case 1:
				echo "<li>등록처리중 에러가 발생하였습니다. 관리자에게 문의하세요</li>";
				break;
			case 2:
				echo "<li>파일등록처리중 에러가 발생하였습니다. 관리자에게 문의하세요</li>";
				break;
		}
		echo "<li>잠시후 리스트로 이동합니다</li>";
		?>
	</body>
</html>