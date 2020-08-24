# Getting Started
To run this script, adjust the configuration to your needs and run `composer install`. Next, add the following crontab to your server:
`0 15 * * * php crawler.php > logs`. This crontab executes crawler script once a day at 15:00 and saves output in `logs` file.

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

# Known issues
Panther Client uses outdated Chrome driver, so in some cases you might have to manually download the right version of ChromeDriver and put it in the vendor/symfony/panther/chromedriver-bin directory.
