<?php
header('Content-Type: text/html; charset=UTF-8');

Require_once('config/common/dbconf.php');
Require_once('config/common/proc.php');

$db = new DB();
$conn = $db->_conn;
?>

<?
$idx = trim($_GET["idx"]);
$Page = trim($_GET["Page"]);
$SearchOpt = trim($_GET["SearchOpt"]);
$SearchVal = trim($_GET["SearchVal"]);

if ($idx=="") { die("<li>글번호가 없습니다</li>"); }
if ($Page=="") { die("<li>페이지 번호가 없습니다</li>"); }

//echo "count_done : " . $_COOKIE["count_done"];
if ($_COOKIE["count_done"]!=$idx) {
	$qry = "";
	$qry = "update tbl_board set click=click+1 where idx=".$idx.";";
	//echo $qry;
	if (!mysqli_query($conn,$qry)) {
		die(mysqli_error($conn));
	} 

	setcookie("count_done", $idx);
} 

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
  <title>상세보기</title>
  <script language="javascript" type="text/javascript" src="./config/js/jquery-3.1.0.js"></script>
  <script language="javascript" type="text/javascript" src="./config/js/jquery.bpopup.min.js"></script>
  <script language="javascript" type="text/javascript" src="./config/js/extend.js?v=2020-07-23-001"></script>
  <script language="javascript" type="text/javascript">
  $(document).ready(function(){
		
		var $idx = $("#idx").val();
		var $Page = $("#Page").val();
		var $SearchOpt = $("#SearchOpt").val();
		var $SearchVal = $("#SearchVal").val();
		var $ref = $("#ref").val();
		var $re_step = $("#re_step").val();
		var $re_lvl = $("#re_lvl").val();

		$("#btnList").on("click",function(){
			location.href = "list.php?Page="+$Page+"&SearchOpt="+$SearchOpt+"&SearchVal="+$SearchVal;
		});

		$("#btnReply").on("click",function(){
			location.href = "reply.php?idx="+$idx+"&Page="+$Page+"&SearchOpt="+$SearchOpt+"&SearchVal="+$SearchVal+"&ref="+$ref+"&re_step="+$re_step+"&re_lvl="+$re_lvl;
		});

		$("#btnDel").on("click",function(){
			$('#popPwd').bPopup(
				{modalClose: true},
				function(){ $("#pwdChk").val('').focus(); }
			);
		});

		$("#btnMod").on("click",function(){
			location.href = "mod.php?idx="+$idx+"&Page="+$Page+"&SearchOpt="+$SearchOpt+"&SearchVal="+$SearchVal;
		});

		$("#btnPwdChkOk").on("click",function(e){
			var $res = "";
			var $pwd = $("#pwdChk");
			if (!$pwd.getRightPwd()) {
				alert("비밀번호가 올바르지 않습니다\n\n- 영문,숫자,특수문자 조합으로 8~16자이어야합니다");
				$pwd.focus();
				return false;
			}
			//alert("idx="+$idx+"&pwd="+escape($pwd.val()));
			$.ajax({
				type: "GET",
				async: false,
				url: "pwdChk.php",
				data: "idx="+$idx+"&pwd="+escape($pwd.val())
			}).fail(function(request,status,error) {  //error
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}).done(function(msg) {
				$res = msg;
			});
			if ($res!='ok') {
				alert($res);
			} else {
				alert("비밀번호가 확인되었습니다")
				$("#pwd").val($("#pwdChk").val());
				$("#frmBoard").attr({'action':'del_ok.php','method':'post'}).submit();
			}
		});

   });
  </script>
  <style type="text/css">
  #popPwd {
	width:500px;
	height:160px;
	border:1px solid gray;
	display:none;
	background-color:white;
	position:relative;
  }
  #popPwd #bClose {
	position:absolute;
	right:-10px;
	top:-30px;
	font:arial-black;
	font-size:36px;
	font-weight:bold;
	color:black;
	background-color:yellow;
	width:40px;
	height:40px;
	line-height:40px;
	text-align:center;
	cursor:pointer;
  }

  #popPwd #pwdcheckbody {
	margin-left:20px;
	margin-top:20px;
  }

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

	<form name="frmBoard" id="frmBoard">
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
		<input type="hidden" name="SearchOpt" id="SearchOpt" value="<?=$SearchOpt?>">
		<input type="hidden" name="SearchVal" id="SearchVal" value="<?=$SearchVal?>">
		<input type="hidden" name="ref" id="ref" value="<?=trim($row["ref"])?>">
		<input type="hidden" name="re_step" id="re_step" value="<?=trim($row["re_step"])?>">
		<input type="hidden" name="re_lvl" id="re_lvl" value="<?=trim($row["re_lvl"])?>">
		<input type="hidden" name="pwd" id="pwd" value="">

		<table border="1">
		<tr>
		<td align="center"><b>작성자</b></td><td><?=trim($row["uname"])?></td>
		</tr>
		<tr>
		<td align="center"><b>제목</b></td><td><?=ChContent(trim($row["title"]), 0)?></td>
		</tr>
		<tr>
		<td align="center"><b>내용</b></td><td><?=ChContent(trim($row["contents"]), 1)?></td>
		</tr>
		<tr>
		<td align="center"><b>클릭수</b></td><td><?=trim($row["click"])?></td>
		</tr>
		<tr>
		<td align="center"><b>아이피</b></td><td><?=trim($row["reg_ip"])?></td>
		</tr>
		<tr>
		<td align="center"><b>아이피(m)</b></td><td><?=trim($row["mod_ip"])?></td>
		</tr>
		<tr>
		<td align="center"><b>등록일</b></td><td><?=trim($row["reg_date"])?></td>
		</tr>
		<tr>
		<td align="center"><b>수정일</b></td><td><?=trim($row["mod_date"])?></td>
		</tr>
		<tr>
		<td align="center"><b>첨부파일</b></td><td>
		
		<?
		$qry = "";
		$qry = "select idx,bidx,fileRealName,fileSaveName,fileType,fileSize,reg_ip,mod_ip,reg_date,mod_date ";
		$qry .= "from tbl_board_upload ";
		$qry .= "where bidx=".$idx." order by idx desc limit 2;";
		$result = @mysqli_query($conn,$qry) or die(mysqli_error($conn)); 
		if ($result) {
			$Cnt = 0;
			while ($row = mysqli_fetch_array($result)) {
				echo "<div class='attach'>";

				echo "(". ($Cnt+1) .")";
				if (explode('/',$row["fileType"])[0]=="image") {
					echo "<a href='download.php?filePath=upload/".substr($row["reg_date"],0,10)."&fileName=".$row["fileSaveName"]."'><img src='upload/". substr($row["reg_date"],0,10) ."/". $row["fileSaveName"] ."' title='". $row["fileRealName"] ."' align='absmiddle' /></a>";
				} else {
					echo "<a href='download.php?filePath=upload/".substr($row["reg_date"],0,10)."&fileName=".$row["fileSaveName"]."' title='". $row["fileRealName"] ."'/>". $row["fileRealName"] ."</a>";
				}
				echo "<input type='hidden' name='files' class='files' value='". substr($row["reg_date"],0,10) ."/". trim($row["fileSaveName"]) ."' />";
				echo "</div>";

				$Cnt++;
			}
		}
		@mysqli_free_result($result);
		?>

			
		
		</td>
		</tr>
		</table>
		<div>
			<input type="button" value="리스트" id="btnList" />
			<input type="button" value="답글" id="btnReply" />
			<input type="button" value="수정" id="btnMod" alt="수정" />
			<input type="button" value="삭제" id="btnDel" alt="삭제" />
		</div>

		<div id="popPwd" class="b-close">
			<div id="bClose" class="b-close">
				X
			</div>
			<div id="pwdcheckbody">
				<b>● 비밀번호 확인</b> <br><br>
				해당글 삭제를 위해 글 비밀번호를 입력하세요<br><br>

				<input type="password" name="pwdChk" id="pwdChk" value="" placeholder="비밀번호" minlength="8" maxlength="16">
				<input type="button" value="확인" id="btnPwdChkOk">
			</div>
		</div>

	</form>
  
 </body>
</html>
