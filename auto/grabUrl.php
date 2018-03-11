<?php
class grabUrl{
	var $toEmail="";
	var $mailSubject="";
	var $mailHeaders="";
	var $addHeaders="";
	var $grabData="";

	function createTo($email){
		$this->toEmail=$email;
	}
	
	function createCC($email){
		$this->mailHeaders.="Cc: $email\r\n";
	}
	
	function createBCC($email){
		$this->mailHeaders.="Bcc: $email\r\n";
	}
	
	function createSubject($sub){
		$this->mailSubject=$sub;
	}
	
	function createFrom($name,$email){
		$this->mailHeaders.="From: '$name' <$email>\r\n";
	}

	function sendMail($charset="iso-8859-1"){
		$this->mailHeaders.="MIME-Version: 1.0\r\n";
		$this->mailHeaders.="Content-type: text/html; charset=$charset\r\n";
		if(mail($this->toEmail,$this->mailSubject,$this->grabData,$this->mailHeaders)){
			return true;
		}else{
			return false;
		}
	}
	
	function getData($url,$use_include_path=0){
		$file = @fopen($url, 'rb', $use_include_path);
		if ($file){
			if ($fsize = @filesize($filename)){
				$data = fread($file, $fsize);
				}else{
					while (!feof($file)){
						$data .= fread($file, 1024);
				}
			}
		fclose($file);
		}
		$this->grabData=$data;
	}
	
	function showPage(){
		echo $this->grabData;
	}
	
	function returnData(){
		return $this->grabData;
	}


}
?>