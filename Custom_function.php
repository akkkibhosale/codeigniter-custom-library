<?php
/**/
if(!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Custom_function { 
	
	// Convert Number to Word Format
    public function convert_number($number) 
    {
        if (($number < 0) || ($number > 999999999)) 
        {
            throw new Exception("Number is out of range");
        }
        if(!is_numeric($number)){
            throw new Exception("input is String");
        }
        $giga = floor($number / 1000000);
        // Millions (giga)
        $number -= $giga * 1000000;
        $kilo = floor($number / 1000);
        // Thousands (kilo)
        $number -= $kilo * 1000;
        $hecto = floor($number / 100);
        // Hundreds (hecto)
        $number -= $hecto * 100;
        $deca = floor($number / 10);
        // Tens (deca)
        $n = $number % 10;
        // Ones
        $result = "";
        if ($giga) 
        {
            $result .= $this->convert_number($giga) .  "Million";
        }
        if ($kilo) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($kilo) . " Thousand";
        }
        if ($hecto) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($hecto) . " Hundred";
        }
        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        if ($deca || $n) {
            if (!empty($result)) 
            {
                $result .= " and ";
            }
            if ($deca < 2) 
            {
                $result .= $ones[$deca * 10 + $n];
            } else {
                $result .= $tens[$deca];
                if ($n) 
                {
                    $result .= "-" . $ones[$n];
                }
            }
        }
        if (empty($result)) 
        {
            $result = "zero";
        }
        return $result;
    }

    //String Encode ANd DECode

    public function encode_string($string,$encrypt_key = '',$iv = ''){
        if ($string == '') 
        {
            throw new Exception("input is empty");
        }
        if($encrypt_key == '' && $iv == ''):
            return bin2hex(openssl_encrypt($string, 'AES-128-CTR','39100d124e58fb5f2757194251599a0a', 0, substr(hash('sha256', 'INITIALIZATION_VECTOR'), 0, 16)));
        else:
            return bin2hex(openssl_encrypt($string, 'AES-128-CTR',$encrypt_key, 0, substr(hash('sha256', $iv), 0, 16)));
        endif;
       
    }

    public function decode_string($string,$encrypt_key = '',$iv = ''){
        if ($string == '') 
        {
            throw new Exception("input is empty");
        }
        if($encrypt_key == '' && $iv == ''):
            return openssl_decrypt(hex2bin($string), 'AES-128-CTR','39100d124e58fb5f2757194251599a0a', 0, substr(hash('sha256', 'INITIALIZATION_VECTOR'), 0, 16));
        else:
            return openssl_decrypt(hex2bin($string), 'AES-128-CTR', $encrypt_key, 0, substr(hash('sha256', $iv), 0, 16));

        endif;
    }



    //firebase send notification

    function send_notification_firebase($firebasekey,$token,$data){

        if (empty($firebasekey) && empty($token) && empty($data)) :
            throw new Exception("input is empty");
        endif;
        if(is_string($token)):
             throw new Exception("token is string format. Convert its into array format");
        endif;
        $offset = 0;
        $limit =100;
        $count = ceil(count($token)/$limit);
        for($i = 1;$i <= $count;$i++){
            $t=[];
            for($ii = $offset;$ii < $limit;$ii++){
                if(isset($token[$ii])){
                    $t[] = $token[$ii];
                }
            }
            $notification = array(
                "registration_ids" => $token,
                "data" => ['data'=>$data],
                "notification" => $data
            );
        
            $headers = array
            (
                 'Authorization: key=' . FIREBASE_API_KEY, 
                 'Content-Type: application/json'
            );  
            $ch = curl_init();  
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');                                                                 
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($notification));                                                                  
                                                                                                                                 
            // Variable for Print the Result
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close ($ch);
            
            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return $response;
            }
                $offset = $limit;
                $limit = $limit+100;
        }
        
        
    }


    function getRadmonString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
 
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
 
    return $randomString;
}



}

?>