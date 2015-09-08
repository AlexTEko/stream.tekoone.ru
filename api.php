<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$directory = 'rec/';
$TOKEN = "D3E79F86D8D716DFF81C52D711367";

$user = '123';
$pass = '123';

$stream = "test";

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
    return $load[0]*100;

}

function getInfo($videofile) {
  $array = json_decode(shell_exec("avprobe rec/".$videofile." -show_streams -of json"));
  $video = $array->streams[0];
  $audio = $array->streams[1];
  $duration = round($video->duration,0);
  if ($duration<60)
    $duration .="s";
  else {
    $m = round($duration/60,0);
    $s = $duration % 60;
    $duration = $m."m"." ".$s."s";
  }
  if ($video->level == 32)
    $level = 'NVENC';
  elseif ($video->level == 41)
    $level = 'x264';
  else
    $level = 'Unknown';
  $width = $video->width;
  $height = $video->height;
  $bit_rate = $video->bit_rate;
  $bit_rate = round($bit_rate/10000,0)*10;
  $fps = explode("/",$video->avg_frame_rate);
  $fps = round($fps[0]/$fps[1],0);
  $outp = '{"resolution":"'.$width.'x'.$height
    .'","duration":"'.$duration
    .'","fps":"'.$fps
    .'","level":"'.$level
    .'","bit_rate":"'.$bit_rate
    .'"}';
  return $outp;
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

  if (isset($output) ) {
    $outp = "";
    $fc = fopen("cache.video_count", "r+");
    $video_count = fread($fc, filesize("cache.video_count"));
    if ($video_count != count($output)) {
      fseek($fc, 0);
      fwrite($fc, count($output));
      $fv = fopen("cache.video", "w");
      foreach ($output as $rs) {
  			if ($outp != "") {$outp .= ",";}
  			$outp .= '{"name":"'  . str_replace(".flv.mp4","",$rs) . '",';
  			$outp .= '"src":"rec/'  . $rs. '",';
  			$outp .= '"img":"rec/'  . str_replace(".mp4",".min.jpeg",$rs). '",';
        $outp .= '"tech_info":'.getInfo($rs).'}';
  		}
      fwrite($fv, $outp);
      fclose($fv);
    }
    else {
      $fv = fopen("cache.video", "r");
      $outp = fread($fv, filesize("cache.video"));
      fclose($fv);
    }
    fclose($fc);
	}

  //  $xml=simplexml_load_string(file_get_contents('http://localhost:850/stat')) or die("Error: Cannot create object");
  //  $count  = ($xml->server->application->live->nclients) - 1;
  $count= 0;

  $live = 'false';
//if ((file_exists("live")) && (file_exists("/tmp/hls/".$stream.".m3u8")))
  if ((file_exists("live")) && file_get_contents("http://stream.tekoone.ru:850/hls/test.m3u8"))
    $live = 'true';

  $ds = round(disk_total_space("/")/1024/1024/1024,2);
  $df = round(disk_free_space("/")/1024/1024/1024,2);
  $du = $ds - $df;
  $disk = '{"percent":"'.round(100-($df/$ds)*100,0).'","used":"'.$du.'","free":"'.$df.'"}';

	$outp ='{"records":['.$outp.'],"live":'.$live.',"count":"'.$count.'",
	    "disk":'.$disk.',"memory":"'.round(get_server_memory_usage()['real'],0).'","cache":"'.round(get_server_memory_usage()['cache'],0).'","cpu":"'.get_server_cpu_usage().'"}';
	echo($outp);
}

if ($_GET['do'] == 'live') {
		//echo '{"live:" "'.file_exists("live").'"}';
}

if ($_GET['do'] == "delete") {
    if (isset($_POST['token'])) {
        if ($_POST['token'] == $TOKEN) {
            if (isset($_POST['file'])) {
                $file = $_POST['file'].".flv.mp4";
                unlink($directory . $file);
                unlink($directory . str_replace(".mp4", ".jpeg", $file));
                unlink($directory . str_replace(".mp4", ".min.jpeg", $file));
            }
        }
    }
}

if ($_GET['do'] == "login") {
    if (isset($_POST['user']) && isset($_POST['pass'])) {
        if (($_POST['user'] == $user) && ($_POST['pass'] == $pass)) {
            echo('{"token":"'.$TOKEN.'"}');
        }
    }
}

if ($_GET['do'] == "free") {

}
