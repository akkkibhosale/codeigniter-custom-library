<?php
/**/
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Custom_function { 
	
	function __construct()
	{
		 $this->ci =& get_instance();
		 $this->ci->load->dbforge();
	}
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



function generate_uniqid($table_name,$field)
{
   
    // $this->ci->load->database();
    $query = $this->ci->db->order_by($field,'desc')->limit(1)->get($table_name)->result_array();

    if($query)
    {           
        $query = array_shift($query);           
        $uniqid = $query[$field];
        $temp = $uniqid + 1;
        $uniqid = $temp;        
        
    }
    else
    {
        $uniqid = $prefix.'1';
    }
    return $uniqid;
}

function add_country(){
  
    $this->ci->dbforge->drop_table('list_country',TRUE);
    $fields = array(
        'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'unsigned' => TRUE,
                'auto_increment' => TRUE
        ),
        'iso' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
        ),
        'countryname' => array(
                'type' =>'VARCHAR',
                'constraint' => '255',
                'null' => false,
        ),
        'nicename' => array(
               'type' =>'VARCHAR',
                'constraint' => '255',
                'null' => false,
        ),
        'iso3' => array(
               'type' =>'VARCHAR',
                'constraint' => '255',
                'null' => false,
        ),
        'numcode' => array(
               'type' =>'INT',
                'constraint' => '11',
                'null' => false,
        ),
        'phonecode' => array(
               'type' =>'INT',
               'constraint' => '11',
               'null' => false,
        ),
);
    $this->ci->dbforge->add_field($fields);
    $this->ci->dbforge->add_key('id', TRUE);
    $this->ci->dbforge->create_table('list_country', TRUE);
    
    
    $insert_countries = "INSERT INTO `list_country` (`id`, `iso`, `countryname`, `nicename`, `iso3`, `numcode`, `phonecode`) VALUES
                        (1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93),
                        (2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355),
                        (3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213),
                        (4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684),
                        (5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376),
                        (6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244),
                        (7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264),
                        (8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0),
                        (9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268),
                        (10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54),
                        (11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374),
                        (12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297),
                        (13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61),
                        (14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43),
                        (15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994),
                        (16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242),
                        (17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973),
                        (18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880),
                        (19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246),
                        (20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375),
                        (21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32),
                        (22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501),
                        (23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229),
                        (24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441),
                        (25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975),
                        (26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591),
                        (27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387),
                        (28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267),
                        (29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0),
                        (30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55),
                        (31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246),
                        (32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673),
                        (33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359),
                        (34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226),
                        (35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257),
                        (36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855),
                        (37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237),
                        (38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1),
                        (39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238),
                        (40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345),
                        (41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236),
                        (42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235),
                        (43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56),
                        (44, 'CN', 'CHINA', 'China', 'CHN', 156, 86),
                        (45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61),
                        (46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672),
                        (47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57),
                        (48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269),
                        (49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242),
                        (50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242),
                        (51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682),
                        (52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506),
                        (53, 'CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', 384, 225),
                        (54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385),
                        (55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53),
                        (56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357),
                        (57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420),
                        (58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45),
                        (59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253),
                        (60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767),
                        (61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809),
                        (62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593),
                        (63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20),
                        (64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503),
                        (65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240),
                        (66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291),
                        (67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372),
                        (68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251),
                        (69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500),
                        (70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298),
                        (71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679),
                        (72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358),
                        (73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33),
                        (74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594),
                        (75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689),
                        (76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0),
                        (77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241),
                        (78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220),
                        (79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995),
                        (80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49),
                        (81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233),
                        (82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350),
                        (83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30),
                        (84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299),
                        (85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473),
                        (86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590),
                        (87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671),
                        (88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502),
                        (89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224),
                        (90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245),
                        (91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592),
                        (92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509),
                        (93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0),
                        (94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39),
                        (95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504),
                        (96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852),
                        (97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36),
                        (98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354),
                        (99, 'IN', 'INDIA', 'India', 'IND', 356, 91),
                        (100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62),
                        (101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98),
                        (102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964),
                        (103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353),
                        (104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972),
                        (105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39),
                        (106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876),
                        (107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81),
                        (108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962),
                        (109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7),
                        (110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254),
                        (111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686),
                        (112, 'KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', 408, 850),
                        (113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82),
                        (114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965),
                        (115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996),
                        (116, 'LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', 418, 856),
                        (117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371),
                        (118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961),
                        (119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266),
                        (120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231),
                        (121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218),
                        (122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423),
                        (123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370),
                        (124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352),
                        (125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853),
                        (126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389),
                        (127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261),
                        (128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265),
                        (129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60),
                        (130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960),
                        (131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223),
                        (132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356),
                        (133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692),
                        (134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596),
                        (135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222),
                        (136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230),
                        (137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269),
                        (138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52),
                        (139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691),
                        (140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373),
                        (141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377),
                        (142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976),
                        (143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664),
                        (144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212),
                        (145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258),
                        (146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95),
                        (147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264),
                        (148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674),
                        (149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977),
                        (150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31),
                        (151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599),
                        (152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687),
                        (153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64),
                        (154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505),
                        (155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227),
                        (156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234),
                        (157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683),
                        (158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672),
                        (159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670),
                        (160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47),
                        (161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968),
                        (162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92),
                        (163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680),
                        (164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970),
                        (165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507),
                        (166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675),
                        (167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595),
                        (168, 'PE', 'PERU', 'Peru', 'PER', 604, 51),
                        (169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63),
                        (170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0),
                        (171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48),
                        (172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351),
                        (173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787),
                        (174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974),
                        (175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262),
                        (176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40),
                        (177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70),
                        (178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250),
                        (179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290),
                        (180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869),
                        (181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758),
                        (182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508),
                        (183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784),
                        (184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684),
                        (185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378),
                        (186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239),
                        (187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966),
                        (188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221),
                        (189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381),
                        (190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248),
                        (191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232),
                        (192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65),
                        (193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421),
                        (194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386),
                        (195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677),
                        (196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252),
                        (197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27),
                        (198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0),
                        (199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34),
                        (200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94),
                        (201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249),
                        (202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597),
                        (203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47),
                        (204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268),
                        (205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46),
                        (206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41),
                        (207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963),
                        (208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886),
                        (209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992),
                        (210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255),
                        (211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66),
                        (212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670),
                        (213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228),
                        (214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690),
                        (215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676),
                        (216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868),
                        (217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216),
                        (218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90),
                        (219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370),
                        (220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649),
                        (221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688),
                        (222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256),
                        (223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380),
                        (224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971),
                        (225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44),
                        (226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1),
                        (227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1),
                        (228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598),
                        (229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998),
                        (230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678),
                        (231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58),
                        (232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84),
                        (233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284),
                        (234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340),
                        (235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681),
                        (236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212),
                        (237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967),
                        (238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260),
                        (239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263)";
     $output =  'false';
   
        if($this->ci->db->query($insert_countries)):
            $output =  'true';
        endif;

    return $output;
   
}

