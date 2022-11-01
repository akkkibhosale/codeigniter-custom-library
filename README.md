# codeigniter-custom-library
add this file into codeigniter project/system/library/

//Controller Code
  $this->load->library('custom_function');
  
  echo $this->custom_function->convert_number(1234);
  
  echo '<br>';
  echo $this->custom_function->encode_string('custom Function');
  
  echo '<br>';
  echo $this->custom_function->decode_string('3473574969706f746d7364694d664c4342495776');
  
  echo '<br>';
  // echo $this->custom_function->send_notification_firebase(($firebasekey,$token,$data);
  
      // $token = array();
      // $data =  array( 
      //     "title" => $data['headings'], 
      //     "body" =>  $data['message'], 
      //     "image" => $data['img'], 
      //     "timestamp" => date('Y-m-d H:i:s'),
      // );
  // echo '<br>';
  echo $this->custom_function->getRadmonString(10);
  echo '<br>';
