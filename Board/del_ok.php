<?
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');

$referer = $_SERVER['HTTP_REFERER']; //echo $referer;
$URIs = parse_url($referer);
$idx = trim($_POST["idx"]);
$Page = trim($_POST["Page"]);
$SearchOpt = trim($_POST["SearchOpt"]);
$SearchVal = trim($_POST["SearchVal"]);
$pwd = trim($_POST["pwd"]);
$reg_ip = $_SERVER["REMOTE_ADDR"];
//echo $URIs["host"] . "<br>";
//echo $URIs["path"] . "<br>";

if ($URIs["host"]!="localhost" || $URIs["path"]!="/board/view.php") {
	die("<li>(".$reg_ip.")에서 비정상 접근이 감지되었습니다</li>");
}

if ($idx=="") { die("<li>글번호가 없습니다</li>"); }
if ($Page=="") { die("<li>페이지 번호가 없습니다</li>"); }
if ($pwd=="") { die("<li>비밀번호가 없습니다</li>"); }

$db = new DB();
$conn = $db->_conn;

$Res = "";
$qry = "";
$qry = "delete from tbl_board where idx=".$idx." and pwd=PASSWORD('".$pwd."');";
if (!mysqli_query($conn,$qry)) {
	$Res = 2;
	die(mysqli_error($conn));
} else {
	$Res = 1;
}
@mysqli_free_result($result);
?>

<!doctype html>
<html lang="ko">
 <head>
  <meta charset="UTF-8">
  <title>글삭제</title>
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
		<?
		if ($Res==1) {
			echo "<li>정상적으로 삭제되었습니다</li>";
		} else {
			echo "<li>비밀번호가 일치하지 않거나, 이미 삭제된 글입니다</li>";
		}

		echo "<li>잠시후 리스트로 이동합니다</li>";
		?>
	</body>
</html>