function add_state(){
  
    $this->ci->dbforge->drop_table('list_state',TRUE);
    $fields = array(
        'stateid' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'unsigned' => TRUE,
                'auto_increment' => TRUE
        ),
        'statename' => array(
                'type' =>'VARCHAR',
                'constraint' => '255',
                'null' => false,
        ),
        'countryid' => array(
               'type' =>'INT',
                'constraint' => '11',
                'null' => false,
        ),
);
    $this->ci->dbforge->add_field($fields);
    $this->ci->dbforge->add_key('stateid', TRUE);
    $this->ci->dbforge->create_table('list_state', TRUE);
    
    
    $insert_state = "INSERT INTO `list_state` (`stateid`, `statename`, `countryid`) VALUES
                            (1, 'Odisha', 99),
                            (2, 'Andaman and Nicobar', 99),
                            (3, 'Andhra Pradesh', 99),
                            (4, 'Arunachal Pradesh', 99),
                            (5, 'Assam', 99),
                            (6, 'Bihar', 99),
                            (7, 'Chandigarh', 99),
                            (8, 'Chhattisgarh', 99),
                            (9, 'Dadra and Nagar Haveli', 99),
                            (10, 'Daman and Diu', 99),
                            (11, 'Delhi', 99),
                            (12, 'Goa', 99),
                            (13, 'Gujarat', 99),
                            (14, 'Haryana', 99),
                            (15, 'Himachal Pradesh', 99),
                            (16, 'Jammu and Kashmir', 99),
                            (17, 'Jharkhand', 99),
                            (18, 'Karnataka', 99),
                            (19, 'Kerala', 99),
                            (20, 'Lakshadweep', 99),
                            (21, 'Madhya Pradesh', 99),
                            (22, 'Maharashtra', 99),
                            (23, 'Manipur', 99),
                            (24, 'Meghalaya', 99),
                            (25, 'Mizoram', 99),
                            (26, 'Nagaland', 99),
                            (27, 'Puducherry', 99),
                            (28, 'Punjab', 99),
                            (29, 'Rajasthan', 99),
                            (30, 'Sikkim', 99),
                            (31, 'Tamil Nadu', 99),
                            (32, 'Telangana', 99),
                            (33, 'Tripura', 99),
                            (34, 'Uttar Pradesh', 99),
                            (35, 'Uttarakhand', 99),
                            (36, 'West Bengal', 99),
                            (37, 'Ladak', 99);";
     $output =  'false';
   
        if($this->ci->db->query($insert_state)):
            $output =  'true';
        endif;

    return $output;
   
}

