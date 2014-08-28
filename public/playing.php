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
    $user = R::load('users', $log->user_id);
    $game = R::load('games', $log->games_id);
    echo '<strong>' . $user->nickname . '</strong> - ';
    if($log->stopped){
      $started = Carbon::createFromFormat('Y-m-d H:i:s', $log->started);
      $stopped = Carbon::createFromFormat('Y-m-d H:i:s', $log->stopped);
      $played = $started->diffInMinutes($stopped);
      echo 'played <strong>' . $game->name . '<strong> for <strong>' . $played  . ' minutes</strong>, started playing at ' . $log->started . ' and stopped at ' . $log->stopped;
    } else {
      echo '<strong><i>Currently playing ' . $game->name . ' now!</i></strong>';
    }
  echo '</li>';
}
echo '</ul>';
