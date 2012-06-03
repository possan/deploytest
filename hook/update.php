<?php

	// configuration
	$stable_branch = "stable";
	$giturl = "git://github.com/possan/deploytest.git";	
	$hookpath = "/var/www/deploytest/hook/";

	// do it
	openlog("deploytest", LOG_PERROR, LOG_LOCAL0);

        error_reporting(E_ALL);        
	header("Content-type: text/plain" );
	
	syslog(E_INFO, "Got github callback");

	$id = substr(md5(microtime()),0,8);
	$githublink = "";	
	
	try {
                $payload = json_decode($_REQUEST['payload']);
	} catch(Exception $e) {
                exit(0);
        }	
	
	$update = false;

    	file_put_contents('github.log', print_r($payload, TRUE), FILE_APPEND);        
	if ($payload->ref === 'refs/heads/'.$stable_branch) {
        	$id = $payload->head_commit->id;
		$giturl = $payload->repository->url;
		$githublink = $payload->head_commit->url;
	      	$update = true;
        }

	if( $_GET["dryrun"] == 1 ) {
 		$update = true;
	}

	if( $update ) {
		syslog(E_DEBUG, "Commit id: ".$id);
		syslog(E_DEBUG, "Github link: ".$githublink);
		syslog(E_DEBUG, "Git clone url: ".$giturl);
		passthru('cd '.$hookpath.' && ./dl.sh '.$id." \"".$githublink."\" \"".$giturl."\" \"".$stable_branch."\"");
	}

	closelog();

?>