function add_district(){
  
    $this->ci->dbforge->drop_table('list_district',TRUE);
    $fields = array(
        'districtid' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'unsigned' => TRUE,
                'auto_increment' => TRUE
        ),
        'districtname' => array(
                'type' =>'VARCHAR',
                'constraint' => '255',
                'null' => false,
        ),
        'stateid' => array(
               'type' =>'INT',
                'constraint' => '11',
                'null' => false,
        ),
);
    $this->ci->dbforge->add_field($fields);
    $this->ci->dbforge->add_key('districtid', TRUE);
    $this->ci->dbforge->create_table('list_district', TRUE);
    
    
    $insert_district = "INSERT INTO `list_district` (`districtid`, `districtname`, `stateid`) VALUES
                        (1, 'Angul', 1),
                        (2, 'Balasore', 1),
                        (3, 'Bargarh', 1),
                        (4, 'Bhadrak', 1),
                        (5, 'Bolangir', 1),
                        (6, 'Boudh', 1),
                        (7, 'Cuttack', 1),
                        (8, 'Deogarh', 1),
                        (9, 'Dhenkanal', 1),
                        (10, 'Gajapati', 1),
                        (11, 'Ganjam', 1),
                        (12, 'Jagatsinghpur', 1),
                        (13, 'Jajpur', 1),
                        (14, 'Jharsuguda', 1),
                        (15, 'Kalahandi', 1),
                        (16, 'Kandhamal', 1),
                        (17, 'Kendrapara', 1),
                        (18, 'Keonjhar', 1),
                        (19, 'Khurdha', 1),
                        (20, 'Koraput', 1),
                        (21, 'Malkangiri', 1),
                        (22, 'Mayurbhanj', 1),
                        (23, 'Nawarangpur', 1),
                        (24, 'Nayagarh', 1),
                        (25, 'Nuapada', 1),
                        (26, 'Puri', 1),
                        (27, 'Rayagada', 1),
                        (28, 'Sambalpur', 1),
                        (29, 'Sonepur', 1),
                        (30, 'Sundergarh', 1),
                        (31, 'Nicobars', 2),
                        (32, 'North and middle a', 2),
                        (33, 'South andamans', 2),
                        (34, 'Anantapur', 3),
                        (35, 'Chittoor', 3),
                        (36, 'East godavari', 3),
                        (37, 'Guntur', 3),
                        (38, 'Krishna', 3),
                        (39, 'Kurnool', 3),
                        (40, 'Prakasam', 3),
                        (41, 'Spsr nellore', 3),
                        (42, 'Srikakulam', 3),
                        (43, 'Visakhapatanam', 3),
                        (44, 'Vizianagaram', 3),
                        (45, 'West godavari', 3),
                        (46, 'Y.s.r.', 3),
                        (47, 'Anjaw', 4),
                        (48, 'Changlang', 4),
                        (49, 'Dibang valley', 4),
                        (50, 'East kameng', 4),
                        (51, 'East siang', 4),
                        (52, 'Kra daadi', 4),
                        (53, 'Kurung kumey', 4),
                        (54, 'Lohit', 4),
                        (55, 'Longding', 4),
                        (56, 'Lower dibang valle', 4),
                        (57, 'Lower subansiri', 4),
                        (58, 'Namsai', 4),
                        (59, 'Papum pare', 4),
                        (60, 'Siang', 4),
                        (61, 'Tawang', 4),
                        (62, 'Tirap', 4),
                        (63, 'Upper siang', 4),
                        (64, 'Upper subansiri', 4),
                        (65, 'West kameng', 4),
                        (66, 'West siang', 4),
                        (67, 'Baksa', 5),
                        (68, 'Barpeta', 5),
                        (69, 'Bongaigaon', 5),
                        (70, 'Cachar', 5),
                        (71, 'Chirang', 5),
                        (72, 'Darrang', 5),
                        (73, 'Dhemaji', 5),
                        (74, 'Dhubri', 5),
                        (75, 'Dibrugarh', 5),
                        (76, 'Dima hasao', 5),
                        (77, 'Goalpara', 5),
                        (78, 'Golaghat', 5),
                        (79, 'Hailakandi', 5),
                        (80, 'Jorhat', 5),
                        (81, 'Kamrup', 5),
                        (82, 'Kamrup metro', 5),
                        (83, 'Karbi anglong', 5),
                        (84, 'Karimganj', 5),
                        (85, 'Kokrajhar', 5),
                        (86, 'Lakhimpur', 5),
                        (87, 'Marigaon', 5),
                        (88, 'Nagaon', 5),
                        (89, 'Nalbari', 5),
                        (90, 'Sivasagar', 5),
                        (91, 'Sonitpur', 5),
                        (92, 'Tinsukia', 5),
                        (93, 'Udalguri', 5),
                        (94, 'Araria', 6),
                        (95, 'Arwal', 6),
                        (96, 'Aurangabad', 6),
                        (97, 'Banka', 6),
                        (98, 'Begusarai', 6),
                        (99, 'Bhagalpur', 6),
                        (100, 'Bhojpur', 6),
                        (101, 'Buxar', 6),
                        (102, 'Darbhanga', 6),
                        (103, 'Gaya', 6),
                        (104, 'Gopalganj', 6),
                        (105, 'Jamui', 6),
                        (106, 'Jehanabad', 6),
                        (107, 'Kaimur (bhabua)', 6),
                        (108, 'Katihar', 6),
                        (109, 'Khagaria', 6),
                        (110, 'Kishanganj', 6),
                        (111, 'Lakhisarai', 6),
                        (112, 'Madhepura', 6),
                        (113, 'Madhubani', 6),
                        (114, 'Munger', 6),
                        (115, 'Muzaffarpur', 6),
                        (116, 'Nalanda', 6),
                        (117, 'Nawada', 6),
                        (118, 'Pashchim champaran', 6),
                        (119, 'Patna', 6),
                        (120, 'Purbi champaran', 6),
                        (121, 'Purnia', 6),
                        (122, 'Rohtas', 6),
                        (123, 'Saharsa', 6),
                        (124, 'Samastipur', 6),
                        (125, 'Saran', 6),
                        (126, 'Sheikhpura', 6),
                        (127, 'Sheohar', 6),
                        (128, 'Sitamarhi', 6),
                        (129, 'Siwan', 6),
                        (130, 'Supaul', 6),
                        (131, 'Vaishali', 6),
                        (132, 'Chandigarh', 7),
                        (133, 'Balod', 8),
                        (134, 'Baloda bazar', 8),
                        (135, 'Balrampur', 8),
                        (136, 'Bastar', 8),
                        (137, 'Bemetara', 8),
                        (138, 'Bijapur', 8),
                        (139, 'Bilaspur', 8),
                        (140, 'Dantewada', 8),
                        (141, 'Dhamtari', 8),
                        (142, 'Durg', 8),
                        (143, 'Gariyaband', 8),
                        (144, 'Janjgir-champa', 8),
                        (145, 'Jashpur', 8),
                        (146, 'Kabirdham', 8),
                        (147, 'Kanker', 8),
                        (148, 'Kondagaon', 8),
                        (149, 'Korba', 8),
                        (150, 'Korea', 8),
                        (151, 'Mahasamund', 8),
                        (152, 'Mungeli', 8),
                        (153, 'Narayanpur', 8),
                        (154, 'Raigarh', 8),
                        (155, 'Raipur', 8),
                        (156, 'Rajnandgaon', 8),
                        (157, 'Sukma', 8),
                        (158, 'Surajpur', 8),
                        (159, 'Surguja', 8),
                        (160, 'Dadra and nagar ha', 9),
                        (161, 'Daman', 10),
                        (162, 'Diu', 10),
                        (163, 'Central', 11),
                        (164, 'East', 11),
                        (165, 'New delhi', 11),
                        (166, 'North', 11),
                        (167, 'North east', 11),
                        (168, 'North west', 11),
                        (169, 'Shahdara', 11),
                        (170, 'South', 11),
                        (171, 'South east', 11),
                        (172, 'South west', 11),
                        (173, 'West', 11),
                        (174, 'North goa', 12),
                        (175, 'South goa', 12),
                        (176, 'Ahmadabad', 13),
                        (177, 'Amreli', 13),
                        (178, 'Anand', 13),
                        (179, 'Arvalli', 13),
                        (180, 'Banas kantha', 13),
                        (181, 'Bharuch', 13),
                        (182, 'Bhavnagar', 13),
                        (183, 'Botad', 13),
                        (184, 'Chhotaudepur', 13),
                        (185, 'Dang', 13),
                        (186, 'Devbhumi dwarka', 13),
                        (187, 'Dohad', 13),
                        (188, 'Gandhinagar', 13),
                        (189, 'Gir somnath', 13),
                        (190, 'Jamnagar', 13),
                        (191, 'Junagadh', 13),
                        (192, 'Kachchh', 13),
                        (193, 'Kheda', 13),
                        (194, 'Mahesana', 13),
                        (195, 'Mahisagar', 13),
                        (196, 'Morbi', 13),
                        (197, 'Narmada', 13),
                        (198, 'Navsari', 13),
                        (199, 'Panch mahals', 13),
                        (200, 'Patan', 13),
                        (201, 'Porbandar', 13),
                        (202, 'Rajkot', 13),
                        (203, 'Sabar kantha', 13),
                        (204, 'Surat', 13),
                        (205, 'Surendranagar', 13),
                        (206, 'Tapi', 13),
                        (207, 'Vadodara', 13),
                        (208, 'Valsad', 13),
                        (209, 'Ambala', 14),
                        (210, 'Bhiwani', 14),
                        (211, 'Charki dadri', 14),
                        (212, 'Faridabad', 14),
                        (213, 'Fatehabad', 14),
                        (214, 'Gurugram', 14),
                        (215, 'Hisar', 14),
                        (216, 'Jhajjar', 14),
                        (217, 'Jind', 14),
                        (218, 'Kaithal', 14),
                        (219, 'Karnal', 14),
                        (220, 'Kurukshetra', 14),
                        (221, 'Mahendragarh', 14),
                        (222, 'Mewat', 14),
                        (223, 'Palwal', 14),
                        (224, 'Panchkula', 14),
                        (225, 'Panipat', 14),
                        (226, 'Rewari', 14),
                        (227, 'Rohtak', 14),
                        (228, 'Sirsa', 14),
                        (229, 'Sonipat', 14),
                        (230, 'Yamunanagar', 14),
                        (231, 'Bilaspur', 15),
                        (232, 'Chamba', 15),
                        (233, 'Hamirpur', 15),
                        (234, 'Kangra', 15),
                        (235, 'Kinnaur', 15),
                        (236, 'Kullu', 15),
                        (237, 'Lahul and spiti', 15),
                        (238, 'Mandi', 15),
                        (239, 'Shimla', 15),
                        (240, 'Sirmaur', 15),
                        (241, 'Solan', 15),
                        (242, 'Una', 15),
                        (243, 'Anantnag', 16),
                        (244, 'Badgam', 16),
                        (245, 'Bandipora', 16),
                        (246, 'Baramulla', 16),
                        (247, 'Doda', 16),
                        (248, 'Ganderbal', 16),
                        (249, 'Jammu', 16),
                        (250, 'Kargil', 16),
                        (251, 'Kathua', 16),
                        (252, 'Kishtwar', 16),
                        (253, 'Kulgam', 16),
                        (254, 'Kupwara', 16),
                        (255, 'Leh ladakh', 16),
                        (256, 'Poonch', 16),
                        (257, 'Pulwama', 16),
                        (258, 'Rajauri', 16),
                        (259, 'Ramban', 16),
                        (260, 'Reasi', 16),
                        (261, 'Samba', 16),
                        (262, 'Shopian', 16),
                        (263, 'Srinagar', 16),
                        (264, 'Udhampur', 16),
                        (265, 'Bokaro', 17),
                        (266, 'Chatra', 17),
                        (267, 'Deoghar', 17),
                        (268, 'Dhanbad', 17),
                        (269, 'Dumka', 17),
                        (270, 'East singhbum', 17),
                        (271, 'Garhwa', 17),
                        (272, 'Giridih', 17),
                        (273, 'Godda', 17),
                        (274, 'Gumla', 17),
                        (275, 'Hazaribagh', 17),
                        (276, 'Jamtara', 17),
                        (277, 'Khunti', 17),
                        (278, 'Koderma', 17),
                        (279, 'Latehar', 17),
                        (280, 'Lohardaga', 17),
                        (281, 'Pakur', 17),
                        (282, 'Palamu', 17),
                        (283, 'Ramgarh', 17),
                        (284, 'Ranchi', 17),
                        (285, 'Sahebganj', 17),
                        (286, 'Saraikela kharsawa', 17),
                        (287, 'Simdega', 17),
                        (288, 'West singhbhum', 17),
                        (289, 'Bagalkot', 18),
                        (290, 'Ballari', 18),
                        (291, 'Belagavi', 18),
                        (292, 'Bengaluru rural', 18),
                        (293, 'Bengaluru urban', 18),
                        (294, 'Bidar', 18),
                        (295, 'Chamarajanagar', 18),
                        (296, 'Chikballapur', 18),
                        (297, 'Chikkamagaluru', 18),
                        (298, 'Chitradurga', 18),
                        (299, 'Dakshin kannad', 18),
                        (300, 'Davangere', 18),
                        (301, 'Dharwad', 18),
                        (302, 'Gadag', 18),
                        (303, 'Hassan', 18),
                        (304, 'Haveri', 18),
                        (305, 'Kalaburagi', 18),
                        (306, 'Kodagu', 18),
                        (307, 'Kolar', 18),
                        (308, 'Koppal', 18),
                        (309, 'Mandya', 18),
                        (310, 'Mysuru', 18),
                        (311, 'Raichur', 18),
                        (312, 'Ramanagara', 18),
                        (313, 'Shivamogga', 18),
                        (314, 'Tumakuru', 18),
                        (315, 'Udupi', 18),
                        (316, 'Uttar kannad', 18),
                        (317, 'Vijayapura', 18),
                        (318, 'Yadgir', 18),
                        (319, 'Alappuzha', 19),
                        (320, 'Ernakulam', 19),
                        (321, 'Idukki', 19),
                        (322, 'Kannur', 19),
                        (323, 'Kasaragod', 19),
                        (324, 'Kollam', 19),
                        (325, 'Kottayam', 19),
                        (326, 'Kozhikode', 19),
                        (327, 'Malappuram', 19),
                        (328, 'Palakkad', 19),
                        (329, 'Pathanamthitta', 19),
                        (330, 'Thiruvananthapuram', 19),
                        (331, 'Thrissur', 19),
                        (332, 'Wayanad', 19),
                        (333, 'Lakshadweep distri', 20),
                        (334, 'Agar malwa', 21),
                        (335, 'Alirajpur', 21),
                        (336, 'Anuppur', 21),
                        (337, 'Ashoknagar', 21),
                        (338, 'Balaghat', 21),
                        (339, 'Barwani', 21),
                        (340, 'Betul', 21),
                        (341, 'Bhind', 21),
                        (342, 'Bhopal', 21),
                        (343, 'Burhanpur', 21),
                        (344, 'Chhatarpur', 21),
                        (345, 'Chhindwara', 21),
                        (346, 'Damoh', 21),
                        (347, 'Datia', 21),
                        (348, 'Dewas', 21),
                        (349, 'Dhar', 21),
                        (350, 'Dindori', 21),
                        (351, 'East nimar', 21),
                        (352, 'Guna', 21),
                        (353, 'Gwalior', 21),
                        (354, 'Harda', 21),
                        (355, 'Hoshangabad', 21),
                        (356, 'Indore', 21),
                        (357, 'Jabalpur', 21),
                        (358, 'Jhabua', 21),
                        (359, 'Katni', 21),
                        (360, 'Khargone', 21),
                        (361, 'Mandla', 21),
                        (362, 'Mandsaur', 21),
                        (363, 'Morena', 21),
                        (364, 'Narsinghpur', 21),
                        (365, 'Neemuch', 21),
                        (366, 'Panna', 21),
                        (367, 'Raisen', 21),
                        (368, 'Rajgarh', 21),
                        (369, 'Ratlam', 21),
                        (370, 'Rewa', 21),
                        (371, 'Sagar', 21),
                        (372, 'Satna', 21),
                        (373, 'Sehore', 21),
                        (374, 'Seoni', 21),
                        (375, 'Shahdol', 21),
                        (376, 'Shajapur', 21),
                        (377, 'Sheopur', 21),
                        (378, 'Shivpuri', 21),
                        (379, 'Sidhi', 21),
                        (380, 'Singrauli', 21),
                        (381, 'Tikamgarh', 21),
                        (382, 'Ujjain', 21),
                        (383, 'Umaria', 21),
                        (384, 'Vidisha', 21),
                        (385, 'Ahmednagar', 22),
                        (386, 'Akola', 22),
                        (387, 'Amravati', 22),
                        (388, 'Aurangabad', 22),
                        (389, 'Beed', 22),
                        (390, 'Bhandara', 22),
                        (391, 'Buldhana', 22),
                        (392, 'Chandrapur', 22),
                        (393, 'Dhule', 22),
                        (394, 'Gadchiroli', 22),
                        (395, 'Gondia', 22),
                        (396, 'Hingoli', 22),
                        (397, 'Jalgaon', 22),
                        (398, 'Jalna', 22),
                        (399, 'Kolhapur', 22),
                        (400, 'Latur', 22),
                        (401, 'Mumbai', 22),
                        (402, 'Mumbai suburban', 22),
                        (403, 'Nagpur', 22),
                        (404, 'Nanded', 22),
                        (405, 'Nandurbar', 22),
                        (406, 'Nashik', 22),
                        (407, 'Osmanabad', 22),
                        (408, 'Palghar', 22),
                        (409, 'Parbhani', 22),
                        (410, 'Pune', 22),
                        (411, 'Raigad', 22),
                        (412, 'Ratnagiri', 22),
                        (413, 'Sangli', 22),
                        (414, 'Satara', 22),
                        (415, 'Sindhudurg', 22),
                        (416, 'Solapur', 22),
                        (417, 'Thane', 22),
                        (418, 'Wardha', 22),
                        (419, 'Washim', 22),
                        (420, 'Yavatmal', 22),
                        (421, 'Bishnupur', 23),
                        (422, 'Chandel', 23),
                        (423, 'Churachandpur', 23),
                        (424, 'Imphal east', 23),
                        (425, 'Imphal west', 23),
                        (426, 'Senapati', 23),
                        (427, 'Tamenglong', 23),
                        (428, 'Thoubal', 23),
                        (429, 'Ukhrul', 23),
                        (430, 'East garo hills', 24),
                        (431, 'East jaintia hills', 24),
                        (432, 'East khasi hills', 24),
                        (433, 'North garo hills', 24),
                        (434, 'Ri bhoi', 24),
                        (435, 'South garo hills', 24),
                        (436, 'South west garo hi', 24),
                        (437, 'South west khasi h', 24),
                        (438, 'West garo hills', 24),
                        (439, 'West jaintia hills', 24),
                        (440, 'West khasi hills', 24),
                        (441, 'Aizawl', 25),
                        (442, 'Champhai', 25),
                        (443, 'Kolasib', 25),
                        (444, 'Lawngtlai', 25),
                        (445, 'Lunglei', 25),
                        (446, 'Mamit', 25),
                        (447, 'Saiha', 25),
                        (448, 'Serchhip', 25),
                        (449, 'Dimapur', 26),
                        (450, 'Kiphire', 26),
                        (451, 'Kohima', 26),
                        (452, 'Longleng', 26),
                        (453, 'Mokokchung', 26),
                        (454, 'Mon', 26),
                        (455, 'Peren', 26),
                        (456, 'Phek', 26),
                        (457, 'Tuensang', 26),
                        (458, 'Wokha', 26),
                        (459, 'Zunheboto', 26),
                        (460, 'Karaikal', 27),
                        (461, 'Mahe', 27),
                        (462, 'Pondicherry', 27),
                        (463, 'Yanam', 27),
                        (464, 'Amritsar', 28),
                        (465, 'Barnala', 28),
                        (466, 'Bathinda', 28),
                        (467, 'Faridkot', 28),
                        (468, 'Fatehgarh sahib', 28),
                        (469, 'Fazilka', 28),
                        (470, 'Firozepur', 28),
                        (471, 'Gurdaspur', 28),
                        (472, 'Hoshiarpur', 28),
                        (473, 'Jalandhar', 28),
                        (474, 'Kapurthala', 28),
                        (475, 'Ludhiana', 28),
                        (476, 'Mansa', 28),
                        (477, 'Moga', 28),
                        (478, 'Nawanshahr', 28),
                        (479, 'Pathankot', 28),
                        (480, 'Patiala', 28),
                        (481, 'Rupnagar', 28),
                        (482, 'Sangrur', 28),
                        (483, 'S.a.s nagar', 28),
                        (484, 'Sri muktsar sahib', 28),
                        (485, 'Tarn taran', 28),
                        (486, 'Ajmer', 29),
                        (487, 'Alwar', 29),
                        (488, 'Banswara', 29),
                        (489, 'Baran', 29),
                        (490, 'Barmer', 29),
                        (491, 'Bharatpur', 29),
                        (492, 'Bhilwara', 29),
                        (493, 'Bikaner', 29),
                        (494, 'Bundi', 29),
                        (495, 'Chittorgarh', 29),
                        (496, 'Churu', 29),
                        (497, 'Dausa', 29),
                        (498, 'Dholpur', 29),
                        (499, 'Dungarpur', 29),
                        (500, 'Ganganagar', 29),
                        (501, 'Hanumangarh', 29),
                        (502, 'Jaipur', 29),
                        (503, 'Jaisalmer', 29),
                        (504, 'Jalore', 29),
                        (505, 'Jhalawar', 29),
                        (506, 'Jhunjhunu', 29),
                        (507, 'Jodhpur', 29),
                        (508, 'Karauli', 29),
                        (509, 'Kota', 29),
                        (510, 'Nagaur', 29),
                        (511, 'Pali', 29),
                        (512, 'Pratapgarh', 29),
                        (513, 'Rajsamand', 29),
                        (514, 'Sawai madhopur', 29),
                        (515, 'Sikar', 29),
                        (516, 'Sirohi', 29),
                        (517, 'Tonk', 29),
                        (518, 'Udaipur', 29),
                        (519, 'East district', 30),
                        (520, 'North district', 30),
                        (521, 'South district', 30),
                        (522, 'West district', 30),
                        (523, 'Ariyalur', 31),
                        (524, 'Chennai', 31),
                        (525, 'Coimbatore', 31),
                        (526, 'Cuddalore', 31),
                        (527, 'Dharmapuri', 31),
                        (528, 'Dindigul', 31),
                        (529, 'Erode', 31),
                        (530, 'Kanchipuram', 31),
                        (531, 'Kanniyakumari', 31),
                        (532, 'Karur', 31),
                        (533, 'Krishnagiri', 31),
                        (534, 'Madurai', 31),
                        (535, 'Nagapattinam', 31),
                        (536, 'Namakkal', 31),
                        (537, 'Perambalur', 31),
                        (538, 'Pudukkottai', 31),
                        (539, 'Ramanathapuram', 31),
                        (540, 'Salem', 31),
                        (541, 'Sivaganga', 31),
                        (542, 'Thanjavur', 31),
                        (543, 'Theni', 31),
                        (544, 'The nilgiris', 31),
                        (545, 'Thiruvallur', 31),
                        (546, 'Thiruvarur', 31),
                        (547, 'Tiruchirappalli', 31),
                        (548, 'Tirunelveli', 31),
                        (549, 'Tiruppur', 31),
                        (550, 'Tiruvannamalai', 31),
                        (551, 'Tuticorin', 31),
                        (552, 'Vellore', 31),
                        (553, 'Villupuram', 31),
                        (554, 'Virudhunagar', 31),
                        (555, 'Adilabad', 32),
                        (556, 'Bhadradri', 32),
                        (557, 'Hyderabad', 32),
                        (558, 'Jagitial', 32),
                        (559, 'Jangoan', 32),
                        (560, 'Jayashankar', 32),
                        (561, 'Jogulamba', 32),
                        (562, 'Kamareddy', 32),
                        (563, 'Karimnagar', 32),
                        (564, 'Khammam', 32),
                        (565, 'Komaram bheem asif', 32),
                        (566, 'Mahabubabad', 32),
                        (567, 'Mahbubnagar', 32),
                        (568, 'Mancherial', 32),
                        (569, 'Medak', 32),
                        (570, 'Medchal', 32),
                        (571, 'Nagarkurnool', 32),
                        (572, 'Nalgonda', 32),
                        (573, 'Nirmal', 32),
                        (574, 'Nizamabad', 32),
                        (575, 'Peddapalli', 32),
                        (576, 'Rajanna', 32),
                        (577, 'Rangareddi', 32),
                        (578, 'Sangareddy', 32),
                        (579, 'Siddipet', 32),
                        (580, 'Suryapet', 32),
                        (581, 'Vikarabad', 32),
                        (582, 'Wanaparthy', 32),
                        (583, 'Warangal', 32),
                        (584, 'Warangal urban', 32),
                        (585, 'Yadadri', 32),
                        (586, 'Dhalai', 33),
                        (587, 'Gomati', 33),
                        (588, 'Khowai', 33),
                        (589, 'North tripura', 33),
                        (590, 'Sepahijala', 33),
                        (591, 'South tripura', 33),
                        (592, 'Unakoti', 33),
                        (593, 'West tripura', 33),
                        (607, 'Agra', 34),
                        (608, 'Aligarh', 34),
                        (609, 'Allahabad', 34),
                        (610, 'Ambedkar nagar', 34),
                        (611, 'Amethi', 34),
                        (612, 'Amroha', 34),
                        (613, 'Auraiya', 34),
                        (614, 'Azamgarh', 34),
                        (615, 'Baghpat', 34),
                        (616, 'Bahraich', 34),
                        (617, 'Ballia', 34),
                        (618, 'Balrampur', 34),
                        (619, 'Banda', 34),
                        (620, 'Barabanki', 34),
                        (621, 'Bareilly', 34),
                        (622, 'Basti', 34),
                        (623, 'Bhadohi', 34),
                        (624, 'Bijnor', 34),
                        (625, 'Budaun', 34),
                        (626, 'Bulandshahr', 34),
                        (627, 'Chandauli', 34),
                        (628, 'Chitrakoot', 34),
                        (629, 'Deoria', 34),
                        (630, 'Etah', 34),
                        (631, 'Etawah', 34),
                        (632, 'Faizabad', 34),
                        (633, 'Farrukhabad', 34),
                        (634, 'Fatehpur', 34),
                        (635, 'Firozabad', 34),
                        (636, 'Gautam buddha naga', 34),
                        (637, 'Ghaziabad', 34),
                        (638, 'Ghazipur', 34),
                        (639, 'Gonda', 34),
                        (640, 'Gorakhpur', 34),
                        (641, 'Hamirpur', 34),
                        (642, 'Hapur', 34),
                        (643, 'Hardoi', 34),
                        (644, 'Hathras', 34),
                        (645, 'Jalaun', 34),
                        (646, 'Jaunpur', 34),
                        (647, 'Jhansi', 34),
                        (648, 'Kannauj', 34),
                        (649, 'Kanpur dehat', 34),
                        (650, 'Kanpur nagar', 34),
                        (651, 'Kasganj', 34),
                        (652, 'Kaushambi', 34),
                        (653, 'Kheri', 34),
                        (654, 'Kushi nagar', 34),
                        (655, 'Lalitpur', 34),
                        (656, 'Lucknow', 34),
                        (657, 'Maharajganj', 34),
                        (658, 'Mahoba', 34),
                        (659, 'Mainpuri', 34),
                        (660, 'Mathura', 34),
                        (661, 'Mau', 34),
                        (662, 'Meerut', 34),
                        (663, 'Mirzapur', 34),
                        (664, 'Moradabad', 34),
                        (665, 'Muzaffarnagar', 34),
                        (666, 'Pilibhit', 34),
                        (667, 'Pratapgarh', 34),
                        (668, 'Rae bareli', 34),
                        (669, 'Rampur', 34),
                        (670, 'Saharanpur', 34),
                        (671, 'Sambhal', 34),
                        (672, 'Sant kabeer nagar', 34),
                        (673, 'Shahjahanpur', 34),
                        (674, 'Shamli', 34),
                        (675, 'Shravasti', 34),
                        (676, 'Siddharth nagar', 34),
                        (677, 'Sitapur', 34),
                        (678, 'Sonbhadra', 34),
                        (679, 'Sultanpur', 34),
                        (680, 'Unnao', 34),
                        (681, 'Varanasi', 34),
                        (594, 'Almora', 35),
                        (595, 'Bageshwar', 35),
                        (596, 'Chamoli', 35),
                        (597, 'Champawat', 35),
                        (598, 'Dehradun', 35),
                        (599, 'Haridwar', 35),
                        (600, 'Nainital', 35),
                        (601, 'Pauri garhwal', 35),
                        (602, 'Pithoragarh', 35),
                        (603, 'Rudra prayag', 35),
                        (604, 'Tehri garhwal', 35),
                        (605, 'Udam singh nagar', 35),
                        (606, 'Uttar kashi', 35),
                        (682, '24 paraganas north', 36),
                        (683, '24 paraganas south', 36),
                        (684, 'Alipurduar', 36),
                        (685, 'Bankura', 36),
                        (686, 'Bardhaman', 36),
                        (687, 'Birbhum', 36),
                        (688, 'Coochbehar', 36),
                        (689, 'Darjeeling', 36),
                        (690, 'Dinajpur dakshin', 36),
                        (691, 'Dinajpur uttar', 36),
                        (692, 'Hooghly', 36),
                        (693, 'Howrah', 36),
                        (694, 'Jalpaiguri', 36),
                        (695, 'Kolkata', 36),
                        (696, 'Maldah', 36),
                        (697, 'Medinipur east', 36),
                        (698, 'Medinipur west', 36),
                        (699, 'Murshidabad', 36),
                        (700, 'Nadia', 36),
                        (701, 'Purulia', 36)";
     $output =  'false';
   
        if($this->ci->db->query($insert_district)):
            $output =  'true';
        endif;

    return $output;
   
}


    static function uploadFileintoFolder($user_id, $image, $folder,$image_type)
    {
        $fileName = $user_id;
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $filePath = $folder . "/" . $fileName . '.png';
        if($image_type == 'file'):
             move_uploaded_file($image['tmp_name'], $filePath);
        else:
            $file = base64_decode($image);
            file_put_contents($filePath, $file);
        endif;
        return $filePath;

    }
    
    static function DeleteFileintoFolder($path,$filename)
    {
        if (file_exists($path.'/'.$filename)):
            unlink($path.'/'.$filename);
            return 'file deleted';
        else:
             return 'file not found';
        endif;
    }



}

?>
