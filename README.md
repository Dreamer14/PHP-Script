# PHP-Script
PHP Script to Insert datato Mysql from a csv file
#Task
///////////////////////////////////

Create a PHP script, that is executed from the command line, which accepts a CSV file as input
(see command line directives below) and processes the CSV file. The parsed file data is to be
inserted into a MySQL database. A CSV file is provided as part of this task that contains test
data, the script must be able to process this file appropriately.

The PHP script will need to correctly handle the following criteria:
      • CSV file will contain user data and have three columns: name, surname, email
      (see table definition below)
      • CSV file will have an arbitrary list of users
      • Script will iterate through the CSV rows and insert each record into a dedicated
      MySQL database into the table “users”
      • The users database table will need to be created/rebuilt as part of the PHP script.
      This will be defined as a Command Line directive below
      • Name and surname field should be set to be capitalised e.g. from “john” to “John”
      before being inserted into DB
      • Emails need to be set to be lower case before being inserted into DB
      • The script should validate the email address before inserting, to make sure that it
      is valid (valid means that it is a legal email format, e.g. “xxxx@asdf@asdf” is not
      a legal format). In case that an email is invalid, no insert should be made to
      database and an error message should be reported to STDOUT.
      
      
      
      Script Command Line Directives
The PHP script should include these command line options (directives):
• --file [csv file name] – this is the name of the CSV to be parsed
• --create_table – this will cause the MySQL users table to be built (and no further
• action will be taken)
• --dry_run – this will be used with the --file directive in case we want to run the
script but not insert into the DB. All other functions will be executed, but the
database won't be altered
• -u – MySQL username
• -p – MySQL password
• -h – MySQL host
• --help – which will output the above list of directives with details.
