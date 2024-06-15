<?php

namespace CryptoTrade;
use CryptoTrade\App\Sqlite;
use Dotenv\Dotenv;
use CryptoTrade\App\Tasks;
use CryptoTrade\App\Wallet;
use CryptoTrade\App\Display;
require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();
$api = $_ENV['MY_API'];

$database = new Sqlite();
$transactions = $database->loadTransactions();
$owned = $database->loadOwned();
$money = $database->loadMoney();

$tasks = new Tasks($api);
$wallet = new Wallet($api, $transactions, $owned, $money);
$show = new Display($tasks, $wallet);

while (true) {
    $show->getMenu();
    $userAction = (int)readline("Enter your action: ");
    $show->chooseAction($userAction, $database);
}