# webbylabfilms
First step: You need to have MySQL Server
You can download and set up it like this instruction:
https://netbeans.org/kb/docs/ide/install-and-configure-mysql-server_ru.html

If you already have MySQL Server - check PHP version on your computer.
Application can be correctly working from PHP 7.1+ versions

Next step:
Run MySQL Server
Go to the folder with the project and write in terminal from this point:
1)cd database
2)php CreateDatabase.php
You should see that: "DB created successfully"

Check that in MySQL Server:
Just write correct settings

My settings:
host=localhost
dbname=webbylabfilms
username=root
password=

And the last step:
You need to run PHP local server

You must be where index.php file located and write command:
php -S localhost:8000

Open your browser and type localhost:8000
Enjoy!
