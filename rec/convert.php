<?php
		$directory = '/var/www/stream.tekoone.ru/rec/live/';
		$rec = '/var/www/stream.tekoone.ru/rec/';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $file) {
            if (file_exists($directory.$file)) {
                $file_name = explode(".", $file);
                if (end($file_name) == "flv")
				{
                    exec("avconv -i ".$directory.$file." -codec copy ".$directory.$file.".mp4 ");
					exec("avconv -i ".$directory.$file." -r 1 -s 1280x720 -f image2 ".$directory.$file.".jpeg");
					exec("avconv -i ".$directory.$file." -r 1 -s 40x32 -f image2 ".$directory.$file.".min.jpeg");
					
					rename($directory.$file.".mp4", $rec.$file.".mp4");
					rename($directory.$file.".jpeg", $rec.$file.".jpeg");
					rename($directory.$file.".min.jpeg", $rec.$file.".min.jpeg");
					
					unlink($directory.$file);
				}
            }
        }
		
		echo "Ok!";