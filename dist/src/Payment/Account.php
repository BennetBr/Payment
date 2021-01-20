<?php

namespace Payment;

use Payment\DB\Connection;
use Payment\Exceptions\InactivityException;
use Payment\Exceptions\OverdrawException;

/**
 * Accounts belong to people and hold a certain amount of cash.
 */
class Account {

    /** Specifies how much money can be overdrawn with transfers */
    public const TRANSFER_OVERDRAW_LIMIT = -200;

    /** Specifies how much money can be deposited in one transaction */
    public const DEPOSIT_LIMIT = 5000;

    protected function __construct (
        private int $id,
        private string $firstName,
        private string $lastName,
        private bool $active,
        private float $balance
    ){}

    public function getID (): int {
        return $this->id;
    }

    public function getFirstName (): string {
        return $this->firstName;
    }

    public function setFirstName (string $firstName): void {
        if ($firstName === $this->firstName) return;

        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $stmt = Connection::getInstance()
                ->prepare("UPDATE `accounts` SET firstname = :name WHERE id = :id;");
            $stmt->execute([
                "name" => $firstName,
                "id" => $this->id
            ]);

            if ($stmt->rowCount() !== 1){
                throw new \RuntimeException("Error while updating accounts firstName! Affected {$stmt->rowCount()} rows!");
            }else{
                $con->commit();
                $this->firstName = $firstName;
            }
        }catch (\Exception $e){
            $con->rollBack();
            throw $e;
        }
    }

    public function getLastName (): string {
        return $this->lastName;
    }

    public function setLastName (string $lastName): void {
        if ($lastName === $this->lastName) return;

        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $stmt = Connection::getInstance()
                ->prepare("UPDATE `accounts` SET lastname = :name WHERE id = :id;");
            $stmt->execute([
                "name" => $lastName,
                "id" => $this->id
            ]);

            if ($stmt->rowCount() !== 1){
                throw new \RuntimeException("Error while updating accounts lastName! Affected {$stmt->rowCount()} rows!");
            }else{
                $con->commit();
                $this->lastName = $lastName;
            }
        }catch (\Exception $e){
            $con->rollBack();
            throw $e;
        }
    }

    public function getBalance (): float {
        return $this->balance;
    }

    public function setBalance (float $newBalance): void {
        $stmt = Connection::getInstance()->prepare("UPDATE `accounts` SET balance = :balance WHERE id = :id;");
        $stmt->execute([
            "balance" => $newBalance,
            "id" => $this->getID()
        ]);

        if ($stmt->rowCount() !== 1){
            throw new \RuntimeException("Error while updating accounts balance! Affected {$stmt->rowCount()} rows!");
        }else{
            $this->balance = $newBalance;
        }
    }

    public function isActive (): bool {
        return $this->active;
    }

    public function setActive (bool $status): void {
        if ($status === $this->active) return;

        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $stmt = Connection::getInstance()
                ->prepare("UPDATE `accounts` SET active = :active WHERE id = :id;");
            $stmt->execute([
                "active" => ($status ? 1 : 0),
                "id" => $this->getID()
            ]);

            if ($stmt->rowCount() !== 1){
                throw new \RuntimeException("Error while updating accounts active status! Affected {$stmt->rowCount()} rows!");
            }else{
                $con->commit();
                $this->active = $status;
            }
        } catch (\Exception $e){
            $con->rollBack();
            throw $e;
        }
    }

    public function transfer (self $targetAccount, float $amount): void {
        if ($amount <= 0){
            throw new \UnderflowException("Invalid transfer amount - Only positive amounts may be transferred!");
        }elseif (!$this->isActive()){
            throw new InactivityException("Error while transferring money - The origin account is inactive!");
        }elseif (!$targetAccount->isActive()){
            throw new InactivityException("Error while transferring money - The target account is inactive!");
        }elseif ($this->getBalance() - $amount < static::TRANSFER_OVERDRAW_LIMIT){
            throw new OverdrawException("Error while transferring money - The account overdraw would exceed the overdraw limit!");
        }

        Transaction::beginTransaction ($this, $targetAccount, $amount)->commit();
    }

    public function deposit (float $amount): void {
        if ($amount <= 0){
            throw new \UnderflowException("Invalid deposit amount - Only positive amounts may be deposited!");
        }elseif ($amount > static::DEPOSIT_LIMIT){
            throw new \OverflowException("Invalid deposit amount - Only up to ".static::DEPOSIT_LIMIT." may be deposited!");
        }else{
            Transaction::beginTransaction (NULL, $this, $amount)->commit();
        }
    }

    public function withdraw (float $amount): void {
        if ($amount <= 0){
            throw new \UnderflowException("Invalid withdraw amount - Only positive amounts may be withdrawn!");
        }elseif ($this->getBalance() - $amount < 0){
            throw new OverdrawException("Invalid withdraw amount - withdrawing this much money would overdraw the account!");
        }else{
            Transaction::beginTransaction ($this, NULL, $amount)->commit();
        }
    }

    /**
     * Fetches all accounts from the database.
     * 
     * @return array All accounts as an array of accounts.
     */
    public static function getAllAccounts (): array {
        $accounts = [];
        $stmt = Connection::getInstance()
            ->prepare("SELECT * FROM `accounts`;");
        $stmt->execute([]);

        while ($accountData = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $accounts[] = new static (
                $accountData["id"],
                $accountData["firstname"],
                $accountData["lastname"],
                $accountData["active"] > 0,
                $accountData["balance"]
            );
        }
        return $accounts;
    }

    /**
     * Fetches all accounts from the database.
     * 
     * @return array All accounts as an array of accounts.
     */
    public static function getFromID (int $id): Account {
        $stmt = Connection::getInstance()
            ->prepare("SELECT * FROM `accounts` WHERE id = :id;");
        $stmt->execute([
            "id" => $id
        ]);

        if ($stmt->rowCount() !== 1){
            throw new \RuntimeException("Error while fetching account! Affected {$stmt->rowCount()} rows!");
        }else{
            $accountData = $stmt->fetch(\PDO::FETCH_ASSOC);
            return new static (
                $accountData["id"],
                $accountData["firstname"],
                $accountData["lastname"],
                $accountData["active"] > 0,
                $accountData["balance"]
            );
        }
    }

    public static function create (string $firstName, string $lastName): Account {
        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $stmt = $con->prepare("INSERT INTO `accounts` (firstname, lastname, active, balance) VALUES (:first, :last, b'1', 0);");
            $stmt->execute([
                "first" => $firstName,
                "last" => $lastName
            ]);
    
            if ($stmt->rowCount() !== 1){
                throw new \RuntimeException("Error while creating an account! Affected {$stmt->rowCount()} rows!");
            }
            $id = $con->lastInsertId();

            $con->commit();
            return new static ($id, $firstName, $lastName, 1, 0);
        } catch (\Exception $e){
            $con->rollBack();
            throw $e;
        }
    }

    public static function delete (int $id): void {
        $stmt = Connection::getInstance()
            ->prepare("DELETE FROM `accounts` WHERE id = :id;");
        $stmt->execute([
            "id" => $id
        ]);

        if ($stmt->rowCount() !== 1){
            throw new \RuntimeException("Error while deleting account $id! Affected {$stmt->rowCount()} rows!");
        }
    }
}