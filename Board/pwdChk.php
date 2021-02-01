<?
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');

$idx = trim($_GET["idx"]);
$pwd = trim($_GET["pwd"]);

$Res = "";
if ($Res=="" && $idx=="") {
	$Res = "글번호가 없습니다";
} elseif ($Res=="" && $pwd=="") {
	$Res = "비밀번호가 없습니다";
} elseif ($Res=="") {
	
	$db = new DB();
	$conn = $db->_conn;

	$qry = "select count(*) from tbl_board where idx=".$idx." and pwd=PASSWORD('".$pwd."');";
	$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
	if ($result) {
		$row = mysqli_fetch_array($result);
	}
	@mysqli_free_result($result);

	if ((int)$row[0]<1) {
		$Res = "비밀번호가 일치하지 않습니다";
	} else {
		$Res = "ok";
	}

}

echo $Res;
?>