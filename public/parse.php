<?php
error_reporting(E_ALL);
// Bring in anything from Composer and derps
require_once '../vendor/autoload.php';

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

// Beans, beans, beans... fart
R::setup('mysql:host=localhost;dbname=playing','phunky','sl1200m2');

$log = R::dispense('log');

// Go grab my steam profile
$client = new Client('http://steamcommunity.com/id/phunky/');
$request = $client->get();
$response = $request->send();

// Grab the response body
$crawler = new Crawler($response->getBody(true));

// If my profile says i'm in game, store which game i'm currently playing
$crawler->filter('.profile_in_game.persona.in-game .profile_in_game_name')->each(function($node, $i){
  $playing = trim( $node->text() );
  $log = R::findLast('log');

  if( !$log ){
    $log = R::dispense('log');
    $log->game = $playing;
    $log->started = date("Y-m-d H:i:s");
  }

  if( $log->game !== $playing ){
    $log->stopped = date("Y-m-d H:i:s");
    R::store( $log );

    $log = R::dispense('log');
    $log->game = $playing;
    $log->started = date("Y-m-d H:i:s");
  }

  $log->last_seen = date("Y-m-d H:i:s");
  R::store( $log );
});

$crawler->filter('.profile_in_game.persona.offline, .profile_in_game.persona.online')->each(function($node, $i){
  $log = R::findLast('log');

  if( $log && !$log->stopped ){
    $log->stopped = date("Y-m-d H:i:s");
    R::store( $log );
  }

});

