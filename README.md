Project setup

Create your .env.local and set these variables:  
DATABASE_URL  
DB_USER  
DB_PASSWORD  
DB_HOST  
DB_NAME  

Then execute:  
`composer install`  
`npm i`   
`npm run dev`   
`symfony console app:update-db`  
`symfony serve`  
`symfony open:local`  


## Warning
If you have an error when running `symfony console app:update-db`  you might need to set your max_allowed_packet size.
You can run in mysql

`SET GLOBAL max_allowed_packet=1073741824;`  

Or you can **uncomment** the line 56 in **src/Command/GenerateDatabaseFromCsvCommand.php** 
and run  
`symfony console app:update-db`
