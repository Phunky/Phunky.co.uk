<?php
error_reporting(E_ALL);
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

    // No last played game (first parse)
    // or we're playing a different game from the last one
    if( !$last_log || $last_log->game->name !== $playing || $last_log->stopped !== null ){

      // We're not playing the same game as before
      // So stop it
      if( $last_log && !$last_log->stopped ){
        $log->last_seen = date("Y-m-d H:i:s");
        $last_log->stopped = date("Y-m-d H:i:s");
        R::store( $last_log );
      }

      // Find what we're currenly playing
      $playing = R::findLast('games', ' name = ? ', [ $game_name ]);

      // Couldn't find the game, so store it as a new one
      if( !$playing ){
        $playing = R::dispense('games');
        $playing->name = $game_name;
        R::store( $playing );
      }

      // New log for new game
      $log = R::dispense('log');
      $log->started = date("Y-m-d H:i:s");
      $log->game = $playing;
      $log->user = $user;
      R::store( $log );

      $recent = $crawler->filter('.recent_games .game_name a')->eq(0);
      if( $recent && trim( $recent->text() ) === $game_name ){
        $href = $recent->attr('href');
        if( $href ){
          $explode = explode('/', $recent->attr('href'));
          $playing->app_id = end( $explode );
          R::store( $playing );
        }
      }
    }

    // Last seen for non-stopped games
    if( !$log->stopped ){
      $log->last_seen = date("Y-m-d H:i:s");
      R::store( $log );
    }
  });

  $crawler->filter('.profile_in_game.persona.offline, .profile_in_game.persona.online')->each(function($node, $i) use ($last_log) {

    if( $last_log && $last_log->stopped === null ){
      $last_log->last_seen = date("Y-m-d H:i:s");
      $last_log->stopped = date("Y-m-d H:i:s");
      R::store( $last_log );
    }

  });

}
