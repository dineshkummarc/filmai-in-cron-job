<?php

require "vendor/autoload.php";

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Panther\Client;

$dotenv = new Dotenv();
$dotenv->load('.env', '.env.local');

$client = Client::createChromeClient();

try {
    $crawler = $client->request('GET', $_ENV['LOGIN_URL']);

    $form = $crawler->filter('.loginFrm')->form();
    $form->setValues(
        [
            'login' => $_ENV['LOGIN'],
            'password' => $_ENV['PASSWORD'],
        ]
    );
    $crawler = $client->submit($form);
    echo "Points in account: ".$crawler->filter('.ptsplus')->getText();
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    $client->quit();
}
