<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Bring in anything from Composer and derps
require_once '../vendor/autoload.php';

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

// Beans, beans, beans... fart
R::setup('mysql:host=localhost;dbname=playing','phunky','sl1200m2');

$users = R::findAll('users');

// Loop through and grab that shit.
foreach($users as $user){

  // Go grab my steam profile
  $client = new Client('http://steamcommunity.com/id/' . $user->community_id . '/');
  $request = $client->get();
  $response = $request->send();

  // Grab the response body
  $crawler = new Crawler($response->getBody(true));

  // Grab last game from db
  $last_log = R::findLast('log', ' user_id = ? ', [ $user->id ]);

  // If my profile says i'm in game, store which game i'm currently playing
  $crawler->filter('.profile_in_game.persona.in-game .profile_in_game_name')->each(function($node, $i) use ($user, $crawler, $last_log) {
    $game_name = trim( $node->text() );

    // Find what we're currenly playing
    $playing = R::findLast('games', ' name = ? ', [ $game_name ]);

    var_dump($last_log->game_id);
    var_dump($playing->id);
    var_dump($user);

    // No last played game (first parse)
    // or we're playing a different game from the last one
    if( !$last_log || $last_log->game_id !== $playing->id || $last_log->stopped !== null ){

      // We're not playing the same game as before
      // So stop it
      if( $last_log && !$last_log->stopped ){
        $last_log->stopped = date("Y-m-d H:i:s");
        R::store( $last_log );
      }

      // Couldn't find the game, so store it as a new one
      if( !$playing ){
        $playing = R::dispense('games');
        $playing->name = $game_name;
        R::store( $playing );
      }

      // New log for new game
      $log = R::dispense('log');
      $log->started = date("Y-m-d H:i:s");
      $log->games = $playing;
      $log->user = $user;
      $log->last_seen = date("Y-m-d H:i:s");
      R::store( $log );

      $recent = $crawler->filter('.recent_games .game_name a')->eq(0);
      if( $recent && trim( $recent->text() ) === $game_name ){
        $href = $recent->attr('href');
        if( $href ){
          $explode = explode('/', $recent->attr('href'));
          $playing->app_id = end( $explode );
          R::store( $playing );
          $log->app_id = $playing->app_id;
          R::store($log);
        }
      }
    }

    // Last seen for non-stopped games
    $last_log->last_seen = date("Y-m-d H:i:s");->last_seen = date("Y-m-d H:i:s");
    R::store( $last_log )
  });

  $crawler->filter('.profile_in_game.persona.offline, .profile_in_game.persona.online')->each(function($node, $i) use ($last_log) {

    if( $last_log && $last_log->stopped === null ){
      $last_log ->last_seen = date("Y-m-d H:i:s");
      $last_log->stopped = date("Y-m-d H:i:s");
      R::store( $last_log );
    }

  });

}
