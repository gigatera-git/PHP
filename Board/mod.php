<?php
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/var.php');
Require_once('config/common/proc.php');
Require_once('config/common/dbconf.php');


$db = new DB();
$conn = $db->_conn;

$idx = trim($_GET["idx"]);
$Page = trim($_GET["Page"]);
$SearchOpt = trim($_GET["SearchOpt"]);
$SearchVal = trim($_GET["SearchVal"]);

if ($idx=="") { die("<li>글번호가 없습니다</li>"); }
if ($Page=="") { die("<li>페이지 번호가 없습니다</li>"); }

$qry = "";
$qry = "select idx,uname,title,pwd,contents,click,ref,re_step,re_lvl,deleted,reg_ip,mod_ip,reg_date,mod_date ";
$qry .= "from tbl_board ";
$qry .= "where idx=".$idx.";";
//echo $qry;
$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
if ($result) {
	$row = mysqli_fetch_array($result);
}
@mysqli_free_result($result);
?>

<!doctype html>
<html lang="ko">
 <head>
  <meta charset="UTF-8">
  <title>글수정</title>
  <script language="javascript" type="text/javascript" src="./config/js/jquery-3.1.0.js"></script>
  <script language="javascript" type="text/javascript" src="./config/js/extend.js"></script>
  <script language="javascript" type="text/javascript" src="./ckeditor/ckeditor.js"></script>
  <script language="javascript" type="text/javascript">
  $(document).ready(function(){


	  CKEDITOR.replace('contents',{
		filebrowserUploadUrl:'upload_ckeditor.php'
	});
	
	
	//var $uname = $("#uname");
	//var $title = $("#title");
	var $pwd = $("#pwd");
	//var $pwd2 = $("#pwd2");
	//var $content = $("#content");
	
	/*
	//html4 일때 작성
	//console.log( $uname.getRightPwd() );
	//블라블라 일일이 작성한다
	
	$("#btnOk").on("click",function(e){
		e.preventDefault();
		if (confirm("저장할까요?")) {
			$("#frmBoard").attr({'action':'write_ok.php','method':'post'}).submit();
		}
	});
	
	*/


	//html5 required 속성 이용
	$("input, textarea").on('focus, keyup',function(){
		$lval = $(this).ltrim();
		$(this).val($lval);
	});
	
	$("#frmBoard").submit(function(e){
		//e.preventDefault();
		if (!$pwd.getRightPwd()) {
			alert("글 비밀번호가 올바르지 않습니다\n\n1. 영문,숫자,특수문자 조합으로 8~16자이어야합니다");
			$pwd2.focus();
			return false;
		}
		if (confirm("저장할까요?")) {
			$("#frmBoard").attr({'action':'mod_ok.php'});
		}
	});

	$("#btnCancel").on("click",function(){
		//history.back();
		location.href = "view.php?idx=<?=$idx?>&Page=<?=$Page?>&SearchOpt=<?=$SearchOpt?>&SearchVal=<?=$SearchVal?>";
	});

  });
  </script>
  <style type="text/css">
  .attach {
	display:block;
  }
  .attach img {
	width:100px;
	border:1px solid gray;
	margin-right:5px;
  }
  </style>
 </head>
 <body>

	<form name="frmBoard" id="frmBoard" method="post" enctype="multipart/form-data">
		
		<input type="hidden" name="idx" id="idx" value="<?=trim($row["idx"])?>">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
		<input type="hidden" name="SearchOpt" id="SearchOpt" value="<?=$SearchOpt?>">
		<input type="hidden" name="SearchVal" id="SearchVal" value="<?=$SearchVal?>">

		<table border="1">
		<tr>
		<td align="center"><b>글쓴이</b></td>
		<td><input type="hidden" name="uname" id="uname" value="<?=trim($row["uname"])?>" size="10" maxlength="10" placeholder="글쓴이" autofocus required oninvalid="this.setCustomValidity('글쓴이를 입력하세요')" oninput="setCustomValidity('')">
		<?=trim($row["uname"])?>
		</td>
		</tr>
		<tr>
		<td align="center"><b>제목</b></td>
		<td><input type="text" name="title" id="title" value="<?=trim($row["title"])?>" size="30" maxlength="30" placeholder="제목" required oninvalid="this.setCustomValidity('제목을 입력하세요')" oninput="setCustomValidity('')"></td>
		</tr>

		<tr>
		<td align="center"><b>비밀번호</b></td>
		<td><input type="password" name="pwd" id="pwd" value="12345678#a" size="16" minlength="8" maxlength="16" placeholder="비밀번호" required oninvalid="this.setCustomValidity('비밀번호를 입력하세요')" oninput="setCustomValidity('')"></td>
		</tr>

		<tr>
		<td align="center"><b>내용</b></td>
		<td><textarea name="contents" id="contents" cols="20" rows="10" required oninvalid="this.setCustomValidity('글내용을 입력하세요')" oninput="setCustomValidity('')"><?=trim($row["contents"])?></textarea></td>
		</tr>

		<tr>
		<td align="center"><b>첨부</b></td>
		<td>
			<?
			$qry = "";
			$qry = "select idx,bidx,fileRealName,fileSaveName,fileType,fileSize,reg_ip,mod_ip,reg_date,mod_date ";
			$qry .= "from tbl_board_upload  ";
			$qry .= "where bidx=".$row["idx"]." order by idx desc limit 2; ";
			//echo $qry;
			$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
			?>

			<?
			if ($result) {
				$Cnt = 0;
				while ($row = mysqli_fetch_array($result)) {
					echo "<div class='attach'>";
					echo "(". ($Cnt+1) .")";
					if (IsImage($row["fileSaveName"])) {
						echo "<a href='download.php?filePath=upload/".substr($row["reg_date"],0,10)."&fileName=".$row["fileSaveName"]."'><img src='upload/". substr(0,10,$row["reg_date"]) ."/". $row["fileSaveName"] ."' title='". $row["fileRealName"] ."' align='absmiddle' /></a>";
					} else {
						echo "<a href='download.php?filePath=upload/".substr($row["reg_date"],0,10)."&fileName=".$row["fileSaveName"]."' title='". $row["fileRealName"] ."'>". $row["fileRealName"] ."</a>";
						//파일형식 아이콘 추가, 원하면..
					}

					echo "(<input type='checkbox' name='attachDels[]' class='attachDels' value='".substr($row["reg_date"],0,10)."/".$row["fileSaveName"]."' />삭제)";
					echo "<br>";

					//echo "<input type='file' name='files' class='files' value='".substr($row["reg_date"],0,10)."/".$row["fileSaveName"]."' />";
					echo "</div>";
					
					$Cnt++;
					//fileAttachMax = fileAttachMax - 1
				}
			}
			@mysqli_free_result($result);
			//echo "fileAttachMax : " . $fileAttachMax . "<br>";
			echo "<hr>";
			for ($i=0;$i<=($fileAttachMax-1);$i++) {
				echo "<input type='file' name='files[]' class='file'>";
				echo "<br>";
			}
			?>
		</td>
		</tr>

		</table>
		
		<table border="0">
		<tr>
		<td>
			<input type="submit" value="수정" id="btnOk">
			<input type="button" value="취소" id="btnCancel">
		</td>
		</tr>
		</table>

	</form>
  
 </body>
</html>