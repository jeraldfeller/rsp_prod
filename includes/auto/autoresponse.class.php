<?php
include("grabUrl.php");

// for backward compatibility
if (!function_exists('file_get_contents'))
{
    function file_get_contents($filename, $use_include_path = 0)
    {
        $data = '';
        $file = @fopen($filename, 'rb', $use_include_path);
        if ($file)
        {
            if ($fsize = @filesize($filename))
            {
                $data = fread($file, $fsize);
            }
            else
            {
                while (!feof($file))
                {
                    $data .= fread($file, 1024);
                }
            }
            fclose($file);
        }
        return $data;
    }
}

/***************************************************************************************************************/

class autoresponse{
	
	var $server='';
	var $username='';
	var $password='';
	
	var $mbox='';				/* mailbox resource */
	
	var $responseContent='';		/* content of the response mail */
	var $responseContentSource='';	        /* source to pick up content from */
	var $responseFormat='';  		/* format in which the response mail is sent */
	var $responseType='';			/* type of the mail that is sent out */ 
	var $responseHeaders='';		/* additional headers needed by the response */
	var $responseEmail='';			/* responder email as it appears in the autoresponse mail*/
	

	function autoresponse($username,$password,$responderEmail,$mailserver='localhost',$servertype='pop',$port='110'){
		if($servertype=='imap'){
		/* imap mailbox */
			if($port=='') $port='143';
			$strConnect='{'.$mailserver.':'.$port. '}INBOX'; 
		}else{
		/* pop mailbox */
			$strConnect='{'.$mailserver.':'.$port. '/pop3}INBOX'; 
		}
		$this->server			=	$strConnect;
		$this->username			=	$username;
		$this->password			=	$password;
		$this->responseEmail	=	$responderEmail;
		
	}
	
	function connect(){
		/* connect to the mailbox and make a active link */
		$this->mbox=imap_open($this->server,$this->username,$this->password);
	}
	
	function prepareResponse($content,$type,$format){
		switch($type){
			case 'custom':
				$this->responseContent=$content;
				break;
			case 'url':
				$mc=new grabUrl();
				$mc->getData("$content");
				$this->responseContent=$mc->returnData();
				break;
			case 'file':
				$this->responseContent=file_get_contents($content);
				break;
		}
		$this->responseType=$type;
		$this->responseFormat=$format;
		$this->responseHeaders="From: ".$this->responseEmail."\r\n";
		$this->responseHeaders.="X-Mailer: autoresponse.class.php (autoresponse script by vedanta barooah)\r\n";
		if($format=='html'){
			$this->responseHeaders.="MIME-Version: 1.0\r\n";
			$this->responseHeaders.="Content-type: text/html; charset=iso-8859-1\r\n";
		}
	}
	
	function send($type='custom',$format='html',$deleteMails=false){
		$headers=imap_headers($this->mbox);
		$mail_ids=array();
		for($idx=0,$mid=1;$idx<count($headers);$idx++,$mid++){
			$mail_header=imap_header($this->mbox,$mid);
			$sender=$mail_header->from[0];
			$sender_replyto=$mail_header->reply_to[0];
			if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster'){
				array_push($mail_ids,
					array(
							'to'=>strtolower($sender->mailbox).'@'.$sender->host,
							'toName'=>$sender->personal,
							'to_alt'=>strtolower($sender_replyto->mailbox).'@'.$sender_replyto->host,
							'toName_alt'=>$sender_replyto->personal,
							'subject'=>$mail_header->subject,
							'from'=>strtolower($mail_header->toaddress)
						)
				);
			}
			if($deleteMails) imap_delete($this->mbox,$mid);
		}
		if($type=='url'){
			for($idx=0;$idx<count($mail_ids);$idx++){
				$this->prepareResponse($mail_ids[$idx]['subject'],$type,$format);
				if($mail_ids[$idx]['to_alt']!=''){ $mailTo=$mail_ids[$idx]['to_alt']; }else{ $mailTo=$mail_ids[$idx]['to'];}
				mail($mailTo,"RE: ".$mail_ids[$idx]['subject'],$this->responseContent,$this->responseHeaders);
			}
		}else if($type=='file' || $type=='custom'){
			for($idx=0;$idx<count($mail_ids);$idx++){
				$this->prepareResponse($this->responseContentSource,$type,$format);
				if($mail_ids[$idx]['to_alt']!=''){ $mailTo=$mail_ids[$idx]['to_alt']; }else{ $mailTo=$mail_ids[$idx]['to'];}
				mail($mailTo,"RE: ".$mail_ids[$idx]['subject'],$this->responseContent,$this->responseHeaders);
			}
		}
	}
	
	function close_mailbox(){
		imap_close($this->mbox,CL_EXPUNGE);
	}
}
?>