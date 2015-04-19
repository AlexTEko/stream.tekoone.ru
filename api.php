<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$directory = 'rec/';

function get_server_memory_usage(){

    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage['real'] = $mem[2]/$mem[1]*100 - $mem[6]/$mem[1]*100;
    $memory_usage['cache'] = $mem[6]/$mem[1]*100;
    return $memory_usage;
}

function get_server_cpu_usage(){

    $load = sys_getloadavg();
    return $load[0];

}


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

//    $myfile = fopen("watching", "r+");
//    $count = fgets($myfile);
//    if (empty($count))
//        $count = 0;
//    fclose($myfile);

    $xml=simplexml_load_string(file_get_contents('http://stream.tekoone.ru:8080/stat')) or die("Error: Cannot create object");
    $count  = ($xml->server->application->live->nclients) - 1;

    $ds = round(disk_total_space("/")/1024/1024/1024,2);
    $df = round(disk_free_space("/")/1024/1024/1024,2);
    $du = $ds - $df;
    $disk = '{"percent":"'.round(100-($df/$ds)*100,0).'","used":"'.$du.'","free":"'.$df.'"}';



	$outp ='{"records":['.$outp.'],"live":"'.file_exists("live").'","count":"'.$count.'",
	    "disk":'.$disk.',"memory":"'.round(get_server_memory_usage()['real'],0).'","cache":"'.round(get_server_memory_usage()['cache'],0).'","cpu":"'.round(get_server_cpu_usage(),0).'"}';
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

if ($_GET['do'] == "free") {

}