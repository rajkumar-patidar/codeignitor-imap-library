## Codeignitor Imap Liabrary to get all emails:

To get all unread and read email using gmail login details. 

1. Firstly put imap.php into codeignitor library folder.
2. Load library into your controller like 

> $this->load->library('imap');

###### Set all reuired variables:-

> $hostname	= '{imap.gmail.com:993/imap/ssl}INBOX';

> $username	= 'example@example.com';

> $password	= '123456';

###### To search specific email using subject name into inbox (optional)
> $subject	= 'HR';

###### Set maximum featching emails limits (optional)
> $maximum	= '10';

3. Now call Imap function into your controller like.

> $response = $this->imap->get_emails($hostname,$username,$password,$subject,$maximum);

It will return all email data into response.

