<?php

namespace CryptoTrade\App;

use CryptoTrade\App\Api\CoinMC;
use CryptoTrade\App\Api\CryptoApi;


class Wallet
{
    private CryptoApi $cryptoApi;
    private float $money;
    private array $transactions;
    private array $owned;

    public function __construct(string $api,  array $transactions, array $owned, float $money)
    {
        $this->cryptoApi = new CoinMC($api);
        $this->transactions = $transactions;
        $this->owned = $owned;
        $this->money = $money;
    }

    public function transaction(string $trade, string $cryptoName, float $spent, float $received): array
    {
        return $this->transactions[] = [
            "trade" => $trade,
            "cryptoName" => $cryptoName,
            "spent" => $spent,
            "received" => $received
        ];
    }

    public function owned(string $name, float $value): array
    {
        return $this->owned[] = [
            "cryptoName" => $name,
            "value" => $value
        ];
    }

    public function purchase(): void
    {

        while (true) {
            $userCrypto = ucfirst(readline("Crypto you want to purchase: "));
            if ($userCrypto == "") {
                continue;
            }
            $userAmount = (int)readline("Amount $: ");
            if ($userAmount < 1) {
                continue;
            }
            break;
        }
        if ($userAmount > $this->money) {
            echo "You don't have enough money to purchase." . PHP_EOL;
            return;
        }
        $cryptoList = $this->cryptoApi->getResponse();
        foreach ($cryptoList as $item) {
            if ($userCrypto === $item->getName()) {
                $price = $item->getPrice();
                $totalCrypto = $userAmount / $price;
                $purchase = strtolower(readline("Are you sure you want to purchase {$item->getName()} for $$userAmount (y/n)? "));
                if ($purchase === "n" || $purchase === "no") {
                    return;
                }
                $this->money -= $userAmount;
                echo "You purchased $totalCrypto {$item->getName()} for $$userAmount." . PHP_EOL;
                $this->owned($item->getName(), $totalCrypto);
                $this->transaction("Purchased", $item->getName(), $userAmount, $totalCrypto);
                return;
            }
        }
        echo "Didn't find a match!" . PHP_EOL;
    }

    public function sell()
    {
        $cryptoList = $this->cryptoApi->getResponse();
        if (empty($this->owned)) {
            return;
        }
        $userSell = ucfirst(readline("What crypto you want to sell: "));
        if ($userSell == "") {
            return;
        }
        foreach ($this->owned as $key => $item) {
            if ($userSell === $item['cryptoName']) {
                $sell = strtolower(readline("Are you sure you want to sell $userSell (y/n)? "));
                if ($sell === "n" || $sell === "no") {
                    return;
                }
                foreach ($cryptoList as $crypto) {
                    if ($userSell === $crypto->getName()) {
                        $price = $crypto->getPrice();
                        $totalDollars = $price * $item['value'];
                        $this->money += $totalDollars;
                        $this->transaction("Sold", $item["cryptoName"], $item['value'], $totalDollars);
                        echo "You sold $userSell and received $$totalDollars." . PHP_EOL;
                        unset($this->owned[$key]);
                        $sqlite = new Sqlite();
                        $sqlite->delete($userSell);
                        return;
                    }
                }
            }
        }
        echo "You don't own $userSell." . PHP_EOL;
    }


    public function getTransaction(): array
    {
        return $this->transactions;
    }

    public function getOwned(): array
    {
        return $this->owned;
    }

    public function getMoney(): int
    {
        return $this->money;
    }
}