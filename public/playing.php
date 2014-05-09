<?php

// Bring in anything from Composer and derps
require_once '../vendor/autoload.php';

use Carbon\Carbon;

if (($handle = fopen("../data/playing.csv", "r")) !== FALSE) {
  $last = null;
  while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

    if( isset($last) && $last[2] === $row[2] ){
      $last_timestamp = Carbon::createFromFormat('Y-m-d H:i:s', $last[0]);
      $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', $row[0]);

      if( $last_timestamp->diffInMinutes($timestamp) > 5 ){
        var_dump($row);
      }
    } else {
      var_dump($row);
    }

    $last = $row;
  }
  fclose($handle);
}
