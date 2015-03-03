<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Imap
{
	
	var $hostname	= "";
	var $username	= "";
	var $password	= "";
	
	//-------------------------------------------------------------------------------------
	public function get_emails($hostname,$username,$password,$subject,$maximum)
	{
		$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: Not Connected to Enternet or '.imap_last_error());
		$emails = imap_search($inbox,'UNSEEN SUBJECT "'.$subject.'"');
		$output = array();
		if(is_array($emails)) 
		{
		    $i = 0;
		    $count = 1;
		    /* put the newest emails on top */
		    rsort($emails);
		    foreach($emails as $email_number) 
		    {
		    	if($count > $maximum)
		    	{
		    		write_response("\n \n--You Can Featch Maximum (".$maximum.") Emails At A Time.");
		    		echo write_in_file("<br/>". date('m/d/Y H:i:s')."--You Can Featch Maximum (".$maximum.") Emails At A Time.");
		    		break;
		    	}
		    	$overview = imap_fetch_overview($inbox,$email_number,0);
		        $message = imap_fetchbody($inbox,$email_number,1.1);
		        /* output the email header information */
				$output[$i]['seen'] 	= ($overview[0]->seen ? 'read' : 'unread');
				$output[$i]['subject'] 	= $overview[0]->subject;
				$output[$i]['from'] 	= $overview[0]->from;
				$output[$i]['to'] 		= $overview[0]->to;
				$output[$i]['date'] 	= $overview[0]->date;
				$output[$i]['unix_date']= $overview[0]->udate;
				$output[$i]['uid'] 		= $overview[0]->uid;
				$output[$i]['attached_file'] = $this->_check_attached_file($inbox,$email_number);
				$output[$i]['body'] 	= $message;
				$i++;
				$count++;
		    }
		    return $output;
		}
		else
		{
			return $output = "No Unread Email In Inbox.";
		}
		/* close the connection */
		imap_close($inbox);
	}
	
	// --------------------------------------------------------------------
	public function _check_attached_file($inbox, $email_number)
	{
		$structure = imap_fetchstructure($inbox, $email_number);
        $attachments = array();
        /* if any attachments found... */
		        if(isset($structure->parts) && count($structure->parts)) 
		        {
		            for($i = 0; $i < count($structure->parts); $i++) 
		            {
		                $attachments[$i] = array(
		                    'is_attachment' => false,
		                    'filename' => '',
		                    'name' => '',
		                    'attachment' => ''
		                );
		            
		                if($structure->parts[$i]->ifdparameters) 
		                {
		                    foreach($structure->parts[$i]->dparameters as $object) 
		                    {
		                        if(strtolower($object->attribute) == 'filename') 
		                        {
		                            $attachments[$i]['is_attachment'] = true;
		                            $attachments[$i]['filename'] = $object->value;
		                        }
		                    }
		                }
		            
		                if($structure->parts[$i]->ifparameters) 
		                {
		                    foreach($structure->parts[$i]->parameters as $object) 
		                    {
		                        if(strtolower($object->attribute) == 'name') 
		                        {
		                            $attachments[$i]['is_attachment'] = true;
		                            $attachments[$i]['name'] = $object->value;
		                        }
		                    }
		                }
		            
		                if($attachments[$i]['is_attachment']) 
		                {
		                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
		                    
		                    /* 4 = QUOTED-PRINTABLE encoding */
		                    if($structure->parts[$i]->encoding == 3) 
		                    { 
		                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
		                    }
		                    /* 3 = BASE64 encoding */
		                    elseif($structure->parts[$i]->encoding == 4) 
		                    { 
		                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
		                    }
		                }
		            }
		        }
        
		        /* iterate through each attachment and save it */
		       // $file_name =  array();
		       $file_name ='';
		        foreach($attachments as $attachment)
		        {
		            if($attachment['is_attachment'] == 1)
		            {
		                $filename = $attachment['name'];
		                if(empty($filename)) $filename = $attachment['filename'];
		                
		                if(empty($filename)) $filename = time() . ".dat";
		                
		                /* prefix the email number to the filename in case two emails
		                 * have the attachment with the same file name.
		                 */
		                $file_name = $email_number . "-" . $filename;
		                $fp = fopen('media/'.$email_number . "-" . $filename, "w+");
		                fwrite($fp, $attachment['attachment']);
		                chmod('media/'.$email_number . "-" . $filename,0777);
		                fclose($fp);
		            }
		        }
		        return $file_name;
	}
}
// END CI_Imap class
/* End of file Imap.php */
