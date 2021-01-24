<?php
 function fileLog($data) {
     $fileLog =  '/src/app/Logs/route.txt';
     $current = file_get_contents($fileLog);
     $current .= $data."\n";
     file_put_contents($fileLog, $current);
 }