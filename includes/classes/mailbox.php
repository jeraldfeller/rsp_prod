<?
	class Pop3Mailbox {
		var $mbox;


		function Pop3Mailbox($account,$server,$password) {
			//$this->mbox = @imap_open('{' . $server . ':143/imap/notls}INBOX',$account,$password);
			$this->mbox = @imap_open('{localhost:143/imap/notls}INBOX',$account,$password);
		}

		function getHeaders() {
			return @imap_headers($this->mbox);
		}

		function getHeader($msg_id) {
			return @imap_header($this->mbox,$msg_id);
		}

		function getMessage($msg_id) {
			$msg = array();

			$header = @imap_header($this->mbox,$msg_id);

			$msg['subject'] = @$header->subject;
			$msg['from'] = $header->from[0]->mailbox;
			$msg['from_host'] = $header->from[0]->host;
			$msg['to'] = $header->to[0]->mailbox;
			$msg['to_host'] = $header->to[0]->host;
			$msg['date'] = @$header->date;
			$msg['body'] = imap_body($this->mbox,$msg_id);

			return $msg;
		}

		function deleteMessage($msg_id) {
			@imap_delete($this->mbox,$msg_id);
		}

		function close() {
			@imap_close($this->mbox,CL_EXPUNGE);
		}


	}
?>
