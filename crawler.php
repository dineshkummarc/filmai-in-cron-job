<?php

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Panther\Client;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env', __DIR__.'/.env.local');

$isMailSupported = $_ENV['MAILER_SUPPORT'];

$log = (new DateTime())->format('Y-m-d H:i:s').' execution started.'.PHP_EOL;
$client = Client::createChromeClient();

try {
    $log = $log.(new DateTime())->format('Y-m-d H:i:s').' sending GET request to '.$_ENV['LOGIN_URL'].PHP_EOL;
    $crawler = $client->request('GET', $_ENV['LOGIN_URL']);
    $form = $crawler->filter('.loginFrm')->form();
    $form->setValues(
        [
            'login' => $_ENV['LOGIN'],
            'password' => $_ENV['PASSWORD'],
        ]
    );
    $log = $log.(new DateTime())->format('Y-m-d H:i:s').' submitting login form.'.PHP_EOL;
    $crawler = $client->submit($form);
    $before = $crawler->filter('.minPts')->parents()->first()->getText();
    $log = $log.(new DateTime())->format('Y-m-d H:i:s').' executing transfer pts JS (current pts: '.$before.')'.PHP_EOL;
    $client->executeScript('$(".transfer").click()');
    $log = $log.(new DateTime())->format('Y-m-d H:i:s').' execution complete'.PHP_EOL;
    if ($isMailSupported) {
        sendEmail($log);
    }
} catch (Exception $e) {
    $log = $log.(new DateTime())->format('Y-m-d H:i:s').' exception:'.$e->getMessage();
    if ($isMailSupported) {
        sendEmail($log);
    }

    echo $e->getMessage(), PHP_EOL;
} finally {
    $client->quit();
    echo $log;
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
