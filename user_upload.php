<?php

		$short_options = 'u:p:h:f';
        $long_options = array('file:','create_table','dry_run','help');
        $options = getopt($short_options, $long_options);
      
        foreach($options as $option => $value) {
            switch ($option) {
            case 'help':
            	$help = true;
                break;
             case 'file':
               $filename = $value;
               
                break;
            case 'dry_run':
                $dry_run = true;
                break;
            case 'create_table':
                $create_table = true;
                break;
             case 'h':
                $host = $value;
                break;
            case 'u':
              $user = $value;
                break;
            case 'p':
                $password = $value;
                break;
            case 'f':
                $force = true;
                break;
             }
             }            
                          
                          
     echo "USER INPUT SUMMARY:\n",
    "Help: ", isset($help) ? "Yes" : "No", PHP_EOL,	
	"Using input file: ", isset($filename) ? $filename : "No", PHP_EOL,
	"Create table: ", isset($create_table) ? "Yes" : "No", PHP_EOL,	
	"dry_run: ", isset($dry_run) ? "Yes" : "No", PHP_EOL,
	"MySQL DB username: ", isset($user) ? $user : "Not specified", PHP_EOL,
	"MySQL user password: ", isset($password) ? $password : "Not specified", PHP_EOL,
	"MySQL DB host: ", isset($host) ? $host : "Not specified", PHP_EOL,
	"f(force): ", isset($force) ? "Yes" : "No", PHP_EOL, PHP_EOL;



	//functions to perform actions based on scenerio

	function show_help(){

		echo "How to use: \n To create table use- 'php user_upload.php  --create_table -u <MySQL-User Name > -p <MySQL Passowrd> -h <MySQL Host> ' \n To dry_run use - 'php user_upload.php --file users.csv --dry_run' \n To insert the data into database use- 'php user_upload.php --file users.csv -u -p -h ' \n This Script will only insert the data into database if there are not any invalid emails. \n If you want to insert the data into database if there are invalid email (This will only insert the correct records), Run the script- php user_upload.php  --create_table -u <MySQL-User Name > -p <MySQL Passowrd> -h <MySQL Host> -f \n For help use --help \n ";
	}

	//create table in db

	function create_table_db($host, $user, $password)
	{

		echo "Opening Database Connection.".PHP_EOL;
		$connection = mysqli_connect($host, $user, $password) or die(mysqli_connect_error());
		echo "Database Connection Opened Successfully.".PHP_EOL;
		//assumption of database name. 

		if(mysqli_query($connection, "create database if not exists `phpscriptdb`;")){
			echo "Database 'phpscriptdb' Created Successfully \n";
		}
		else{
			echo "Error Creating Database phpscriptdb: " . mysqli_error($connection) . PHP_EOL;
		}

		$sql_query = "CREATE TABLE `users` (
		name VARCHAR(30) NOT NULL,
		surname VARCHAR(30) NOT NULL,
		email VARCHAR(50) NOT NULL UNIQUE, 
		INDEX index_email (email)
		);
		";
		mysqli_select_db($connection, "phpscriptdb");	

		if (mysqli_query($connection, "DROP TABLE IF EXISTS `users`")) {
		echo "Table users exists. Dropping." . PHP_EOL;
		} else {
		echo "Error dropping table users: " . mysqli_error($connection) . PHP_EOL;
		}

		if(mysqli_query($connection, $sql_query)){
			echo "Table users created successfully.".PHP_EOL;
		}
		else{
			echo "Error creating Table users: " . mysqli_error($connection) . PHP_EOL;
		}

		


	}
		//Validating the records for invalid emails and other requirements. 

	function validate_file_data($filename)
	{
		$return_data[] = array();
		$invalid_email[] = array();
		$invalid_emails_count = 0;
		$validated_data[] = array();
		$iter_count = 0;
		$contents = file($filename);
		foreach ($contents as $content)
		{
		//this will skip the first row of csv because they are column names
		if ( $iter_count === 0) 
		{
			$iter_count++;
			continue;
		}
		
		$file_row = str_getcsv($content);
		
		$file_row = preg_replace("/\s+/", "", $file_row);
		// lower case each field
		$file_row[0] = strtolower($file_row[0]);
		$file_row[1] = strtolower($file_row[1]);
		$file_row[2] = strtolower($file_row[2]);
		//upper case first name and last name
		
		$file_row[0] = ucfirst($file_row[0]);
		$file_row[1] = ucfirst($file_row[1]);
		//Email Validation
		if (preg_match("/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/", $file_row[2]) === 0) {
			$current_iter_count = $iter_count+1;
			array_push($invalid_email, $file_row);
			$invalid_emails_count++;
		}
		else
		{
			//inserting the records only with valid emails
			array_push($validated_data, $file_row);

		}
		
		$iter_count++;
	}

		//displaying data
		echo "Validated data \n";
		foreach($validated_data as $data){

			echo implode(",", $data), PHP_EOL;
		}
		//Displaying invalid Emails in the file
		
		if($invalid_emails_count===0){
			echo "No invalid Emails Found. \n";
		}
		else{
			echo "\n", $invalid_emails_count, " ", "Records found with invalid Email. \n";
		foreach($invalid_email as $email)
		{

			echo implode(",", $email), PHP_EOL;
		}
	}

		array_shift ($validated_data);

	array_push($return_data, $validated_data);
	array_push($return_data, $invalid_emails_count);
	
	return $return_data;
	}


	function insert_data($host, $user, $password, $filename )
	{
		
		$data = validate_file_data($filename);
		$validate_data = $data[1];
		$invalid_email_count = $data[2];

		if($invalid_email_count > 0)
		{
			echo " \n Invalid emails found in the file, Cannot insert data into Database. \n If you want to insert the data in Database without the invalid records, Run the same script with -f option. \n";
		}
		else
		{
			echo "\n Inserting data into database .\n";

			$connection = mysqli_connect($host, $user, $password) or die(mysqli_connect_error());
			mysqli_select_db($connection, "phpscriptdb");
			foreach($validate_data as $row)
			{

				$name = mysqli_real_escape_string($connection, $row[0]);
				$surname = mysqli_real_escape_string($connection, $row[1]);
				$email = mysqli_real_escape_string($connection, $row[2]);

				$sql_query = "INSERT INTO `users` (`name`, `surname`, `email`) 
				VALUES ('$name', '$surname', '$email');
			";

			if(mysqli_query($connection, $sql_query)){
			echo "inserted $name, $surname, $email successfully.".PHP_EOL;
			}
			else
			{
			echo "Error inserting: " . mysqli_error($connection) . PHP_EOL;
		}
			}


		}
	}

	function insert_data_force($host, $user, $password, $filename, $force )
	{
			
			$data = validate_file_data($filename);
			$validate_data = $data[1];
			echo "Inserting data into database .\n";

			$connection = mysqli_connect($host, $user, $password) or die(mysqli_connect_error());
			mysqli_select_db($connection, "phpscriptdb");
			foreach($validate_data as $row)
			{

				$name = mysqli_real_escape_string($connection, $row[0]);
				$surname = mysqli_real_escape_string($connection, $row[1]);
				$email = mysqli_real_escape_string($connection, $row[2]);

				$sql_query = "INSERT INTO `users` (`name`, `surname`, `email`) 
				VALUES ('$name', '$surname', '$email');
			";

			if(mysqli_query($connection, $sql_query)){
			echo "inserted $name, $surname, $email successfully.".PHP_EOL;
			}
			else
			{
			echo "Error inserting: " . mysqli_error($connection) . PHP_EOL;
		}
			}


		}
	


	



		
	//scenerio assumptions (therecan be other scenerios but will create ambiguity)

	if(isset($create_table,$user, $password, $host) && !isset($dry_run) && !isset($filename))
	{

		create_table_db($host, $user, $password);
	}
	elseif(isset($dry_run,$filename) && !isset($create_table)&& !isset($user) && !isset($password) && !isset ($host))
	{
		validate_file_data($filename);
	}
	elseif(isset($filename,$user, $password, $host) && !isset($dry_run) && !isset($create_table)  && !isset($force))
	{
		
		insert_data($host, $user, $password, $filename);
	}

	elseif(isset($filename,$user, $password, $host, $force) && !isset($dry_run) && !isset($create_table))
	{
		insert_data_force($host, $user, $password, $filename, $force);
	}

	elseif(isset($create_table) && !isset($user) && !isset($password) && !isset($host))
	{

		echo "Please specify username, password,and host name to create table. \n";
	}
	elseif(isset($help))
	{

		show_help();
	}
	

	else 
	{ 
	die ("Unrecognized sequence of options. please use --help for script scenerios. \n");
	}

?>