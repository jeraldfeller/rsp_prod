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
    var $fail_description;
    var $stringXml;
    var $uspsKey;

    function __construct($address2 = '', $state = '', $city = '', $zip = '', $uspsUseId = '') {
        $this->address2 = urlencode($address2);
        $this->state = $state;
        $this->city = urlencode($city);
        $this->zip = $zip;
        //$this->data = 'resultMode=0&companyName=&address2=&address1=' . str_replace(' ', '+', strtoupper($address2)) . '&city=' . strtoupper($city) . '&state=' . strtoupper($state) . '&urbanCode=&postalCode=0&zip=' . $zip;


        $this->uspsKey = $uspsUseId;
        $this->stringXml = "http://production.shippingapis.com/ShippingAPITest.dll?API=Verify%20&XML=%3CAddressValidateRequest%20USERID=%22$this->uspsKey%22%3E%3CAddress%20ID=%221%22%3E%3CAddress1%3E%20%3C/Address1%3E%3CAddress2%3E$this->address2%3C/Address2%3E%3CCity%3E$this->city%3C/City%3E%20%3CState%3E$this->state%3C/State%3E%3CZip5%3E$this->zip%3C/Zip5%3E%3CZip4%3E%3C/Zip4%3E%3C/Address%3E%20%3C/AddressValidateRequest%3E";
    }

    function search() {



        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->stringXml,
            CURLOPT_HTTPHEADER => array(
            ),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_VERBOSE => 0
        ));
        // Send the request & save response to $resp
        $result = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        $result = simplexml_load_string($result);
        $result = json_encode($result);
        $result = json_decode($result, true);


        if(!$result){
            $this->fail_type = 'network';
            $this->fail_description = '';
            return false;
        }

        if(isset($result['Address']['Error'])){
            $this->fail_type = 'invalid';
            $this->fail_description = $result['Address']['Error']['Description'];
            return false;
        }

        $returnAddress = $result['Address']['Address2'];
        $returnCity = $result['Address']['City'];
        $returnState = $result['Address']['State'];
        $returnZip5 = $result['Address']['Zip5'];
        $returnZip4 = $result['Address']['Zip4'];

        if($returnCity != strtoupper($this->city) || $returnState != strtoupper($this->state) || $returnZip5 != $this->zip){
            $this->fail_type = 'mismatch';
            $this->fail_description = '';
            return false;
        }else{
            $this->result_zip_code = $returnZip5.'-'.$returnZip4;
            return true;
        }

    }

    function return_fail_type() {
        return $this->fail_type;
    }

    function return_fail_description(){
        return $this->fail_description;
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