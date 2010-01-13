<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>@@</title>
<SCRIPT language=JavaScript1.2>

//change 1 to another integer to alter the scroll speed. Greater is faster

var speed=20

var currentpos=0,alt=1,curpos1=0,curpos2=-1

function initialize(){

startit()

}

function scrollwindow(){

if (document.all &&

!document.getElementById)

temp=document.body.scrollTop

else

temp=window.pageYOffset

if (alt==0)

alt=2

else

alt=1

if (alt==0)

curpos1=temp

else

curpos2=temp



if (document.all)

currentpos=document.body.scrollTop+speed

else

currentpos=window.pageYOffset+speed

window.scroll(0,currentpos)


}

function startit(){

forwardInterval =setInterval("scrollwindow()",50)

}

function stopinterval(){

clearInterval (forwardInterval);
document.title=":)";

}
initialize();
window.onload=stopinterval
</SCRIPT>


<?php
include 'do_function.php';

$username = "YOUR MYSQL USERNAME";
$password = "YOUR MYSQL PASSWORD";
$hostname = "localhost"; //you know what this is:) 
$dbname="YOUR DATABASE NAME";
$db_table_name="TABLE NAME";

$user_arr = array();
$pwd_arr = array();
$school_arr = array();
$name_arr = array();
$f_user_arr = array();
$f_pwd_arr = array();
$f_school_arr = array();
$f_name_arr = array();
$dbuser_handle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");


$selected = mysql_select_db($dbname,$dbuser_handle)
  or die("Could not select examples");

$result=mysql_query("SELECT * FROM ".$db_table_name);
$i=0;
while($row = mysql_fetch_array($result)){
	$f_user_arr[$i] = $row['id'];
	$f_pwd_arr[$i] = $row['pwd'];
	$f_school_arr[$i] = $row['school'];
	$f_name_arr[$i] = $row['user'];
	$i++;
}
mysql_close($dbuser_handle);
$user_arr = $f_user_arr;
shuffle($user_arr);
$i=0;
while($i<sizeof($f_user_arr)){
	$pwd_arr[$i]=$f_pwd_arr[array_search($user_arr[$i], $f_user_arr)];
	$school_arr[$i]=$f_school_arr[array_search($user_arr[$i], $f_user_arr)];
	$name_arr[$i]=$f_name_arr[array_search($user_arr[$i], $f_user_arr)];
	$i++;
}

