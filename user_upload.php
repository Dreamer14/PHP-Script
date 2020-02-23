<?php

		$short_options = 'u:p:h:';
        $long_options = array('file:','create_table:','dry_run','help:');
        $options = getopt($short_options, $long_options);
      
        foreach($options as $option => $value) {
            switch ($option) {
            case 'help':
            	show_help();
            	exit(0);
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
                echo $host;

                break;
            case 'u':
              $user = $value;
                break;
            case 'p':
                $password = $value;
                break;
             }
             }            
                          
                          
     echo "USER INPUT SUMMARY:\n", 
	"Using input file: ", isset($filename) ? $filename : "No", PHP_EOL,
	"Create table: ", isset($create_table) ? "Yes" : "No", PHP_EOL,	
	"dry_run: ", isset($dry_run) ? "Yes" : "No", PHP_EOL,
	"MySQL DB username: ", isset($user) ? $user : "Not specified", PHP_EOL,
	"MySQL user password: ", isset($password) ? $password : "Not specified", PHP_EOL,
	"MySQL DB host: ", isset($host) ? $host : "Not specified", PHP_EOL, PHP_EOL;



	//functions to perform actions based on scenerio

	function show_help(){

		echo "help. \n";
	}
	function create_table(){

		echo "create table. \n";
	}

	function validate_file_data(){

		echo "validate data.\n";
	}


	function insert_data(){

		echo "Insert data.\n";
	}
		
	//scenerio assumptions (therecan be other scenerios but will create ambiguity)

	if(isset($create_table,$user, $password, $host) && !isset($dry_run) && !isset($filename)){

		create_table();
	}
	elseif(isset($dry_run,$filename) && !isset($create_table)&& !isset($user) && !isset($password) && !isset ($host)){
		validate_file_data();
	}
	elseif(isset($filename,$user, $password, $host) && !isset($dry_run) && !isset($create_table)){

		insert_data();
	}
	else { /	
	die ("Unrecognized sequence of options. please use --help for script scenerios. \n");
}

?>