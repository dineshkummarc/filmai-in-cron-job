<?php

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Panther\Client;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env', __DIR__.'/.env.local');

$isMailSupported = $_ENV['MAILER_SUPPORT'];

echo (new DateTime())->format('Y-m-d H:i:s').' execution started';
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
    $client->executeScript('$(".transfer").click()');
    $total = $crawler->filter('.minPts')->parents()->first()->getText();
    $result = 'Total points in account: '.$total;
    if ($isMailSupported) {
        sendEmail($result);
    }
    echo "OK";
} catch (Exception $e) {
    if ($isMailSupported) {
        sendEmail($e->getMessage());
    }
    echo $e->getMessage();
} finally {
    $client->quit();
    echo (new DateTime())->format('Y-m-d H:i:s').' execution completed';
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
