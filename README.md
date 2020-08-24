# Getting Started
To run this script, adjust the configuration to your needs and run `composer install`. Next, add the following crontab to your server:
`0 15 * * * php crawler.php > logs`. This crontab executes crawler script once a day at 15:00 and saves output in `logs` file.

# Important
Chrome must be installed on the server. Chrome version must match ChromeDriver version. 
If versions do not match, download the needed ChromeDriver version and put it in the `vendor/symfony/panther/chromedriver-bin/` directory.

# Configuration
Edit .env file to meet your needs

LOGIN=filmai.in username  
PASSWORD=filmai.in password  
LOGIN_URL=filmai.in login path  

MAILER_SUPPORT=1 for enabled, 0 for disabled. If enabled, you will receive mini reports of how many points you have unclaimed and how many points you have in total. The following variables must be also filled.  
MAILER_HOST=Mail service provider host  
MAILER_PORT=Mail service provider port  
MAILER_USERNAME=Mail service username  
MAILER_PASSWORD=Mail service password  
RECIPIENT_EMAIL=Recipient's email address  