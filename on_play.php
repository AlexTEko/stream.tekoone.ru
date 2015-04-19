<?php
    $myfile = fopen("watching", "r+");
    $count = fgets($myfile);
    if (empty($count))
        $count = 0;
    $count++;
    fseek($myfile,0);
    echo $count;
    fwrite($myfile,$count);
    fclose($myfile);

