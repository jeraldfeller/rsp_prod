<?php
class DeferredBilling {
    private $accountId;
    private $accountBalance;
    private $transactions;
    private $credit;
    private $total;

    public function __construct($accountId = 0) {
        global $database;

        $this->accountId = $accountId;
        $this->accountBalance = 0;
        $this->transactions = array();
        $this->total = 0;
        $this->credit = 0;

        $working_credit = 0;

        if ($this->accountId) {
            $query = $database->query("SELECT running_total FROM " . TABLE_ACCOUNTS . " WHERE account_id = '{$this->accountId}'");
            while ($result = $database->fetch_array($query)) {
                $this->accountBalance = $result['running_total'];
            }
        }
        if ($this->accountBalance < 0) {
            $query = $database->query("SELECT * FROM " . TABLE_TRANSACTIONS . " WHERE account_id = '{$this->accountId}' ORDER BY transaction_id DESC");
            while ($result = $database->fetch_array($query)) {
                if ($result['running_total'] >= 0) {
                    // Go back until they were non-negative
                    break;
                } else {
                    $this->transactions[] = $result;
                    // The last credit we calculate will be how much, if any, we started out with
                    $this->credit = $result['running_total'] + $result['amount'];
                    if ($result['amount'] > 0) {
                        $this->total = $this->total + $result['amount'];   
                    } else {
                        $working_credit = $working_credit - $result['amount'];
                    }
                }
            }
        }
        $this->credit = $this->credit + $working_credit;
        $this->total = $this->total - $this->credit;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getCredit() {
        return $this->credit;
    }

    public function getTransactions() {
        return $this->transactions;
    }

    public function summarize() {
        if (count($this->transactions) == 0) {
            return "";
        } elseif (count($this->transactions) == 1) {
            $transaction = $this->transactions[0];
            return "{$transaction['reason']} {$transaction['details']}";
        } else {
            return "Multiple line items";
        }
    }

    public function createSiteHTML($order_total = 0, $transactions_only = false, $fqdn = false, $tense = '') {
        return $this->applyTemplate($order_total, $this->total, $this->credit, $this->transactions, $transactions_only, $fqdn, $tense);
    }
	
	public function createSiteHTMLTwig($order_total = 0, $transactions_only = false, $fqdn = false, $tense = '') {
		return $this->applyTemplateTwig($order_total, $this->total, $this->credit, $this->transactions, $transactions_only, $fqdn, $tense);
	}
	
	public function createSiteHTMLTwigHorizontal($order_total = 0, $transactions_only = false, $fqdn = false, $tense = '') {
		return $this->applyTemplateTwigHorizontal($order_total, $this->total, $this->credit, $this->transactions, $transactions_only, $fqdn, $tense);
	}
	
	public static function applyTemplateTwig($order_total = 0, $deferred_total = 0, $credit = 0, $transactions = array(), $transactions_only = false, $fqdn = '', $tense = '') {	
		
		$html = "<h4 class=\"title-panel\">Deferred Billing</h4>";
		$html.= "<div class=\"panel-library\">";

		foreach ($transactions as $transaction) {

            $amount = number_format($transaction['amount'], 2);

            if ($amount > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"control-label\">".$transaction['reason'].":".$transaction['details']."</label>";
				$html.= "	<div>";
				$html.= "		<span>$".$amount."</span>";
				$html.= "	</div>";
				$html.= "</div>	";
               // $html.= "<tr>\n<td class=\"main\">{$transaction['reason']}: {$transaction['details']}:</td>\n<td class=\"main\">\${$amount}</td>\n</tr>\n";

            }
			
        }
		
		if ($credit > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"control-label\">Credit</label>";
				$html.= "	<div>";
				$html.= "		<span>$-" . number_format($credit, 2) . "</span>";
				$html.= "	</div>";
				$html.= "</div>	";

			}
			
			if (($credit == 0 && count($transactions) > 1) || $credit > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"control-label\">Total Deferred:</label>";
				$html.= "	<div>";
				$html.= "		<span>$" . number_format($deferred_total, 2) . "</span>";
				$html.= "	</div>";
				$html.= "</div>	";

			}
			
			if ($tense == 'past') {
				
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"control-label\">Total Paid:</label>";
				$html.= "	<div>";
				$html.= "		<span>$" . number_format($deferred_total + $order_total, 2) . "</span>";
				$html.= "	</div>";
				$html.= "</div>	";

            //$html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Paid:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";

			} else {

				//$html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Amount Due:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";
				
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"control-label\">Total Amount Due:</label>";
				$html.= "	<div>";
				$html.= "		<span>$" . number_format($deferred_total + $order_total, 2) . "</span>";
				$html.= "	</div>";
				$html.= "</div>	";

			}

		
		$html.="</div>";
		
		return $html;
	
        //return $this->applyTemplate($order_total, $this->total, $this->credit, $this->transactions, $transactions_only, $fqdn, $tense);

    }
	
	public static function applyTemplateTwigHorizontal($order_total = 0, $deferred_total = 0, $credit = 0, $transactions = array(), $transactions_only = false, $fqdn = '', $tense = '') {	
		
		$html = "<h4 class=\"title-panel\">Deferred Billing</h4>";
		$html.= "<div class=\"panel-library\">";

		foreach ($transactions as $transaction) {

            $amount = number_format($transaction['amount'], 2);

            if ($amount > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"col-lg-3 control-label\">".$transaction['reason'].":".$transaction['details']."</label>";
				$html.= "	<div class=\"col-lg-9\">";
				$html.= "		<p class=\"form-control-static\">$".$amount."</p>";
				$html.= "	</div>";
				$html.= "</div>	";
               // $html.= "<tr>\n<td class=\"main\">{$transaction['reason']}: {$transaction['details']}:</td>\n<td class=\"main\">\${$amount}</td>\n</tr>\n";

            }
			
        }
		
		if ($credit > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"col-lg-3 control-label\">Credit</label>";
				$html.= "	<div class=\"col-lg-9\">";
				$html.= "		<p class=\"form-control-static\">$-" . number_format($credit, 2) . "</p>";
				$html.= "	</div>";
				$html.= "</div>	";

			}
			
			if (($credit == 0 && count($transactions) > 1) || $credit > 0) {
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"col-lg-3 control-label\">Total Deferred:</label>";
				$html.= "	<div class=\"col-lg-9\">";
				$html.= "		<p class=\"form-control-static\">$" . number_format($deferred_total, 2) . "</p>";
				$html.= "	</div>";
				$html.= "</div>	";

			}
			
			if ($tense == 'past') {
				
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"col-lg-3 control-label\">Total Paid:</label>";
				$html.= "	<div class=\"col-lg-9\">";
				$html.= "		<p class=\"form-control-static\">$" . number_format($deferred_total + $order_total, 2) . "</p>";
				$html.= "	</div>";
				$html.= "</div>	";

            //$html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Paid:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";

			} else {

				//$html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Amount Due:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";
				
				$html.= "<div class=\"form-group\">";
				$html.= "	<label for=\"street_name\" class=\"col-lg-3 control-label\">Total Amount Due:</label>";
				$html.= "	<div class=\"col-lg-9\">";
				$html.= "		<p class=\"form-control-static\">$" . number_format($deferred_total + $order_total, 2) . "</p>";
				$html.= "	</div>";
				$html.= "</div>	";

			}

		
		$html.="</div>";
		
		return $html;
	
        //return $this->applyTemplate($order_total, $this->total, $this->credit, $this->transactions, $transactions_only, $fqdn, $tense);

    }

    public static function applyTemplate($order_total = 0, $deferred_total = 0, $credit = 0, $transactions = array(), $transactions_only = false, $fqdn = '', $tense = '') {
        $html = "";
        if (!$transactions_only) {
            $html.= "<tr><td height=\"5\"><img src=\"{$fqdn}/images/pixel_trans.gif\" height=\"5\" width=\"1\"></td>\n</tr>\n";
            $html.= "<tr>\n<td class=\"mainLarge\" colspan=\"2\">Deferred Billing</td>\n</tr>\n";
            $html.= "<tr>\n<td height=\"3\"><img src=\"{$fqdn}/images/pixel_trans.gif\" height=\"3\" width=\"1\"></td>\n</tr>\n";
            $html.= "<tr>\n";
            $html.= "<td width=\"100%\">\n";
        }
        $html.= "<table cellspacing=\"0\" cellpadding=\"0\">\n";
        $html.= "<tbody>\n";
        foreach ($transactions as $transaction) {
            $amount = number_format($transaction['amount'], 2);
            if ($amount > 0) {
                $html.= "<tr>\n<td class=\"main\">{$transaction['reason']}: {$transaction['details']}:</td>\n<td class=\"main\">\${$amount}</td>\n</tr>\n";
            }
        }
        if ($credit > 0) {
            $html.= "<tr>\n<td class=\"main\">Credit:</td>\n<td class=\"main\">\$-" . number_format($credit, 2) . "</td>\n</tr>\n";
        }
        $html.= "<tr>\n<td class=\"main\" height=\"5\"><img src=\"{$fqdn}/images/pixel_trans.gif\" height=\"5\" width=\"1\"></td>\n</tr>\n";
        if (($credit == 0 && count($transactions) > 1) || $credit > 0) {
            $html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Deferred:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total, 2) . "</b></td>\n</tr>\n";
        }
        $html.= "<tr>\n<td class=\"main\" height=\"5\"><img src=\"{$fqdn}/images/pixel_trans.gif\" height=\"5\" width=\"1\"></td>\n</tr>\n";
        if ($tense == 'past') {
            $html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Paid:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";
        } else {
            $html.= "<tr>\n<td class=\"main\" width=\"340\"><b>Total Amount Due:</b></td>\n<td class=\"main\"><b>\$" . number_format($deferred_total + $order_total, 2) . "</b></td>\n</tr>\n";
        }
        $html.= "</tbody></table>\n";
        if (!$transactions_only) {
            $html.= "</td></tr>\n";
        }

        return $html;
    }

