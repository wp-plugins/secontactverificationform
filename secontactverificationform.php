<?php
/* 
Plugin Name: SEContactVerificationForm
Plugin URI: http://www.isms.com.my/
Description: A SMS and email contact form SMS phone verification function. Install and add [secontactverificationform] to any pages or post!
Version: 1.0.4
Author: H.P.Ang
Author URI: http://www.isms.com.my/
License: GPL
*/

if(!isset($_SESSION)){
	
	session_start();
}

function ismscURL_verify($link){
	
	$http = curl_init($link);
	// do your curl thing here
	curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE); 
	$http_result = curl_exec($http);
	$http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
	curl_close($http);
	
	return $http_result;
}

function generate_custom_fieldtype_verify($name, $type){
	switch($type){
		case "field":{
	
			return '<input type="text" name="'.$name.'" id="'.$name.'" />';
			break;
		}
		case "textbox":{
		
			return '<textarea name="'.$name.'" id="'.$name.'" cols="45" rows="5"></textarea>';
			break;
		}
	}
}

function generate_custom_select_option_verify($name, $options){
	
	$options_arr = explode(",", $options);
	
	$return = '<select name="'.$name.'" id="'.$name.'">';
	
	foreach($options_arr as $value){
		
		$return .= '<option value="'.$value.'">'.$value.'</option>';
	}
	
	$return .= '</select>';
	
	return $return;
}

