<?php
// Updated 1/28/2012 brad@brgr2.com Added shorter CURL timeout when checking address.
class zip4 {

    var $address2;
    var $state;
    var $city;
    var $visited;
    var $zip;
    var $pagenumber;
    var $firmname;
    var $urbanization;
    var $data;
    var $result_zip_code;
    var $fail_type;

    function __construct($address2 = '', $state = '', $city = '', $zip = '') {
        $this->address2 = $address2;
        $this->state = $state;
        $this->city = $city;
        $this->zip = $zip;
        $this->data = 'resultMode=0&companyName=&address2=&address1=' . str_replace(' ', '+', strtoupper($address2)) . '&city=' . strtoupper($city) . '&state=' . strtoupper($state) . '&urbanCode=&postalCode=0&zip=' . $zip;
    }

    function search() {

        $ch = curl_init();
        // brad@brgr2.com
        curl_setopt_array($ch, array(
            CURLOPT_URL => "https://tools.usps.com/go/ZipLookupResultsAction!input.action",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->data,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 1, // in seconds
            CURLOPT_TIMEOUT => 2 // in seconds
        ));

        $result = null;
        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) {
            $this->fail_type = 'network';
            return false;
        }

        if (strpos($result, 'more than one address') !== false) {
            $more_than_one = true;
        } else {
            $more_than_one = false;
        }
        if (strpos($result, 'Non Deliverable</div>') !== false) {
            $non_deliverable = true;
        } else {
            $non_deliverable = false;
        }

//            $result = str_replace(array('"', ' ', "\n", "\n\r", "\n\n", "\r\r", '
//            '), '', $result);

        $start = 0;
        $result = $this->strip_html_tags($result);
        if (stripos($result, $this->zip))
            $start = stripos($result, $this->zip, stripos($result, $this->zip) + 1);

        if ($start > 0) {
            // beware embedded html that remains + 4 ?!?
            $this->result_zip_code = rtrim(substr($result, $start, 5) . "-" . substr($result, $start + 6, 4));
            if (stripos($result, 'not recognized')) {
                $this->result_zip_code = $this->result_zip_code . "9999";
            }
            $this->fail_type = '';
            return true;
        } else {
            $this->result_zip_code = $this->extract($result);
            if ($this->result_zip_code) {
                $this->fail_type = '';
                return true;
            } elseif (stripos($result, 'not recognized')) {
                $this->result_zip_code = $this->result_zip_code . "9999";
                $this->fail_type = '';
                return true;
            } elseif (stripos($result, 'Non Deliverable')) {
                $this->fail_type = 'none';
                return false;
            } elseif ('more than one address') {
                $this->fail_type = 'multiple';
                return false;
            } else {
                $this->fail_type = 'unknown';
                return false;
            }
        }
        return false;
    }

    function return_fail_type() {
        return $this->fail_type;
    }

    function return_zip_code() {
// echo "2this result_zip_code".$this->result_zip_code;
        return $this->result_zip_code;
    }

    function strip_html_tags($text) {
        $text = preg_replace(
                array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
                ), array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', "$0", "$0", "$0", "$0", "$0", "$0", "$0", "$0",), $text);
        $text = strip_tags($text, '<b><a>');
        $text = html_entity_decode($text);
        $start = strpos($text, USPS_BEGIN);
        $end = strpos($text, USPS_END);
        return rtrim(substr($text, $start - 1, $end - $start));
    }

    function extract($text) {
        $zip4 = 4;
        $zipstr = "";
        $zip5 = 0;
        for ($x = strlen($text); $x >= 0; $x--) {
            if (is_numeric(substr($text, $x, 1))) {
                if ($zip4 > 0) {
                    $zipstr = substr($text, $x, 1) . $zipstr;
                    $zip4 -= 1;
                    if ($zip4 == 0) {
                        $zipstr = "-" . $zipstr;
                        $zip5 = 5;
                    }
                } else {
                    if ($zip5 > 0) {
                        $zipstr = substr($text, $x, 1) . $zipstr;
                        $zip5 -= 1;
                        if ($zip5 == 0) {
                            $this->zip = $zip5;
                            $x = -1;
                        }
                    }
                }
            } else {
                if (substr($text, $x, 1) != "-") {
                    $zip4 = 4;
                    $zipstr = "";
                    $zip5 = 0;
                }
            }
        }
        return $zipstr;
    }

}

?>