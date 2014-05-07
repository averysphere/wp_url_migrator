<?php
 
  /*  To use, copy and paste to your favorite text editor and then run into the command line or shell.
   *  Sample usage is:
   *  
   *  $ php -f ./wp_url_migrator.php h=localhost u=root p= d=bizlist x=bizlist_ http://192.168.1.1 http://localhost
   *
   *  where:
   *  h => the database server host
   *  u => database user to use
   *  p => password of the database user, write 'p=' without quote if none
   *  d => the database name
   *  x => the table prefix, write 'x=' without quote if none
   *
   *  The last two arguments stand for pattern and replacement, and they should be the last ones in the line.
   *
   *  Remember to back up your database before running this script. I am not held liable for any circumstances
   *  as a result of using this script. Good luck to your migratrion!
   *
   *  Harvey Entendez Diaz (averysphere) of One World ICTS Philippines Inc.
   *  averysphere.com / geekythinking.com / one-world.asia
   *
  */
 
 
  if ( $argc < 2 ) {
    print "\n\nNo arguments provided. I DON'T KNOW WHAT TO DO! Cannot continue...\n\n";
    exit;
  }
  elseif ( $argc >= 2 AND $argc <=7 ) {
    print "\n\nRequired arguments missing. I DON'T KNOW WHAT TO DO! Cannot continue...\n\n";
    exit;
  }
 
  $db_host = '';
  $db_name = '';
  $db_user = '';
  $db_pswd = '';
  $db_prfx = '';
 
  for( $i = 1; $i < ($argc - 1); $i++ ) {
    $arg = split('=', $argv[$i]);
    if (sizeof($arg) > 1) {
      switch( $arg[0] ) {
        case 'h':
          $db_host = $arg[1];
          break;
        case 'u':
          $db_user = $arg[1];
          break;
        case 'd':
          $db_name = $arg[1];
          break;
        case 'p':
          $db_pswd = $arg[1];
          break;
        case 'x':
          $db_prfx = $arg[1];
          break;
      }
    }
  }
 
  $pattern =  $argv[6];
  $replace = $argv[7];
 
  $cn = new mysqli($db_host, $db_user, $db_pswd, $db_name) or die() ;
  if ( ! $cn->connect_errno)
  {
    print "\n\nConnected now to $db_host...\n\n";
    print "Updating OPTIONS...\n";
    $sql = "UPDATE " . $db_prfx . "options SET option_value = REPLACE(option_value, '$pattern', '$replace') WHERE option_name = 'home' OR option_name = 'siteurl'";
    print $sql . "\n";
    if ( $query = $cn->query($sql) ) {
      print $cn->affected_rows . " rows affected...\n\n";
    }
    else {
      print "Query did not run successfully...\n\n";
    }
   
    print "Updating post GUIDs...\n";
    $sql = "UPDATE " . $db_prfx . "posts SET guid = REPLACE(guid, '$pattern', '$replace');";
    print $sql . "\n";
    if ( $query = $cn->query($sql) ) {
      print $cn->affected_rows . " rows affected...\n\n";
    }
    else {
      print "Query did not run successfully...\n\n";
    }
   
    print "Updating post URLs...\n";
    $sql = "UPDATE " . $db_prfx . "posts SET post_content = REPLACE (post_content, '$pattern', '$replace')";
    print $sql . "\n";
    if ( $query = $cn->query($sql) ) {
      print $cn->affected_rows . " rows affected...\n\n";
    }
    else {
      print "Query did not run successfully...\n\n";
    }
   
    print "Updating image paths...\n";
    $sql = 'UPDATE ' . $db_prfx . 'posts SET post_content = REPLACE (post_content, \'src="' . $pattern . '\', \'src="' . $replace . '\')';
    print $sql . "\n";
    if ( $query = $cn->query($sql) ) {
      print $cn->affected_rows . " rows affected...\n\n";
    }
    else {
      print "Query did not run successfully...\n\n";
    }
   
    print "Updating meta values...\n";
    $sql = "UPDATE " . $db_prfx . "postmeta SET meta_value = REPLACE (meta_value, '$pattern','$replace')";
    print $sql . "\n";
    if ( $query = $cn->query($sql) ) {
      print $cn->affected_rows . " rows affected...\n\n";
    }
    else {
      print "Query did not run successfully...\n\n";
    }
  }
  else
  {
    print "\n\nThe process met an error when trying to connect to your database.\nPlease check that your MySQL credentials are all right.\n\n";
    print "Error is: \n" . $cn->connect_error . "\n\n";
  }
  $cn->close();
 
?>