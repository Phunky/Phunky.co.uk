<?php

// Bring in anything from Composer and derps
require_once '../vendor/autoload.php';

use Carbon\Carbon;

// Beans, beans, beans... fart
R::setup('mysql:host=localhost;dbname=playing','phunky','sl1200m2');

$logs = R::findAll('log', 'ORDER BY id DESC');

echo "<h1>Game stream</h1>";
echo '<ul>';
foreach($logs as $log){
  echo '<li>';
    echo '<strong>' . $log->user->nickname . '</strong>';
    if($log->stopped){
      $started = Carbon::createFromFormat('Y-m-d H:i:s', $log->started);
      $stopped = Carbon::createFromFormat('Y-m-d H:i:s', $log->stopped);
      $played = $started->diffInMinutes($stopped);
      echo 'played ' . $log->games->name . ' for ' . $played  . ' minutes, started playing at ' . $log->started . ' and stopped at ' . $log->stopped;
    } else {
      echo 'Currently playing ' . $log->games->name . ' now!';
    }
  echo '</li>';
}
echo '</ul>';
