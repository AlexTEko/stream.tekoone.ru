<?php
  if (file_get_contents("http://stream.tekoone.ru:850/hls/test.m3u8"))
    echo 'true';
  else {
    echo 'false';
  }
