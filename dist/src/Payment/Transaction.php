<?php

namespace Payment;

use Payment\DB\Connection;
use Payment\Exceptions\InactivityException;
use Payment\Exceptions\OverdrawException;

/**
 * Transactions belong to accounts and document the move of cash.
 */
class Transaction {

    protected function __construct (
        private ?int $id,
        private ?Account $from,
        private ?Account $to,
        private float $amount,
        private bool $committed,
        private ?int $timestamp = NULL
    ){}

    public static function beginTransaction (
        ?Account $from, ?Account $to, float $amount
    ): static {
        if ($from === NULL && $to === NULL){
            throw new \UnexpectedValueException ("A transaction needs at least one account to transfer from or transfer to!");
        }elseif ($from?->getID() === $to?->getID()){
            throw new \UnexpectedValueException ("A transaction cannot transfer funds from an account onto itself!");
        }
        return new static (NULL, $from, $to, $amount, false, NULL);
    }

    public function getFromAccount (): ?Account {
        return $this->from;
    }

    public function getToAccount (): ?Account {
        return $this->to;
    }

    public function getAmount (): float {
        return $this->amount;
    }

    public function isCommitted (): bool {
        return $this->committed;
    }

    public function getTimestamp (): ?int {
        return $this->timestamp;
    }

    public function commit (): void {
        if ($this->isCommitted()){
            throw new \BadMethodCallException("Error while committing transaction - The transaction is already commited!");
        }
        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            //Write transaction
            $stmt = $con->prepare("INSERT INTO `transactions` (account_from, account_to, amount) VALUES (:from, :to, :amount);");
            $stmt->execute([
                "from" => $this->getFromAccount()?->getID(),
                "to" => $this->getToAccount()?->getID(),
                "amount" => $this->getAmount()
            ]);

            if ($stmt->rowCount() !== 1){
                throw new \RuntimeException("Error while committing transaction! Affected {$stmt->rowCount()} rows!");
            }
            
            $this->id = $con->lastInsertId();
            $stmt = $con->prepare("SELECT UNIX_TIMESTAMP(timestamp) AS time FROM `transactions` WHERE id = ?;");
            $stmt->execute([$this->id]);
            $this->timestamp = $stmt->fetch(\PDO::FETCH_ASSOC)["time"];
    
            //Update accounts to reflect the transaction
            if ($this->getFromAccount() instanceof Account){
                $this->getFromAccount()->setBalance(
                    $this->getFromAccount()->getBalance() - $this->getAmount()
                );
            }
            
            if ($this->getToAccount() instanceof Account){
                $this->getToAccount()->setBalance(
                    $this->getToAccount()->getBalance() + $this->getAmount()
                );
            }

            $con->commit();
            $this->committed = true;
        } catch (\Exception $e){
            $con->rollback();
            throw $e;
        }
    }

    public static function getAllTransactionsFor (Account $account): array {
        $stmt = Connection::getInstance()
            ->prepare("SELECT * FROM `transactions` WHERE account_from = :id OR account_to = :id;");
        $stmt->execute([
            "id" => $account->getID()
        ]);

        $transactions = [];

        while ($transaction = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $transactions[] = new static (
                $transaction["id"],
                $transaction["account_from"],
                $transaction["account_to"],
                $transaction["amount"],
                true,
                $transaction["timestamp"]
            );
        }
        return $transactions;
    }
}

?>