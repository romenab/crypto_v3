<?php

namespace CryptoTrade\App;

use Medoo\Medoo;

class Sqlite
{
    private Medoo $db;

    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'sqlite',
            'database_name' => 'app/storage/database.sqlite',
        ]);
        $this->create();
    }

    private function create(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS wallet (
            money INTEGER
        )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS transactions (
            trade TEXT,
            cryptoName TEXT,
            spent REAL,
            received REAL
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS owned (
            cryptoName TEXT,
            value REAL
        )");
    }
    public function delete(string $cryptoName): void
    {
        $this->db->delete("owned", ["cryptoName" => $cryptoName]);
    }

    public function insert(Wallet $wallet): void
    {

        $money = $wallet->getMoney();
        $this->db->update("wallet", ["money" => $money]);


        foreach ($wallet->getTransaction() as $transaction) {
            $isTransition = $this->db->get("transactions", "*", [
                "AND" => [
                    "trade" => $transaction['trade'],
                    "cryptoName" => $transaction['cryptoName'],
                    "spent" => $transaction['spent'],
                    "received" => $transaction['received']
                ]
            ]);

            if (!$isTransition) {
                $this->db->insert("transactions", $transaction);
            }
        }

        foreach ($wallet->getOwned() as $owned) {
            $isOwned = $this->db->get("owned", "*", [
                "AND" => [
                    "cryptoName" => $owned['cryptoName'],
                    "value" => $owned['value']
                ]
            ]);
            if (!$isOwned) {
                $this->db->insert("owned", $owned);
            }
        }
    }

    public function loadTransactions(): array
    {
        return $this->db->select("transactions", ["trade", "cryptoName", "spent", "received"]);
    }

    public function loadOwned(): array
    {
        return $this->db->select("owned", ["cryptoName", "value"]);
    }

    public function loadMoney(): float
    {
        $money = $this->db->get("wallet", "money");
        if ($money === null) {
            $this->db->insert("wallet", ["money" => 1000]);
            return 1000;
        }
        return (float)$money;
    }
}