<?php
function doBatchSubmit($school,$id,$pwd,$answer0,$answer1,$answer2){
	$reply="";
	$level = 5;
	$ch = curl_init();
	$url="http://prof-ho.appspot.com/login";
	$query="orgNo=".$school."&loginId=".$id."&passwd=".$pwd;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	$af = curl_init();
	curl_setopt($af, CURLOPT_RETURNTRANSFER, 1); 
	
	$result = curl_exec($ch); 
	if(search_string("<title>","</title>", $result,1)){
		if(search_string("<title>","</title>", $result,1) == "failure" ||search_string("<title>","</title>", $result,1) == "A passage A day"){
			$reply.= "登入不能";
			if(search_string('<font color="#FF0000">',"</font>", $result)){
				$reply.= ",".search_string('<font color="#FF0000">',"</font>", $result);
			}
		}elseif(search_string("<title>","</title>", $result,1) == "每日一篇"){
			$hex = search_string("jsessionid=",'"', $result,1);
			$url = "http://www.prof-ho.com/rdcn/student/readArticle.do;jsessionid=".$hex."?series=".$level;
			curl_setopt($af, CURLOPT_URL, $url);
			$result = curl_exec($af);
			
			for($s=0 ; $s<3 ; $s++){
				$n = $s+1;
				$i=1+$s*4;
				$p = 1+$n*4;
				while($i< $p){
					$this_q = search_string('<td align="right" background="../images/tbbg.jpg">','</tr>',$result,$i);
					if(search_string('<input type="radio" name="answer[',']',$this_q) == 0){
						if(search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q) == $answer0){
							$real_answer0 = search_string(']" value="','"',$this_q);
						}
					}
					if(search_string('<input type="radio" name="answer[',']',$this_q) == 1){
						if(search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q) == $answer1){
							$real_answer1 = search_string(']" value="','"',$this_q);
						}
					}
					if(search_string('<input type="radio" name="answer[',']',$this_q) == 2){
						if(search_string('<td align="left" background="../images/tbbg.jpg">','</td>',$this_q) == $answer2){
							$real_answer2 = search_string(']" value="','"',$this_q);
						}
					}
					$i++;
				}
			}
			$url="http://www.prof-ho.com/rdcn/student/replyArticle.do;jsessionid=".$hex;
			$query='series='.$level.'&answer[0]='.$real_answer0.'&answer[1]='.$real_answer1.'&answer[2]='.$real_answer2;
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			$result = curl_exec($ch); 
			if(search_string("<title>" , "</title>" , $result) == "prompt" || search_string("<title>" , "</title>" , $result) == "failure"){
				$reply.= search_string('<font color="#FF0000">' , '</font>' , $result);
			}
			if(search_string('<p align="center">' , '</p>' , $result) == "請為這篇文章的作者打分"){
				$reply.= "搞掂";
			}
			$reply.= "<BR />submitted answer: <BR/>".$answer0.";;".$answer1.";;".$answer2."<BR /> value: <BR />";
			$reply.= $real_answer0.';;'.$real_answer1.';;'.$real_answer2."<BR />";
		}else{
			$reply.= "好像有點問題...<BR />除錯用訊息:".$result;
		}
	}
	curl_close($af);
	curl_close($ch);
	return $reply;
}
?>