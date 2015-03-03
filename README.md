## Codeignitor Imap Liabrary to get all emails:

To get all unread and read email using gmail login details. 

1. Firstly put imap.php into codeignitor library folder.
2. Load library into your controller like 

> $this->load->library('imap');

###### Set all reuired variables:-

> $hostname	= '{imap.gmail.com:993/imap/ssl}INBOX';

> $username	= 'example@example.com';

> $password	= '123456';

###### To set subject of gmail inbox for search
> $subject	= 'HR';

###### To set maximum emails featching quantity from gmail 
> $maximum	= '10';

3. Now call Imap function into your controller like.

> $response = $this->imap->get_emails($hostname,$username,$password,$subject,$maximum);

It will return all email data into response.

