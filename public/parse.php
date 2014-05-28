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
$crawler->filter('.profile_in_game.persona.in-game .profile_in_game_name')->each(function($node, $i) use ($crawler) {
  $playing = trim( $node->text() );
  $log = R::findLast('log');

  // No last played game (first parse)
  // or we're playing a different game from the last one
  if( !$log || $log->game !== $playing ){

    // We're not playing the same game as before
    // So stop it
    if( $log ){
      $log->stopped = date("Y-m-d H:i:s");
      R::store( $log );
    }

    // New log for new game
    $log = R::dispense('log');
    $log->started = date("Y-m-d H:i:s");
    $log->game = $playing;

    $recent = $crawler->filter('.recent_games .game_name a')->eq(0);
    if( $recent && trim( $recent->text() ) === $playing ){
      $href = $recent->attr('href');
      if( $href ){
        $explode = explode('/', $recent->attr('href'));
        $log->appid = end( $explode );
      }
    }
  }

  // Last seen
  $log->last_seen = date("Y-m-d H:i:s");
  R::store( $log );
});

$crawler->filter('.profile_in_game.persona.offline, .profile_in_game.persona.online')->each(function($node, $i){
  $log = R::findLast('log');

  if( $log && $log->stopped === null ){
    $log->stopped = date("Y-m-d H:i:s");
    R::store( $log );
  }

});