function contactform_func_verify($atts, $content){
  global $post;
  
  //$outputData='<link rel="stylesheet" type="text/css" media="all" href="'.WP_PLUGIN_URL.'/secontactform/css/style.css" />';
  $outputData='<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/secontactverificationform/include/contactform.css">
			<script src="'.WP_PLUGIN_URL.'/secontactverificationform/include/contactform.js" type="text/javascript"></script>
			<script>
			  function clickToValidate(phone){
				var $j = jQuery.noConflict();
				if(phone.length==0 || phone=="0"){
				alert("Please enter phone number!");
				return false;
				}else{
				document.form_isms_contact.imobilephone.disabled=true;
				document.form_isms_contact.iverifybutton.disabled=true;
				$j.getJSON("https://www.isms.com.my/api_sms_verification.php?a='.get_option('isms_verify_username').'&b='.get_option('isms_verify_password').'&c=1&phone="+phone+"&callback=?",function(result){
				  document.getElementById(\'ResultVerify\').innerHTML=result;
				  return true;
				});
				}
			  }
			    function verifyMyPhone(phone,pin){  
				var $j = jQuery.noConflict();
				if(pin.length > 0){
					//alert("http://www.isms.com.my/api_sms_verification.php?url=Y&pin="+pin+"&a='.get_option('isms_verify_username').'&b='.get_option('isms_verify_password').'&phone="+phone+"&callback=?");
					 $j.getJSON("http://www.isms.com.my/api_sms_verification.php?url=Y&pin="+pin+"&a='.get_option('isms_verify_username').'&b='.get_option('isms_verify_password').'&phone="+phone+"&callback=?",function(result){
					if (result == "SUCCESS"){
					  document.getElementById(\'ResultVerifyPhone\').innerHTML="VERIFIED";
					  document.form_isms_contact.verify.value="1";
					  document.form_isms_contact.verify_my_phone.disabled=true;
						return true;
					}else{
					  document.getElementById(\'ResultVerifyPhone\').innerHTML="INVALID";  
						return true;
					}
					});
				}
			  }
			</script>';
  $permalink = get_permalink($post->ID);
  
  if($error = $_GET["demoform_error"]){
    //$outputData .= "Error processing submission<br>";
    $outputData .= "<div class='box box-error' style='font-size:13px;'>".$_SESSION['error_text']."</div>";
  }elseif($success = $_GET["demoform_success"]){
    if (get_option('form_success_msg')){
      $outputData .= "<div class='box box-success' style='font-size:13px;'>".get_option('form_success_msg')."</div>";
    }else{
      $outputData .= "<div class='box box-success' style='font-size:13px;'>Thank you for your submission.</div>";
    }
  }
  
  $outputData .='<form id="form_isms_contact" name="form_isms_contact" method="post" action="'.$permalink.'">';
  $outputData .='<fieldset class="bmostyle">';
  $outputData .='<legend>'.(get_option('form_title')!=""?get_option('form_title'):"Contact form").'</legend>';
  
  if(get_option('isms_name')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_name_required')=="1"?"<input type='hidden' id='isms_name_required' value='1'>* ":"").'Full Name</label></div>
				<div class="list01-right"><input type="text" name="iname" id="iname"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_first_name')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_first_name_required')=="1"?"<input type='hidden' id='isms_first_name_required' value='1'>* ":"").'First Name</label></div>
				<div class="list01-right"><input type="text" name="ifirstname" id="ifirstname"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_last_name')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_last_name_required')=="1"?"<input type='hidden' id='isms_last_name_required' value='1'>* ":"").'Last Name</label></div>
				<div class="list01-right"><input type="text" name="ilastname" id="ilastname"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_mobile_phone')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_mobile_phone_required')=="1"?"<input type='hidden' id='isms_mobile_phone_required' value='1'>* ":"").'Mobile Phone</label></div>
				<div class="list01-right">
					<input type="text" name="imobilephone" id="imobilephone"/>
					'.(get_option('isms_verify_enable')=="1"?'<div id="ResultVerify"><input type="button" name="iverifybutton" id="iverifybutton" value="Verification required, click here" style="padding-bottom: 5px; width: 200px;" onclick="clickToValidate(document.form_isms_contact.imobilephone.value);"/></div>':'').'
				</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_email')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_email_required')=="1"?"<input type='hidden' id='isms_email_required' value='1'>* ":"").'Email</label></div>
				<div class="list01-right"><input type="text" name="iemail" id="iemail"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_reemail')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_reemail_required')=="1"?"<input type='hidden' id='isms_reemail_required' value='1'>* ":"").'Reconfirm Email</label></div>
				<div class="list01-right"><input type="text" name="ireemail" id="ireemail"/></div>
			<div class="clear"></div></div>';
  } 
  if(get_option('isms_address')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_address_required')=="1"?"<input type='hidden' id='isms_address_required' value='1'>* ":"").'Address</label></div>
				<div class="list01-right"><input type="text" name="iaddress" id="iaddress"/></div>
			<div class="clear"></div></div>';
  }   
  if(get_option('isms_country')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_country_required')=="1"?"<input type='hidden' id='isms_country_required' value='1'>* ":"").'Country</label></div>
				<div class="list01-right">
					<select name="icountry" id="icountry">
						<option value="">--Please select--</option>
						'.list_of_countries_verify().'
					</select>
				</div>
			<div class="clear"></div></div>';
  }     
  if(get_option('isms_passport_no')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_passport_no_required')=="1"?"<input type='hidden' id='isms_passport_no_required' value='1'>* ":"").'Passport No.</label></div>
				<div class="list01-right"><input type="text" name="ipassport" id="ipassport"/></div>
			<div class="clear"></div></div>';
  }       
  if(get_option('isms_social_security_no')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_social_security_no_required')=="1"?"<input type='hidden' id='isms_social_security_no_required' value='1'>* ":"").'Social Security No.</label></div>
				<div class="list01-right"><input type="text" name="isocial_security_no" id="isocial_security_no"/></div>
			<div class="clear"></div></div>';
  }       
  if(get_option('isms_dob')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_dob_required')=="1"?"<input type='hidden' id='isms_dob_required' value='1'>* ":"").'Date of Birth</label></div>
				<div class="list01-right"><input type="text" name="idob" id="idob"/>(dd/mm/yyyy)</div>
			<div class="clear"></div></div>';
  }         
  if(get_option('isms_company')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_company_required')=="1"?"<input type='hidden' id='isms_company_required' value='1'>* ":"").'Company</label></div>
				<div class="list01-right"><input type="text" name="icompany" id="icompany"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_product')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_product_required')=="1"?"<input type='hidden' id='isms_product_required' value='1'>* ":"").'Product</label></div>
				<div class="list01-right"><input type="text" name="iproduct" id="iproduct"/></div>
			<div class="clear"></div></div>';
  }  
  if(get_option('isms_website')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_website_required')=="1"?"<input type='hidden' id='isms_website_required' value='1'>* ":"").'Website</label></div>
				<div class="list01-right"><input type="text" name="iwebsite" id="iwebsite"/></div>
			<div class="clear"></div></div>';
  }   

  if(get_option('isms_custom1')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom1_required')=="1"?"<input type='hidden' id='isms_custom1_required' value='1'>* ":"").get_option('isms_custom_name1').'</label></div>
				<div class="list01-right">'.generate_custom_fieldtype_verify("cisms_custom_name1", get_option('isms_custom_type1')).'</div>
			<div class="clear"></div></div>';
  }   
  if(get_option('isms_custom2')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom2_required')=="1"?"<input type='hidden' id='isms_custom2_required' value='1'>* ":"").get_option('isms_custom_name2').'</label></div>
				<div class="list01-right">'.generate_custom_fieldtype_verify("cisms_custom_name2", get_option('isms_custom_type2')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_custom3')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom3_required')=="1"?"<input type='hidden' id='isms_custom3_required' value='1'>* ":"").get_option('isms_custom_name3').'</label></div>
				<div class="list01-right">'.generate_custom_fieldtype_verify("cisms_custom_name3", get_option('isms_custom_type3')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_custom4')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom4_required')=="1"?"<input type='hidden' id='isms_custom4_required' value='1'>* ":"").get_option('isms_custom_name4').'</label></div>
				<div class="list01-right">'.generate_custom_fieldtype_verify("cisms_custom_name4", get_option('isms_custom_type4')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_custom5')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom5_required')=="1"?"<input type='hidden' id='isms_custom5_required' value='1'>* ":"").get_option('isms_custom_name5').'</label></div>
				<div class="list01-right">'.generate_custom_fieldtype_verify("cisms_custom_name5", get_option('isms_custom_type5')).'</div>
			<div class="clear"></div></div>';
  }

  if(get_option('isms_custom_select1')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom_select1_required')=="1"?"<input type='hidden' id='isms_custom_select1_required' value='1'>* ":"").get_option('isms_custom_select_name1').'</label></div>
				<div class="list01-right">'.generate_custom_select_option_verify("cisms_custom_select_name1", get_option('isms_custom_select_option1')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_custom_select2')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom_select2_required')=="1"?"<input type='hidden' id='isms_custom_select2_required' value='1'>* ":"").get_option('isms_custom_select_name2').'</label></div>
				<div class="list01-right">'.generate_custom_select_option_verify("cisms_custom_select_name2", get_option('isms_custom_select_option2')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_custom_select3')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_custom_select3_required')=="1"?"<input type='hidden' id='isms_custom_select3_required' value='1'>* ":"").get_option('isms_custom_select_name3').'</label></div>
				<div class="list01-right">'.generate_custom_select_option_verify("cisms_custom_select_name3", get_option('isms_custom_select_option3')).'</div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_subject')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_subject_required')=="1"?"<input type='hidden' id='isms_subject_required' value='1'>* ":"").'Subject</label></div>
				<div class="list01-right"><input type="text" name="isubject" id="isubject"/></div>
			<div class="clear"></div></div>';
  }
  if(get_option('isms_message')=="1"){
    $outputData .='<div class="list01">
				<div class="list01-left"><label>'.(get_option('isms_message_required')=="1"?"<input type='hidden' id='isms_message_required' value='1'>* ":"").'Message</label></div>
				<div class="list01-right"><textarea name="imessage" id="imessage" cols="55" rows="5"></textarea></div>
			<div class="clear"></div></div>';
  }

  if(get_option('isms_captcha')=="1"){
    $outputData .='<div class="list01"><div class="list01-left"><label>&nbsp;</label></div><div class="list01-right"><img src="'.WP_PLUGIN_URL.'/secontactverificationform/include/captcha.php" id="captcha" /></div><div class="clear"></div></div>';
    $outputData .='<div class="list01">
				<div class="list01-left"><label>Captcha</label></div>
				<div class="list01-right"><input type="text" name="captchatxt" id="captchatxt" /></div>
			<div class="clear"></div></div>';
  }
  
  $outputData .='<div class="list01">
				<div class="list01-left"></div>
				<div class="list01-right"><input type="submit" value="Submit" onclick="return validate();"/></div>
			<div class="clear"></div></div>';
  $outputData .='</fieldset>'; 
  $outputData .= wp_nonce_field("cfn","contact_form_verify_nonce");
  $outputData .='</form>'; 

  return $outputData;
}
//  function my_scripts_method() {
//    wp_deregister_script( 'jquery' );
//    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
//    wp_enqueue_script( 'jquery' );
//}    
// 
//add_action('wp_enqueue_scripts', 'my_scripts_method');
  
