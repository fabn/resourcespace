#!/usr/bin/php
<?php
include(dirname(__FILE__) . "/../include/db.php");
include(dirname(__FILE__) . "/../include/general.php");
include(dirname(__FILE__) . "/../include/image_processing.php");

// We store the start date.
$global_start_time = microtime(true);

// We define the number of threads.
$max_forks = 3;

$lock_directory = '.';

// We create an array to store children pids.
$children = array();

/**
 * This function clean up the list of children pids.
 * This allow to detect the freeing of a thread slot.
 */
function reap_children()
  {
  global $children;

  $tmp = array();

  foreach ($children as $pid)
    {
    if (pcntl_waitpid($pid, $status, WNOHANG) != $pid)
      {
      array_push($tmp, $pid);
      }
    // else
    // {
    //   echo "[SIGCHLD] child $pid reaped.\n";
    // }
    }

  $children = $tmp;

  return count($tmp);
  } // reap_children()



/**
 * This function is used to process SIGALRM signal.
 * This is usefull when the parent process is killed.
 */
function sigalrm_handler()
  {
  die("[SIGALRM] hang in thumbnails creation ?\n");
  }



/**
 * This function is used to process SIGCHLD signal.
 * 
 */
function sigchld_handler($signal)
  {
  $running_jobs = reap_children();

  // echo "[SIGCHLD] jobs left: $running_jobs\n";

  pcntl_waitpid(-1, $status, WNOHANG);
  }



/**
 * This function is used to process SIGINT signal.
 * 
 */
function sigint_handler()
  {
  //unlink($lock_directory . "/update_daemon.lock");
  die("[SIGINT] exiting.\n");
  }


// We define the functions to use for signal handling.
pcntl_signal(SIGALRM, 'sigalrm_handler');
pcntl_signal(SIGCHLD, 'sigchld_handler');



// We fetch the list of resources to process.
$resources=sql_query("SELECT resource.ref, resource.file_extension FROM resource WHERE resource.has_image = 0");

foreach($resources as $resource) // For each resources
  {

  // We wait for a fork emplacement to be freed.
  while(count($children) >= $max_forks)
    {
    // We clean children list.
    reap_children();
    sleep(1);
    }

  if(count($children) < $max_forks) // Test if we can create a new fork.
    {
    $pid = pcntl_fork();
    if ($pid == -1)
      {
      die("fork failed!\n");
      }
    else if ($pid)
      {
      array_push($children, $pid);
      // echo sprintf("[MASTER] spawned client %d [PID:%d]...\n", count($children), $pid);
      }
    else
      {
      pcntl_signal(SIGCHLD, SIG_IGN);
      pcntl_signal(SIGINT, SIG_DFL);

      // Processing resource.
      echo sprintf("Processing resource n°%d.\n", $resource['ref']);

      $start_time = microtime(true);

      // For each fork, we need a new connection to database.
      mysql_connect($mysql_server,$mysql_username,$mysql_password, true);
      mysql_select_db($mysql_db);

      // If $mysql_charset is defined, we use it
      // else, we use the default charset for mysql connection.
      if(isset($mysql_charset))
        {
        if($mysql_charset)
          {
          mysql_set_charset($mysql_charset);
          }
        }
      create_previews($resource['ref'], false, $resource['file_extension']);

      echo sprintf("Processed resource n°%d in %01.2f seconds.\n", $resource['ref'], microtime(true) - $start_time);
      // We exit in order to avoid fork bombing.
      exit(0);
      }
    } // Test if we can create a new fork
  } // For each resources

// We wait for all forks to exit.
while(count($children))
  {
  // We clean children list.
  reap_children();
  sleep(1);
  }

echo sprintf("Completed in %01.2f seconds.\n", microtime(true) - $global_start_time);