function search_string($start , $end , $from, $number=1){
	if($number<1){
		$number=1;
	}
	$from = trim(str_replace("\r\n","",$from));
	$i = 0;
	$ans = $from;
	if(!stristr($ans , $start)){
		return false;
	}
	while($i<$number){
		if(stristr($ans , $start)){
			$ans = stristr($ans , $start);
			$ans = substr($ans, strlen($start));
		}
		$i++;
	}
	if(strpos($ans, $end)){
		$pos = strpos($ans, $end);
		$ans = substr($ans, 0, $pos);
	}
	if(isset($ans) && $ans != "" ){
		return trim(str_replace("\r\n","",$ans));
	}else{
		return false;
	}
}
function search_from_end($end, $start, $from){
	$ans = $from;
	if(strpos($ans, $end)){
		$pos = strpos($ans, $end);
		$ans = substr($ans, 0, $pos);
		$ans = strrchr($ans, $start);
	}
	return $ans;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
$af = curl_init();
curl_setopt($af, CURLOPT_RETURNTRANSFER, 1); 

$got_answer = false;
$tried_user = 0;
for($i=0 ; $i<sizeof($user_arr) ; $i++){
	if(!$got_answer){
		$now_user = $user_arr[$i];
		$now_pwd = $pwd_arr[$i];
		$now_school = $school_arr[$i];
		$url="http://prof-ho.appspot.com/login";
		$query="orgNo=".$now_school."&loginId=".$now_user."&passwd=".$now_pwd;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		echo "user: ".$name_arr[$i]."<BR />";
		$result = curl_exec($ch);
		$retried=0;
		while(!search_string("Status", "OK", $result)&&$retried<3){
			echo $name_arr[$i].' timeout..重試, message:'.$result.'<BR />';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			$result = curl_exec($ch);
			$retried++;
		}
		if(search_string("<title>","</title>", $result,1) == "failure" ||search_string("<title>","</title>", $result,1) == "A passage A day"){
			echo "登入不能";
			if(search_string('<font color="#FF0000">',"</font>", $result)){
				echo ",".search_string('<font color="#FF0000">',"</font>", $result)."<BR />";
			}
		}elseif(search_string("<title>","</title>", $result,1) == "每日一篇"){
			$hex = search_string("jsessionid=",'"', $result,1);
			$url = "http://www.prof-ho.com/rdcn/student/readArticle.do;jsessionid=".$hex."?series=5";
			curl_setopt($af, CURLOPT_URL, $url);
			curl_setopt($af, CURLOPT_RETURNTRANSFER, 1); 
			$result = curl_exec($af);
			if(search_string('<font color="#FF0000">', "</font>", $result) != "你已經成功讀過這篇文章，不能再提交。" && search_string('<font color="#FF0000">', "</font>", $result) != "你已經讀完當天文章，不能再提交。"){
				for($j=0 ; $j<3 ; $j++){
					$n = $j+1;
					$z=1+$j*4;
					$y = 1+$n*4;
					$x = 0;
					while($z< $y){
						$this_q = search_string('<td align="right" background="../images/tbbg.jpg">','</tr>',$result,$z);
						echo "<BR />".$this_q;
						if(!$got_answer){
							if(search_string('<input type="radio" name="answer[',']',$this_q) == 0){
								if($tried_user==0){
									$question0[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									$answer0[$x] = search_string(']" value="','"',$this_q);
								}else{
									$f_question0[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									$f_answer0[$x] = search_string(']" value="','"',$this_q);
										$answer0[array_search($f_question0[$x], $question0)] = $f_answer0[$x];
								}
							}
							if(search_string('<input type="radio" name="answer[',']',$this_q) == 1){
								if($tried_user==0){
									$question1[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									$answer1[$x] = search_string(']" value="','"',$this_q);
								}else{
									$f_question1[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									$f_answer1[$x] = search_string(']" value="','"',$this_q);
										$answer1[array_search($f_question1[$x], $question1)] = $f_answer1[$x];
								}
							}
							if(search_string('<input type="radio" name="answer[',']',$this_q) == 2){
								if($tried_user==0){
									$question2[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									echo 'question2['.$x.'] is : '.search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q)."<BR />";
									$answer2[$x] = search_string(']" value="','"',$this_q);
									echo 'answer2['.$x.'] is : '.search_string(']" value="','"',$this_q)."<BR />";
								}else{
									$f_question2[$x] = search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q);
									$f_answer2[$x] = search_string(']" value="','"',$this_q);
										$answer2[array_search($f_question2[$x], $question2)] = $f_answer2[$x];
								}
							}
						}
						$x++;
						$z++;
					}
				}
				$url="http://www.prof-ho.com/rdcn/student/replyArticle.do;jsessionid=".$hex;
				$j =0;
				while($j<4){
					if(!$got_answer){
						$query='series=5&answer[0]='.$answer0[$tried_user].'&answer[1]='.$answer1[$tried_user].'&answer[2]='.$answer2[$j];
						echo '<BR />'.$query;
						echo '<BR />a....series=5&answer[0]='.$question0[$tried_user].'&answer[1]='.$question1[$tried_user].'&answer[2]='.$question2[$j];
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
						$result = curl_exec($ch); 
						if(search_string("<title>" , "</title>" , $result) == "prompt"){
							echo search_string('<font color="#FF0000">' , '</font>' , $result)."<BR />";
						}
						if(search_string("<title>" , "</title>" , $result) == "failure"){
							echo search_string('<font color="#FF0000">' , '</font>' , $result)."<BR />";
						}
						if(search_string('<p align="center">' , '</p>' , $result) == "請為這篇文章的作者打分"){
							echo "哇,搞掂左!<BR />";
							if(!$got_answer){
								$good_0 = $question0[$tried_user];
								$good_1 = $question1[$tried_user];
								$good_2 = $question2[$j];
								$good_user = $now_user;
							}
							$got_answer = true;
						}
					}
					$j++;
				}
				$tried_user++;
			}else{
				echo $name_arr[$i]." did it... <BR />";
			}
		}else{
			echo "好像有點問題...<BR />除錯用訊息:".$result;
		}
	}
}
if($got_answer){
	echo "哇,搞掂左!<BR />";
	echo "現在去幫其他同學做...這一頁可能會load耐d,請耐心等等!唔好關閉!<BR />";
	$dbuser_handle = mysql_connect($hostname, $username, $password)
	 or die("Unable to connect to MySQL");
	$selected = mysql_select_db($dbname,$dbuser_handle)
	  or die("Could not select examples");
	$result=mysql_query("SELECT * FROM ".$db_table_name." WHERE activated=1");
	while($row = mysql_fetch_array($result)){
		if($row['id']!=$good_user){
			echo "現在開始幫".$row['user']."做...<BR />";
			echo "結果是:".doBatchSubmit($row['school'],$row['id'],$row['pwd'],$good_0,$good_1,$good_2)."<BR />";
		}else{
			echo "跳過".$row['user'].">已經做了<BR />";
		}
		//}
	}
	mysql_close($dbuser_handle);
}
curl_close($af);
curl_close($ch);
?>