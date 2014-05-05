<?php
// Bring in anything from Composer
require_once '../vendor/autoload.php';

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client('http://steamcommunity.com/id/phunky/');
$request = $client->get();
$response = $request->send();

$crawler = new Crawler($response->getBody(true));

$results = [
  'timestamp' => date("Y-m-d H:i:s"),
  'online' => 1,
  'playing' => 0
];

$crawler->filter('.profile_in_game.persona.online')->filter('.profile_in_game.persona.online')->each(function($node, $i) use (&$results) {
  $results['online'] = 1;
  $results['playing'] = 0;
});

$crawler->filter('.profile_in_game.persona.in-game .profile_in_game_name')->each(function($node, $i) use (&$results) {
  $results['online'] = 1;
  $results['playing'] = trim( $node->text() );
});

// Write CSV
$fp = fopen('../data/playing.csv', 'a');
fputcsv($fp, $results);
fclose($fp);

// Notes
// profile_in_game persona in-game | online | offline
// .profile_in_game_name