    public static function applyEmailTemplate($order_total = 0, $deferred_total = 0, $credit = 0, $transactions = array(), $transactions_only = false, $fqdn = '') {
        $html = "";
        if (count($transactions) == 0 && $deferred_total == 0) {
            return $html;
        }
        if (!$transactions_only) {
            $html.= "<tr><td><br /></td></tr>\n";
            $html.= "<tr><td>The following charges were added to this payment:</td></tr>\n";
            $html.= "<tr><td><br /></td></tr>\n";
            $html.= "<tr><td>\n";
        }
        $html.= "<table cellspacing=\"1\" cellpadding=\"1\" width=\"600\">\n";
        $html.= "<tbody>\n";
        foreach ($transactions as $transaction) {
            $amount = number_format($transaction['amount'], 2);
            if ($amount > 0) {
                $html.= "<tr>\n<td width=\"450\"><b>{$transaction['reason']}: {$transaction['details']}</b>:</td>\n<td width=\"150\">\${$amount}</td>\n</tr>\n";
            }
        }
        if ($credit > 0) {
            $html.= "<tr>\n<td width=\"450\"><b>Credit:</b></td>\n<td width=\"150\">\$-" . number_format($credit, 2) . "</td>\n</tr>\n";
        }
        if (($credit == 0 && count($transactions) > 1) || $credit > 0) {
            $html.= "<tr>\n<td width=\"450\"><b>Total Deferred:</b></td>\n<td width=\"150\">\$" . number_format($deferred_total, 2) . "</td>\n</tr>\n";
        }
        $html.= "<tr>\n<td width=\"450\"><b>Total Paid:</b></td>\n<td width=\"150\">\$" . number_format($deferred_total + $order_total, 2) . "</td>\n</tr>\n";
        $html.= "</tbody></table>\n";
        if (!$transactions_only) {
            $html.= "</td></tr>\n";
        }

        return $html;
    }

