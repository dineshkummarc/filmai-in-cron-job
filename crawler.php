<?php

require_once "vendor/autoload.php";

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
    $unclaimed = $crawler->filter('.ptsplus')->getText();
    $total = $crawler->filter('.minPts')->parents()->first()->getText();
    $result = 'Unclaimed points: '.$unclaimed.'; total points: '.$total;
    sendEmail($result);
    echo "OK";
} catch (Exception $e) {
    sendEmail($e->getMessage());
    echo $e->getMessage();
} finally {
    $client->quit();
}

function sendEmail($text)
{
    $transport = (new Swift_SmtpTransport($_ENV['MAILER_HOST'], $_ENV['MAILER_PORT']))
        ->setUsername($_ENV['MAILER_USERNAME'])
        ->setPassword($_ENV['MAILER_PASSWORD'])
    ;

    $mailer = new Swift_Mailer($transport);

    $message = (new Swift_Message((new DateTime())->format('Y-m-d H:i:s').' FILMAI.IN crawler report'))
        ->setFrom([$_ENV['MAILER_USERNAME']])
        ->setTo([$_ENV['RECIPIENT_EMAIL']])
        ->setBody($text)
    ;

    return $mailer->send($message);
}
