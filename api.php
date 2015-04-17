<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$directory = 'rec/';

if ($_GET['do'] == 'get') {
		$directory = 'rec/';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
	
        foreach ($scanned_directory as $file) {
            if (file_exists("rec/".$file)) {
                $file_name = explode(".", $file);
                if (end($file_name) == "mp4")
                    $output[] = $file;
            }
        }

	$outp = "";
	if (isset($output) )
	{
        foreach ($output as $rs) {
			if ($outp != "") {$outp .= ",";}
			$outp .= '{"name":"'  . $rs . '",';
			$outp .= '"src":"rec/'  . $rs. '",';
			$outp .= '"img":"rec/'  . str_replace(".mp4",".min.jpeg",$rs). '"}';
		}
	
	}
	$outp ='{"records":['.$outp.'],"live":"'.file_exists("live").'"}';
	echo($outp);
}


if ($_GET['do'] == 'live') {
		//echo '{"live:" "'.file_exists("live").'"}';
}

if ($_GET['do'] == "delete") {
		$file = $_GET['file'];
		unlink($directory.$file);
		unlink($directory.str_replace(".mp4",".jpeg",$file));
		unlink($directory.str_replace(".mp4",".min.jpeg",$file));
}
?>