    public static function applyDeferredEmailTemplate($deferred_total = 0, $credit = 0, $transactions = array(), $fqdn = '') {
        $html = "";
        if (count($transactions) == 0 && $deferred_total == 0) {
            return $html;
        }
        $html.= "<tr><td>\n";
        $html.= "<table cellspacing=\"1\" cellpadding=\"1\" width=\"600\">\n";
        $html.= "<tbody>\n";
        foreach ($transactions as $transaction) {
            $amount = number_format($transaction['amount'], 2);
            if ($amount > 0) {
                $html.= "<tr>\n<td width=\"450\"><b>{$transaction['reason']}: {$transaction['details']}</b>:</td>\n<td width=\"150\">\${$amount}</td>\n</tr>\n";
            }
        }
        if ($credit > 0) {
            $html.= "<tr>\n<td width=\"450\"><b>Credit:</b></td>\n<td width=\"150\">\$-" . number_format($credit, 2) . "</td>\n</tr>\n";
        }
        $html.= "<tr>\n<td width=\"450\"><b>Total Paid:</b></td>\n<td width=\"150\">\$" . number_format($deferred_total, 2) . "</td>\n</tr>\n";
        $html.= "</tbody></table>\n";
        $html.= "</td></tr>\n";

        return $html;
    }
}
?>
