<?
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');
//echo date("Y-m-d");

$db = new DB();
$conn = $db->_conn;

$SearchOpt = trim($_REQUEST["SearchOpt"]);
$SearchVal = trim($_REQUEST["SearchVal"]);
$argv="SearchOpt=".$SearchOpt."&SearchVal=".$SearchVal;

$Page = trim($_REQUEST["Page"]); If ($Page=="") { $Page=1; }

$intPageSize = 10;
$intBlockPage = 10;

$intTotalCount = 0; 
$intTotalPage = 0;

$qry = "";
$qry = "Select COUNT(*),CEILING((COUNT(*)+0.0)/$intPageSize) ";
$qry .= "from tbl_board ";
$qry .= "where idx<>'' ";
if ($SearchOpt!="" && $SearchVal!="") {
	$qry .= "and ".$SearchOpt." like '%".$SearchVal."%' ";
}
$qry .= ";";
$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
if ($result) {
	$row = mysqli_fetch_array($result);
	$intTotalCount = $row[0]; 
	$intTotalPage = $row[1];
}
@mysqli_free_result($result);


$qry = "";
$qry = "Select idx,uname,title,pwd,click,ref,re_step,re_lvl,reg_ip,reg_date ";
$qry .= "from tbl_board ";
$qry .= "where idx<>'' ";
if ($SearchOpt!="" && $SearchVal!="") {
	$qry .= "and ".$SearchOpt." like '%".$SearchVal."%' ";
}
$qry .= "order by ref desc, re_step, re_lvl ";
$qry .= "limit ". (($Page-1)*$intPageSize) .",10 ";
//echo $qry;
$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn));
?>

<!doctype html>
<html lang="ko">
 <head>
  <meta charset="UTF-8">
  <title>리스트</title>
  <script language="javascript" type="text/javascript" src="./config/js/jquery-3.1.0.js"></script>
  <script language="javascript" type="text/javascript" src="./config/js/extend.js"></script>
  <script language="javascript" type="text/javascript">
  $(document).ready(function(){
		
		$("#frmBoard").submit(function(e){
			//e.preventDefault(); //This can interrupt html5 form check system.
			//if (confirm("저장할까요?")) {
				$("#frmBoard").attr({'action':'list.php'});
			//}
		});

		$(document).on("click","#btnInit",function(){ //for dynamic event...
			location.href = 'list.php';
			//alert('test');
		});

   });
  </script>
 </head>
 <body>

	<form name="frmBoard" id="frmBoard" method="get">
	
	<div id="write">
		[<a href="write.php">글등록</a>] ... php board with mysql/ckeditor/file upload
	</div>
	<table border="1">
	<tr>
	<td align="center"><b>번호</b></td>
	<td align="center"><b>제목</b></td>
	<td align="center"><b>작성자</b></td>
	<td align="center"><b>클릭수</b></td>
	<td align="center"><b>작성일</b></td>
	</tr>

	<?
	if ($result) {
		$Cnt = 0;
		while ($row = mysqli_fetch_array($result)) {
			?>
			<tr>
			<td align="center"><?=($intTotalCount-(($Page - 1) * $intPageSize))-$Cnt?></td>
			<td align="left">
				<img src="./images/common/level.gif" border="0" align="absmiddle" width="<?=$row["re_lvl"]*7?>">
				<?if ($row["re_lvl"]>0) {?>
				<img src="./images/common/ico_reply.gif" border="0" align="absmiddle" >
				<?}?>
				<a href="view.php?idx=<?=$row["idx"]?>&Page=<?=$Page?>&<?=$argv?>"><?=$row["title"]?>
				</a>
			</td>
			<td align="center"><?=$row["uname"]?></td>
			<td align="center"><?=$row["count"]?></td>
			<td align="center"><?=$row["reg_date"]?></td>
			</tr>
			<?
			$Cnt++;
		}
	} else {
		//echo "none";
	}
	?>
	

	</table>

	<div id="search">
		<select name="SearchOpt" id="SearchOpt" required oninvalid="this.setCustomValidity('검색옵션을 선택하세요')" oninput="setCustomValidity('')">
			<option value=""></option>
			<option value="title" <?if ($SearchOpt=='title' && $SearchVal!='') { echo " selected"; }?>>제목</option>
			<option value="contents" <?if ($SearchOpt=='contents' && $SearchVal!='') { echo " selected"; }?>>내용</option>
		</select>
		<input type="text" name="SearchVal" id="SearchVal" maxlength="10" minlength="2" required oninvalid="this.setCustomValidity('검색어를 입력하세요')" oninput="setCustomValidity('')" value="<?if ($SearchOpt!='' && $SearchVal!='') { echo $SearchVal; }?>">
		<input type="submit" value="검색" id="btnSearch" name="btnSearch">
		<?if ($SearchOpt!='' && $SearchVal!='') { 
			echo "<input type='button' value='처음' id='btnInit'>";
		}?>
	</div>


	<div id="paging">
	<?
	If ($Page>1) {
		echo "<a href='list.php?Page=1&".$argv."'>[처음]</a>";
	} else {
		echo "[처음]";
	}
	echo "&nbsp;";

	$intTemp = (int)(($Page - 1) / $intBlockPage) * $intBlockPage + 1;
	//echo "intTemp : " . $intTemp . "<br>";
	
	If ($intTemp == 1) { 
		echo "[이전]";
	} else {
		echo "<a href='list.php?Page=" . ($intTemp - $intBlockPage) . "&".$argv."'>[이전]</a>";
	}
	echo "&nbsp;";
	?>


	<?
	$intLoop = 1;
	while ($intLoop <= $intBlockPage && $intTemp <= $intTotalPage) {
		If ($intTemp == $Page) { 
			echo "<b>". ($intTemp) ."</b>";
		} else {
			echo "<span><a href='list.php?Page=" . ($intTemp) . "&".$argv."'>". ($intTemp) ."</a></span>";
		}
		echo "&nbsp;";
		
		$intTemp++;
		$intLoop++;
	}
	echo "&nbsp;";
	?>


	<?
	If ($intTemp > $intTotalPage) { 
		echo "[다음]";
	} else {
		echo "<a href='list.php?Page=" . $intTemp . "&".$argv."'>[다음]</a>";
	}
	echo "&nbsp;";
	
	If ($Page < $intTotalPage) {
		echo "<a href='list.php?Page=" . $intTotalPage . "&".$argv."'>[마지막]</a> ";
	} else {
		echo "[마지막]";
	}
	?>
	</div>


	</form>

 </body>
</html>
<?
@mysqli_free_result($result);
?>