function handle_contact_post_verify() {
  global $post;
  $r = preg_replace("/[\?&]*(demoform_success|demoform_error)=1/is", "", $_SERVER['HTTP_REFERER']);
  //echo "here:".$_POST["contact_form_nonce"];
  
  if($nonce = $_POST["contact_form_verify_nonce"]){
    if(wp_verify_nonce($nonce, "cfn") ){
      $captcha_valid = 1;
      $no_error = 1;
      $_SESSION['error_text'] = "";
      if(get_option('isms_captcha')=="1"){
        if($_POST['captchatxt'] != $_SESSION['captcha']){
          $captcha_valid = 0;
          $_SESSION['error_text'] .= "Invalid captcha entered.<br>";
        }
      }
      
      if(get_option('isms_name')=="1" && get_option('isms_name_required')=="1"){
        if($_POST['iname'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your name.<br>";
        }
      }
      
      if(get_option('isms_first_name')=="1" && get_option('isms_first_name_required')=="1"){
        if($_POST['ifirstname'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your first name.<br>";
        }
      }
      
      if(get_option('isms_last_name')=="1" && get_option('isms_last_name_required')=="1"){
        if($_POST['ilastname'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your last name.<br>";
        }
      }
      
      if(get_option('isms_mobile_phone')=="1" && get_option('isms_mobile_phone_required')=="1"){
        if($_POST['imobilephone'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your mobile number.<br>";
        }
      }
      
      if(get_option('isms_address')=="1" && get_option('isms_address_required')=="1"){
        if($_POST['iaddress'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your address.<br>";
        }
      }
      
      if(get_option('isms_country')=="1" && get_option('isms_country_required')=="1"){
        if($_POST['icountry'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in the country.<br>";
        }
      }
      
      if(get_option('isms_subject')=="1" && get_option('isms_subject_required')=="1"){
        if($_POST['isubject'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in the subject.<br>";
        }
      }
      
      if(get_option('isms_email')=="1" && get_option('isms_email_required')=="1"){
        if($_POST['iemail'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your email.<br>";
        }
      }
      
      if(get_option('isms_reemail')=="1" && get_option('isms_reemail_required')=="1"){
        if($_POST['ireemail'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please reconfirm your email.<br>";
        }
      }
      
      if(get_option('isms_reemail')=="1" && $_POST['ireemail'] != $_POST['iemail']){
        $no_error = 0;
        $_SESSION['error_text'] .= "You have entered different emails in email and reconfirm email field.<br>";
      }
      
      if(get_option('isms_passport_no')=="1" && get_option('isms_passport_no_required')=="1"){
        if($_POST['ipassport'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your passport.<br>";
        }
      }
      
      if(get_option('isms_social_security_no')=="1" && get_option('isms_social_security_no_required')=="1"){
        if($_POST['isocial_security_no'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your social security no.<br>";
        }
      }
      
      if(get_option('isms_dob')=="1" && get_option('isms_dob')=="1"){
        if($_POST['idob'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your date of birth.<br>";
        }
      }
      
      if(get_option('isms_company')=="1" && get_option('isms_company_required')=="1"){
        if($_POST['icompany'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your company.<br>";
        }
      }
      
      if(get_option('isms_product')=="1" && get_option('isms_product_required')=="1"){
        if($_POST['iproduct'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in the product.<br>";
        }
      }
      
      if(get_option('isms_website')=="1" && get_option('isms_website_required')=="1"){
        if($_POST['iwebsite'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your website.<br>";
        }
      }
      
      if(get_option('isms_message')=="1" && get_option('isms_message_required')=="1"){
        if($_POST['imessage'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your message.<br>";
        }
      }
      
      if(get_option('isms_custom1')=="1" && get_option('isms_custom1_required')=="1"){
        if($_POST['cisms_custom_name1'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in ".get_option('isms_custom_name1')." field.<br>";
        }
      }
      
      if(get_option('isms_custom2')=="1" && get_option('isms_custom2_required')=="1"){
        if($_POST['cisms_custom_name2'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in ".get_option('isms_custom_name2')." field.<br>";
        }
      }
      
      if(get_option('isms_custom3')=="1" && get_option('isms_custom3_required')=="1"){
        if($_POST['cisms_custom_name3'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in ".get_option('isms_custom_name3')." field.<br>";
        }
      }
      
      if(get_option('isms_custom4')=="1" && get_option('isms_custom4_required')=="1"){
        if($_POST['cisms_custom_name4'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in ".get_option('isms_custom_name4')." field.<br>";
        }
      }
      
      if(get_option('isms_custom5')=="1" && get_option('isms_custom5_required')=="1"){
        if($_POST['cisms_custom_name5'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in ".get_option('isms_custom_name5')." field.<br>";
        }
      }
      
      if(get_option('isms_verify_enable')=="1"){
        if($_POST['pin'] == ""){
          $no_error = 0;
          $_SESSION['error_text'] .= "Please fill in your TAC code.<br>";
        }
        elseif($_POST['verify'] == "1"){
		
		
	  }
        else{
		$verify_return = ismscURL_verify("https://www.isms.com.my/api_sms_verification.php?pin=".(int)$_POST['pin']."&a=".get_option('isms_verify_username')."&b=".get_option('isms_verify_password')."&phone=".$_POST['imobilephone']);
		if($verify_return == "" || $verify_return == "FAIL"){
			$no_error = 0;
			$_SESSION['error_text'] .= "Invalid TAC code entered.<br>";
		}
		elseif($verify_return != "SUCCESS"){
			$no_error = 0;
			$_SESSION['error_text'] .= $verify_return;
		}
	  }
      }
      
      if($captcha_valid && $no_error){
        foreach ($_POST as $name => $value){
          if(substr($name, 0, 1) == "i"){
            $message .= substr($name, 1).": $value".(($name=="imobilephone"&&$_POST['verify'])==1?" (Verified)":"")."\n";
          }elseif(substr($name, 0, 2) == "ci"){
            $message .= get_option(substr($name, 1)).": $value\n";
          }
        }
        if(strlen(get_option('notification_email')) > 10){
          if(get_option('isms_smtp_notification') == 1 && get_option('isms_smtp_host') != "" && get_option('isms_smtp_username') != "" && get_option('isms_smtp_password') != "" && get_option('isms_smtp_email') != ""){
            require(WP_PLUGIN_DIR."/secontactverificationform/include/class.phpmailer_gmail.php");
            
            $mail = new PHPMailer();
            $host = get_option('isms_smtp_host');
            if(get_option('isms_smtp_ssl') == 1){$host = "ssl://".$host.":465";}
            
            $mail->IsSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = get_option('isms_smtp_username');
            $mail->Password = get_option('isms_smtp_password');
            $mail->From     = get_option('isms_smtp_email');
            $mail->FromName = get_option('isms_smtp_username');
            
            if(get_option('isms_smtp_debug') == "1"){
			
			$mail->SMTPDebug = true;
		}
            
            $multipleDestAdd_arr=split(";", trim(get_option('notification_email'), ";"));
            
            foreach ($multipleDestAdd_arr as $multipleDestAdd){
			if(strlen($multipleDestAdd) > 5){
				$mail->AddAddress(trim($multipleDestAdd), trim($multipleDestAdd));
			}
            }
            if(get_option('isms_email')=="1" && $_POST['iemail'] != ""){
              if(get_option('isms_name')=="1" && $_POST['iname'] != ""){
                $isms_name = $_POST['iname'];
              }else{
                $isms_name = $_POST['iemail'];
              }
              $mail->AddReplyTo($_POST['iemail'], $isms_name);
            }
            $mail->WordWrap = 50;
            $mail->IsHTML(true);
            $mail->Subject  =  (get_option('form_subject')!=""?get_option('form_subject'):"Contact form");
            $mail->Body     =  nl2br($message);
            if(!$mail->Send()) {
			$no_error = 0;
			$_SESSION['error_text'] .= "SMTP error. Try enabling SMTP debug for debugging.<br>";
            } else {
              
            }
            
            if(get_option('isms_auto_response') == 1 && get_option('isms_auto_response_msg') != "" && get_option('isms_email') == "1" && $_POST['ireemail'] != ""){
			
			$mail->ClearAddresses();
			$mail->AddAddress(trim($_POST['ireemail']), trim($_POST['ireemail']));
			$mail->Body     =  nl2br(get_option('isms_auto_response_msg'));
			if(!$mail->Send()) {
			  
			} else {
			  
			}
		}
            
          }else{
            mail(str_replace(";", ",", trim(get_option('notification_email'), ";")), (get_option('form_subject')!=""?get_option('form_subject'):"Contact form"), $message, 'From: "Contact Form" <'.get_option('notification_email').">\n");
          }
        }
        if(get_option("isms_sms_auto_response") == "1" && get_option("isms_sms_auto_response_msg") != "" && get_option('isms_mobile_phone')=="1" && $_POST['imobilephone'] != ""){
		ismscURL_verify("https://www.isms.com.my/isms_send.php?un=".get_option('isms_user_name')."&pwd=".get_option('isms_password')."&dstno=".$_POST['imobilephone']."&msg=".urlencode(get_option("isms_sms_auto_response_msg"))."&type=1&sendid=wp".get_option('isms_destination'));
	  }
        if(get_option('isms_full_message')!="1"){
          $message = substr($message, 0, 159);
        }
        if(get_option('isms_addressbook')=="1"){
          ismscURL_verify("https://www.isms.com.my/isms_addressbook.php?un=".get_option('isms_user_name')."&pwd=".get_option('isms_password')."&phone=".urlencode($_POST['imobilephone'])."&name=".urlencode($_POST['iname'])."&company=".urlencode($_POST['icompany'])."&email=".urlencode($_POST['iemail'])."&dob=".urlencode($_POST['idob']));
        }
        
        if(get_option('isms_notification')=="1"){
          ismscURL_verify("https://www.isms.com.my/isms_send.php?un=".get_option('isms_user_name')."&pwd=".get_option('isms_password')."&dstno=".get_option('isms_destination')."&msg=".urlencode($message)."&type=1&sendid=wp".get_option('isms_destination'));
        }
        if(get_option('isms_smtp_debug')=="1"){
		
		
	  }
        elseif($no_error == 0){
		  if(preg_match("/\?/is", $r)){
			header("Location: $r&demoform_error=1");
		  }
		  else{
			header("Location: $r?demoform_error=1");
		  }
	  }
	  else{
		  if(preg_match("/\?/is", $r)){
			header("Location: $r&demoform_success=1");
		  }
		  else{
			header("Location: $r?demoform_success=1");
		  }
	  }
        exit();
      }else{
        if(preg_match("/\?/is", $r)){
		header("Location: $r&demoform_error=1");
	  }
	  else{
		header("Location: $r?demoform_error=1");
	  }
        exit();
      }
    }else{
      if(preg_match("/\?/is", $r)){
		header("Location: $r&demoform_error=1");
	}
	  else{
		header("Location: $r?demoform_error=1");
	}
      exit();
	}
  } 
}

add_action('init','handle_contact_post_verify');
add_shortcode('secontactverificationform', 'contactform_func_verify');
  /* Runs when plugin is activated */
register_activation_hook(__FILE__,'email_sms_contact_form_verify_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'email_sms_contact_form_verify_remove' );

function email_sms_contact_form_verify_install() {
  /* Creates new database field */
  add_option("email_sms_verify_data", 'Default', '', 'yes');
}

function email_sms_contact_form_verify_remove() {
  /* Deletes the database field */
  delete_option('email_sms_verify_data');
}

if ( is_admin() ){
  /* Call the html code */
  add_action('admin_menu', 'email_sms_admin_verify_menu');
}

function email_sms_admin_verify_menu() {
  add_options_page('SE Contact Verify Form Setting', 'SE Contact Verify Form', 'administrator', 'email_sms_contact_verify_setting', 'email_sms_html_verify_page');
  add_action( 'admin_init', 'register_mysettings_verify' );
}

function register_mysettings_verify(){
  //register our settings
  register_setting( 'email-sms-settings-group-verify', 'form_title' );
  register_setting( 'email-sms-settings-group-verify', 'form_subject' );
  register_setting( 'email-sms-settings-group-verify', 'form_success_msg' );
  
  
  register_setting( 'email-sms-settings-group-verify', 'isms_notification' );
  register_setting( 'email-sms-settings-group-verify', 'isms_addressbook' );
  register_setting( 'email-sms-settings-group-verify', 'isms_user_name' );
  register_setting( 'email-sms-settings-group-verify', 'isms_password' );
  register_setting( 'email-sms-settings-group-verify', 'isms_destination' );
  register_setting( 'email-sms-settings-group-verify', 'isms_captcha' );
  register_setting( 'email-sms-settings-group-verify', 'isms_full_message' );
  register_setting( 'email-sms-settings-group-verify', 'isms_sms_auto_response' );
  register_setting( 'email-sms-settings-group-verify', 'isms_sms_auto_response_msg' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_notification' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_ssl' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_host' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_username' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_password' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_email' );
  register_setting( 'email-sms-settings-group-verify', 'isms_smtp_debug' );
  register_setting( 'email-sms-settings-group-verify', 'isms_auto_response' );
  register_setting( 'email-sms-settings-group-verify', 'isms_auto_response_msg' );
  register_setting( 'email-sms-settings-group-verify', 'notification_email' );
  
  register_setting( 'email-sms-settings-group-verify', 'isms_verify_username' );
  register_setting( 'email-sms-settings-group-verify', 'isms_verify_password' );
  register_setting( 'email-sms-settings-group-verify', 'isms_verify_enable' );
  /////
  register_setting( 'email-sms-settings-group-verify', 'isms_name' ); register_setting( 'email-sms-settings-group-verify', 'isms_name_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_first_name' ); register_setting( 'email-sms-settings-group-verify', 'isms_first_name_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_last_name' ); register_setting( 'email-sms-settings-group-verify', 'isms_last_name_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_mobile_phone' ); register_setting( 'email-sms-settings-group-verify', 'isms_mobile_phone_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_address' ); register_setting( 'email-sms-settings-group-verify', 'isms_address_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_country' ); register_setting( 'email-sms-settings-group-verify', 'isms_country_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_country_list' );
  register_setting( 'email-sms-settings-group-verify', 'isms_subject' ); register_setting( 'email-sms-settings-group-verify', 'isms_subject_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_email' ); register_setting( 'email-sms-settings-group-verify', 'isms_email_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_reemail' ); register_setting( 'email-sms-settings-group-verify', 'isms_reemail_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_passport_no' ); register_setting( 'email-sms-settings-group-verify', 'isms_passport_no_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_social_security_no' ); register_setting( 'email-sms-settings-group-verify', 'isms_social_security_no_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_dob' ); register_setting( 'email-sms-settings-group-verify', 'isms_dob_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_company' ); register_setting( 'email-sms-settings-group-verify', 'isms_company_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_product' ); register_setting( 'email-sms-settings-group-verify', 'isms_product_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_website' ); register_setting( 'email-sms-settings-group-verify', 'isms_website_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_message' ); register_setting( 'email-sms-settings-group-verify', 'isms_message_required' );
  ////
  register_setting( 'email-sms-settings-group-verify', 'isms_custom1' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom1_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_name1' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_type1' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom2' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom2_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_name2' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_type2' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom3' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom3_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_name3' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_type3' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom4' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom4_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_name4' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_type4' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom5' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom5_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_name5' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_type5' );
  ////
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select1' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom_select1_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_name1' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_option1' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select2' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom_select2_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_name2' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_option2' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select3' ); register_setting( 'email-sms-settings-group-verify', 'isms_custom_select3_required' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_name3' );
  register_setting( 'email-sms-settings-group-verify', 'isms_custom_select_option3' );
}

function custom_field_type_verify($name, $type){?>
	<br><label>Field Type</label><select name="<?php echo $name; ?>">
		<option value="field" <?php echo ($type=="field"?'selected':''); ?>>Field</option>
		<option value="textbox" <?php echo ($type=="textbox"?'selected':''); ?>>Textbox</option>
	</select>
	<?php
}

function email_sms_html_verify_page(){?>
<link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/secontactverificationform/include/contactform.css">
<script src="<?php echo WP_PLUGIN_URL; ?>/secontactverificationform/include/contactform.js" type="text/javascript"></script>
<div>
<h2>SE Contact Verification Form</h2>
  <form method="post" action="options.php">
    <?php settings_fields('email-sms-settings-group-verify'); ?>
    <?php //do_settings_fields('email-sms-settings-group-verify');?>
    
    <table>
      <tr>
        <td valign="top" width="50%">
          <fieldset class="bmostyle_admin">
            <legend>iSMS</legend>
            <div>Register <a href="http://www.isms.com.my/" target="_blank">isms.com.my</a></div>
              <?php if(get_option('isms_user_name') != "" && get_option('isms_password') != ""){
                echo "<div><label>iSMS credit</label>".ismscURL_verify('https://www.isms.com.my/isms_balance.php?un='.get_option('isms_user_name').'&pwd='.get_option('isms_password'))." credits</div>";
              }else{
                echo "<div><label>iSMS credit</label>Please fill in your iSMS username and password.</div>";
              }?>
            <div><label>iSMS username</label><input name="isms_user_name" type="text" id="isms_user_name" value="<?php echo get_option('isms_user_name'); ?>" /></div>
            <div><label>iSMS password</label><input name="isms_password" type="text" id="isms_password" value="<?php echo get_option('isms_password'); ?>" /></div>
            <div><label>Destination Phone</label><input name="isms_destination" type="text" id="isms_destination" value="<?php echo get_option('isms_destination'); ?>" />
            <span>(Separate multiple phones with semicolon(;). Please insert full phone number including country code.)</span>
            </div>
            <div><label>Notify owner with SMS</label><input name="isms_notification" type="checkbox" id="isms_notification" value="1" <?php echo get_option('isms_notification')=="1"?"checked":""; ?> /></div>
            <div><label>Add to iSMS address book</label><input name="isms_addressbook" type="checkbox" id="isms_addressbook" value="1" <?php echo get_option('isms_addressbook')=="1"?"checked":""; ?> /></div>
            <div><label>Full message</label><input name="isms_full_message" type="checkbox" id="isms_full_message" value="1" <?php echo get_option('isms_full_message')=="1"?"checked":""; ?> /><span>(Check this option if you would like to received full message, more than 1 credits will be used if message goes over 159 characters)</span></div>
            <div><label>SMS auto response</label><input name="isms_sms_auto_response" type="checkbox" id="isms_sms_auto_response" value="1" <?php echo get_option('isms_sms_auto_response')=="1"?"checked":""; ?> /></div>
		<div><label>SMS auto response message</label>
		  <textarea name="isms_sms_auto_response_msg" id="isms_sms_auto_response_msg" cols="45" rows="5"><?php echo get_option('isms_sms_auto_response_msg'); ?></textarea>
            <span>(Mobile Phone must be enabled.)</span>
            </div>
            <div><label>iSMS phone verify API username</label><input name="isms_verify_username" type="text" id="isms_verify_username" value="<?php echo get_option('isms_verify_username'); ?>" /></div>
            <div><label>iSMS phone verify API password</label><input name="isms_verify_password" type="text" id="isms_verify_password" value="<?php echo get_option('isms_verify_password'); ?>" /></div>
            <div><label>iSMS phone verify API enable</label><input name="isms_verify_enable" type="checkbox" id="isms_verify_enable" value="1" <?php echo get_option('isms_verify_enable')=="1"?"checked":""; ?> /></div>
          </fieldset>
        </td>
        <td valign="top">
          <fieldset class="bmostyle_admin">
          <legend>Email</legend>
            <div><label>Use SMTP server</label><input name="isms_smtp_notification" type="checkbox" id="isms_smtp_notification" value="1" <?php echo get_option('isms_smtp_notification')=="1"?"checked":""; ?> /></div>            
            <div><label>SMTP debug</label><input name="isms_smtp_debug" type="checkbox" id="isms_smtp_debug" value="1" <?php echo get_option('isms_smtp_debug')=="1"?"checked":""; ?> /></div>            
            <div><label>SMTP host</label><input name="isms_smtp_host" type="text" id="isms_smtp_host" value="<?php echo get_option('isms_smtp_host'); ?>" /></div>
            <div><label>SMTP username</label><input name="isms_smtp_username" type="text" id="isms_smtp_username" value="<?php echo get_option('isms_smtp_username'); ?>" /></div>
            <div><label>SMTP password</label><input name="isms_smtp_password" type="text" id="isms_smtp_password" value="<?php echo get_option('isms_smtp_password'); ?>" /></div>
            <div><label>From email</label><input name="isms_smtp_email" type="text" id="isms_smtp_email" value="<?php echo get_option('isms_smtp_email'); ?>" /></div>
            <div><label>Use SSL</label><input name="isms_smtp_ssl" type="checkbox" id="isms_smtp_ssl" value="1" <?php echo get_option('isms_smtp_ssl')=="1"?"checked":""; ?> /></div>
            <div><label>Destination email</label>
              <textarea name="notification_email" id="notification_email" cols="45" rows="5"><?php echo get_option('notification_email'); ?></textarea>
              <span>Please use semicolon(;) to separate multiple recipient.</span>
            </div>
            <div><label>Auto response</label><input name="isms_auto_response" type="checkbox" id="isms_auto_response" value="1" <?php echo get_option('isms_auto_response')=="1"?"checked":""; ?> /></div>            
            <div><label>Auto response message</label>
              <textarea name="isms_auto_response_msg" id="isms_auto_response_msg" cols="45" rows="5"><?php echo get_option('isms_auto_response_msg'); ?></textarea>
            </div>
          </fieldset>
        </td>
      </tr>
    </table>
    
  <fieldset class="bmostyle_admin">
    <legend>Form setting</legend>
    <table>
      <tr>
        <td valign="top" width="50%">
            <div><label>Form Title</label><input name="form_title" type="textbox" id="form_title" value="<?php echo get_option('form_title'); ?>" /></div>
            <div><label>Form Subject</label><input name="form_subject" type="textbox" id="form_subject" value="<?php echo get_option('form_subject'); ?>" /></div>
            <div><label>On success message</label><textarea name="form_success_msg" type="textbox" id="form_success_msg" cols="45" rows="5"><?php echo get_option('form_success_msg'); ?></textarea></div>

          
            <div><label>Enable captcha</label><input name="isms_captcha" type="checkbox" id="isms_captcha" value="1" <?php echo get_option('isms_captcha')=="1"?"checked":""; ?> /></div>
            <div><label>First Name</label><input name="isms_first_name" type="checkbox" id="isms_first_name" value="1" <?php echo get_option('isms_first_name')=="1"?"checked":""; ?> /> Required <input name="isms_first_name_required" type="checkbox" id="isms_first_name_required" value="1" <?php echo get_option('isms_first_name_required')=="1"?"checked":""; ?> /></div>
            <div><label>last Name</label><input name="isms_last_name" type="checkbox" id="isms_last_name" value="1" <?php echo get_option('isms_last_name')=="1"?"checked":""; ?> /> Required <input name="isms_last_name_required" type="checkbox" id="isms_last_name_required" value="1" <?php echo get_option('isms_last_name_required')=="1"?"checked":""; ?> /></div>
            <div><label>Full Name</label><input name="isms_name" type="checkbox" id="isms_name" value="1" <?php echo get_option('isms_name')=="1"?"checked":""; ?> /> Required <input name="isms_name_required" type="checkbox" id="isms_name_required" value="1" <?php echo get_option('isms_name_required')=="1"?"checked":""; ?> /></div>
            <div><label>Email</label><input name="isms_email" type="checkbox" id="isms_email" value="1" <?php echo get_option('isms_email')=="1"?"checked":""; ?> /> Required <input name="isms_email_required" type="checkbox" id="isms_email_required" value="1" <?php echo get_option('isms_email_required')=="1"?"checked":""; ?> /></div>
            <div><label>Reconfirm Email</label><input name="isms_reemail" type="checkbox" id="isms_reemail" value="1" <?php echo get_option('isms_reemail')=="1"?"checked":""; ?> /> Required <input name="isms_reemail_required" type="checkbox" id="isms_reemail_required" value="1" <?php echo get_option('isms_reemail_required')=="1"?"checked":""; ?> /></div>
            <div><label>Mobile Phone</label><input name="isms_mobile_phone" type="checkbox" id="isms_mobile_phone" value="1" <?php echo get_option('isms_mobile_phone')=="1"?"checked":""; ?> /> Required <input name="isms_mobile_phone_required" type="checkbox" id="isms_mobile_phone_required" value="1" <?php echo get_option('isms_mobile_phone_required')=="1"?"checked":""; ?> /></div>
            <div><label>Address</label><input name="isms_address" type="checkbox" id="isms_address" value="1" <?php echo get_option('isms_address')=="1"?"checked":""; ?> /> Required <input name="isms_address_required" type="checkbox" id="isms_address_required" value="1" <?php echo get_option('isms_address_required')=="1"?"checked":""; ?> /></div>
            <div><label>Country</label><input name="isms_country" type="checkbox" id="isms_country" value="1" <?php echo get_option('isms_country')=="1"?"checked":""; ?> /> Required <input name="isms_country_required" type="checkbox" id="isms_country_required" value="1" <?php echo get_option('isms_country_required')=="1"?"checked":""; ?> /></div>
            <div><label>Custom Countries</label>
              <textarea name="isms_country_list" id="isms_country_list" cols="45" rows="5"><?php echo get_option('isms_country_list'); ?></textarea>
              <span>Please use semicolon(;) to separate multiple countries. Default list of countries will be loaded if left empty.</span>
            </div>
            <div><label>Passport No.</label><input name="isms_passport_no" type="checkbox" id="isms_passport_no" value="1" <?php echo get_option('isms_passport_no')=="1"?"checked":""; ?> /> Required <input name="isms_passport_no_required" type="checkbox" id="isms_passport_no_required" value="1" <?php echo get_option('isms_passport_no_required')=="1"?"checked":""; ?> /></div>
            <div><label>Social Security No.</label><input name="isms_social_security_no" type="checkbox" id="isms_social_security_no" value="1" <?php echo get_option('isms_social_security_no')=="1"?"checked":""; ?> /> Required <input name="isms_social_security_no_required" type="checkbox" id="isms_social_security_no_required" value="1" <?php echo get_option('isms_social_security_no_required')=="1"?"checked":""; ?> /></div>
            <div><label>Date Of Birth</label><input name="isms_dob" type="checkbox" id="isms_dob" value="1" <?php echo get_option('isms_dob')=="1"?"checked":""; ?> /> Required <input name="isms_dob_required" type="checkbox" id="isms_dob_required" value="1" <?php echo get_option('isms_dob_required')=="1"?"checked":""; ?> /></div>
            <div><label>Company</label><input name="isms_company" type="checkbox" id="isms_company" value="1" <?php echo get_option('isms_company')=="1"?"checked":""; ?> /> Required <input name="isms_company_required" type="checkbox" id="isms_company_required" value="1" <?php echo get_option('isms_company_required')=="1"?"checked":""; ?> /></div>
            <div><label>Product</label><input name="isms_product" type="checkbox" id="isms_product" value="1" <?php echo get_option('isms_product')=="1"?"checked":""; ?> /> Required <input name="isms_product_required" type="checkbox" id="isms_product_required" value="1" <?php echo get_option('isms_product_required')=="1"?"checked":""; ?> /></div>
            <div><label>Website</label><input name="isms_website" type="checkbox" id="isms_website" value="1" <?php echo get_option('isms_website')=="1"?"checked":""; ?> /> Required <input name="isms_website_required" type="checkbox" id="isms_website_required" value="1" <?php echo get_option('isms_website_required')=="1"?"checked":""; ?> /></div>
            <div><label>Subject</label><input name="isms_subject" type="checkbox" id="isms_subject" value="1" <?php echo get_option('isms_subject')=="1"?"checked":""; ?> /> Required <input name="isms_subject_required" type="checkbox" id="isms_subject_required" value="1" <?php echo get_option('isms_subject_required')=="1"?"checked":""; ?> /></div>
            <div><label>Message</label><input name="isms_message" type="checkbox" id="isms_message" value="1" <?php echo get_option('isms_message')=="1"?"checked":""; ?> /> Required <input name="isms_message_required" type="checkbox" id="isms_message_required" value="1" <?php echo get_option('isms_message_required')=="1"?"checked":""; ?> /></div>
            
        </td>
        <td valign="top">
            <div><label>Custom Field 1</label>
              <input name="isms_custom1" type="checkbox" id="isms_custom1" value="1" <?php echo get_option('isms_custom1')=="1"?"checked":""; ?> />
                  Required <input name="isms_custom1_required" type="checkbox" id="isms_custom1_required" value="1" <?php echo get_option('isms_custom1_required')=="1"?"checked":""; ?> />
            </div>
            <div><label>Field Name</label><input name="isms_custom_name1" type="text" id="isms_custom_name1" value="<?php echo get_option('isms_custom_name1'); ?>" />
              <?php custom_field_type_verify('isms_custom_type1', get_option('isms_custom_type1')); ?>
            </div>
            <div><hr/></div>
            
            <div><label>Custom Field 2</label><input name="isms_custom2" type="checkbox" id="isms_custom2" value="1" <?php echo get_option('isms_custom2')=="1"?"checked":""; ?> /> Required <input name="isms_custom2_required" type="checkbox" id="isms_custom2_required" value="1" <?php echo get_option('isms_custom2_required')=="1"?"checked":""; ?> /></div>
            <div><label>Field Name</label><input name="isms_custom_name2" type="text" id="isms_custom_name2" value="<?php echo get_option('isms_custom_name2'); ?>" />
              <?php custom_field_type_verify('isms_custom_type2', get_option('isms_custom_type2')); ?>
            </div>
            <div><hr/></div>
            
            <div><label>Custom Field 3</label><input name="isms_custom3" type="checkbox" id="isms_custom3" value="1" <?php echo get_option('isms_custom3')=="1"?"checked":""; ?> /> Required <input name="isms_custom3_required" type="checkbox" id="isms_custom3_required" value="1" <?php echo get_option('isms_custom3_required')=="1"?"checked":""; ?> /></div>
              <div><label>Field Name</label><input name="isms_custom_name3" type="text" id="isms_custom_name3" value="<?php echo get_option('isms_custom_name3'); ?>" />
                <?php custom_field_type_verify('isms_custom_type3', get_option('isms_custom_type3')); ?>
              </div>  
            <div><hr/></div>
            
            <div><label>Custom Field 4</label><input name="isms_custom4" type="checkbox" id="isms_custom4" value="1" <?php echo get_option('isms_custom4')=="1"?"checked":""; ?> /> Required <input name="isms_custom4_required" type="checkbox" id="isms_custom4_required" value="1" <?php echo get_option('isms_custom4_required')=="1"?"checked":""; ?> /></div>
              <div><label>Field Name</label><input name="isms_custom_name4" type="text" id="isms_custom_name4" value="<?php echo get_option('isms_custom_name4'); ?>" />
              <?php custom_field_type_verify('isms_custom_type4', get_option('isms_custom_type4')); ?>
              </div>
            <div><hr/></div>
            
            <div><label>Custom Field 5</label><input name="isms_custom5" type="checkbox" id="isms_custom5" value="1" <?php echo get_option('isms_custom5')=="1"?"checked":""; ?> /> Required <input name="isms_custom5_required" type="checkbox" id="isms_custom5_required" value="1" <?php echo get_option('isms_custom5_required')=="1"?"checked":""; ?> /></div>
              <div><label>Field Name</label><input name="isms_custom_name5" type="text" id="isms_custom_name5" value="<?php echo get_option('isms_custom_name5'); ?>" />
              <?php custom_field_type_verify('isms_custom_type5', get_option('isms_custom_type5')); ?>
              </div>
            <div><hr/></div>
            
            <div><label>Custom Select Field 1</label><input name="isms_custom_select1" type="checkbox" id="isms_custom_select1" value="1" <?php echo get_option('isms_custom_select1')=="1"?"checked":""; ?> /> Required <input name="isms_custom_select1_required" type="checkbox" id="isms_custom_select1_required" value="1" <?php echo get_option('isms_custom_select1_required')=="1"?"checked":""; ?> /></div>
            <div><label>Field Name</label><input name="isms_custom_select_name1" type="text" id="isms_custom_select_name1" value="<?php echo get_option('isms_custom_select_name1'); ?>" /></div>
            <div><label>Options</label><input name="isms_custom_select_option1" type="text" id="isms_custom_select_option1" value="<?php echo get_option('isms_custom_select_option1'); ?>" />
              <span>(Seperate each option with comma(,))</span></div>
            <div><hr/></div>
            
            <div><label>Custom Select Field 2</label><input name="isms_custom_select2" type="checkbox" id="isms_custom_select2" value="1" <?php echo get_option('isms_custom_select2')=="1"?"checked":""; ?> /> Required <input name="isms_custom_select2_required" type="checkbox" id="isms_custom_select2_required" value="1" <?php echo get_option('isms_custom_select2_required')=="1"?"checked":""; ?> /></div>
            <div><label>Field Name</label><input name="isms_custom_select_name2" type="text" id="isms_custom_select_name2" value="<?php echo get_option('isms_custom_select_name2'); ?>" /></div>
            <div><label>Options</label><input name="isms_custom_select_option2" type="text" id="isms_custom_select_option2" value="<?php echo get_option('isms_custom_select_option2'); ?>" />
              <span>(Seperate each option with comma(,))</span>
            </div>
            <div><hr/></div>

            <div><label>Custom Select Field 3</label><input name="isms_custom_select3" type="checkbox" id="isms_custom_select3" value="1" <?php echo get_option('isms_custom_select3')=="1"?"checked":""; ?> /> Required <input name="isms_custom_select3_required" type="checkbox" id="isms_custom_select3_required" value="1" <?php echo get_option('isms_custom_select3_required')=="1"?"checked":""; ?> /></div>
            <div><label>Field Name</label><input name="isms_custom_select_name3" type="text" id="isms_custom_select_name3" value="<?php echo get_option('isms_custom_select_name3'); ?>" /></div>
            <div><label>Options</label><input name="isms_custom_select_option3" type="text" id="isms_custom_select_option3" value="<?php echo get_option('isms_custom_select_option3'); ?>" />
              <span>(Seperate each option with comma(,))</span>
            </div>
        </td>
      </tr>
    </table>
  </fieldset>
  
<p><input type="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
<?php }?>
<?php

function list_of_countries_verify_verify(){
	
	$countries = '"Afghanistan"
				"Albania"
				"Algeria"
				"American Samoa"
				"Andorra"
				"Angola"
				"Antigua and Barbuda"
				"Argentina"
				"Armenia"
				"Aruba"
				"Australia"
				"Austria"
				"Azerbaijan"
				"Bahrain"
				"Bangladesh"
				"Barbados"
				"Belarus"
				"Belgium"
				"Belize"
				"Benin"
				"Bermuda"
				"Bhutan"
				"Bissau"
				"Bolivia"
				"Botswana"
				"Brazil"
				"Brunei"
				"Bulgaria"
				"Burkina Faso"
				"Burundi"
				"Cambodia"
				"Cameroon"
				"Canada"
				"Cape Verde"
				"Cayman Islands"
				"Central African Republic"
				"Chad"
				"Chile"
				"China"
				"Chinese Horoscope"
				"Colombia"
				"Comoros"
				"Congo"
				"Congo Democratic Republic"
				"Cook Islands"
				"Costa Rica"
				"Croatia"
				"Cuba"
				"Cyprus"
				"Czech Republic"
				"Denmark"
				"Djibouti"
				"Dominican Republic"
				"East Timor"
				"Ecuador"
				"Egypt"
				"El Salvador"
				"English Horoscope"
				"Equatorial Guinea"
				"Eritrea"
				"Estonia"
				"Ethiopia"
				"Faeroes Islands"
				"Falkland Islands"
				"Fiji"
				"Finland"
				"France"
				"French Guiana"
				"French Polynesia"
				"Gabon"
				"Gambia"
				"Georgia"
				"Germany"
				"Ghana"
				"Gibraltar"
				"Greece"
				"Greenland"
				"Grenada"
				"Guam"
				"Guatemala"
				"Guinea"
				"Guyana"
				"Haiti"
				"Herzegovina"
				"Honduras"
				"Hong Kong"
				"Hungary"
				"Iceland"
				"India"
				"Indonesia"
				"Iran"
				"Iraq"
				"Ireland"
				"Israel"
				"Italy"
				"Ivory Coast"
				"Jamaica"
				"Japan"
				"Jordan"
				"Kazakhstan"
				"Kenya"
				"Kiribati"
				"Kuwait"
				"Kyrgyzstan"
				"Laos"
				"Latvia"
				"Lebanon"
				"Lesotho"
				"Liberia"
				"Libya"
				"Liechtenstein"
				"Lithuania"
				"Luxembourg"
				"Macao"
				"Macedonia"
				"Madagascar"
				"Malawi"
				"Malaysia"
				"Maldives"
				"Mali"
				"Malta"
				"Martinique"
				"Mauritania"
				"Mauritius"
				"Mayotte"
				"Mexico"
				"Micronesia"
				"Moldova"
				"Monaco"
				"Mongolia"
				"Morocco"
				"Mozambique"
				"Myanmar"
				"Namibia"
				"Nepal"
				"Netherlands"
				"Netherlands Antilles"
				"New Caledonia"
				"New Zealand"
				"Nicaragua"
				"Niger"
				"Nigeria"
				"North Korea"
				"Northern Mariana Islands"
				"Norway"
				"Oman"
				"Pakistan"
				"Palestinian Territory"
				"Panama"
				"Papua New Guinea"
				"Paraguay"
				"Peru"
				"Philippines"
				"Poland"
				"Portugal"
				"Qatar"
				"Reunion"
				"Romania"
				"Russian Federation"
				"Rwanda"
				"San Marino"
				"Sao Tome and Principe"
				"Saudi Arabia"
				"Senegal"
				"Serbia and Montenegro"
				"Seychelles"
				"Sierra Leone"
				"Singapore"
				"Slovak Republic"
				"Slovenia"
				"Somalia"
				"South Africa"
				"South Korea"
				"Spain"
				"Sri Lanka"
				"St Kitts and Nevis"
				"St Lucia"
				"St Pierre and Miquelon"
				"St Vincent and the Grenadines"
				"Sudan"
				"Suriname"
				"Swaziland"
				"Sweden"
				"Switzerland"
				"Syria"
				"Taiwan"
				"Tajikistan"
				"Tanzania"
				"Thailand"
				"Thuraya"
				"Togo"
				"Tonga"
				"Trinidad and Tobago"
				"Tunisia"
				"Turkey"
				"Turkmenistan"
				"Uganda"
				"Ukraine"
				"United Arab Emirates"
				"United Kingdom"
				"United States"
				"Uruguay"
				"US Virgin Islands"
				"Uzbekistan"
				"Vanuatu"
				"Venezuela"
				"Viet Nam"
				"Yemen"
				"Zambia"
				"Zimbabwe"';
	
	if(get_option('isms_country_list') != ""){
		
		$countries = trim(str_replace(";", ",", get_option('isms_country_list')));
	}
	
	$countries = str_replace(array("\r\n", "\n\r", "\r", "\n", "\t"), "", $countries);
	$countries = trim(str_replace('""', ",", $countries), '"');
	
	$return = "";
	$countries_arr = explode(",", $countries);
	foreach($countries_arr as $value){
		
		$return .= '<option value="'.trim($value).'">'.trim($value).'</option>';
	}
	
	return $return;
}

?>