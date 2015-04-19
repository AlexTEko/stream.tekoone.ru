<?php
	$myfile = fopen("live", "w");
	fclose($myfile);

    $myfile = fopen("watching", "w");
    fwrite($myfile,0);
    fclose($myfile);
?>