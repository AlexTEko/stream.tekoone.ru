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
    if (isset($_POST['token'])) {
        if ($_POST['token'] == "D3E79F86D8D716DFF81C52D711367") {
            if (isset($_POST['file'])) {
                $file = $_POST['file'];
                unlink($directory . $file);
                unlink($directory . str_replace(".mp4", ".jpeg", $file));
                unlink($directory . str_replace(".mp4", ".min.jpeg", $file));
            }
        }
    }
}

if ($_GET['do'] == "login") {
    if (isset($_POST['user']) && isset($_POST['pass'])) {
        if (($_POST['user'] == "123") && ($_POST['pass'] == "123")) {
            echo('{"token":"D3E79F86D8D716DFF81C52D711367"}');
        }
    }
}