<?php

// Bring in anything from Composer and derps
require_once '../vendor/autoload.php';

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;

// Go grab my steam profile
$client = new Client('http://steamcommunity.com/id/phunky/');
$request = $client->get();
$response = $request->send();

// Grab the response body
$crawler = new Crawler($response->getBody(true));

// If my profile says i'm in game, store which game i'm currently playing
$crawler->filter('.profile_in_game.persona.in-game .profile_in_game_name')->each(function($node, $i){
  $fp = fopen('../data/playing.csv', 'a');
  fputcsv($fp, [
    'timestamp' => date("Y-m-d H:i:s"),
    'online' => 1,
    'playing' => trim( $node->text() )
  ]);
  fclose($fp);
});
