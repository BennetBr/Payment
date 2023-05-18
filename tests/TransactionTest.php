<?php

use PHPUnit\Framework\TestCase;
use Payment\Account;
use Payment\Exceptions\InactivityException;
use Payment\Exceptions\OverdrawException;

final class TransactionTest extends TestCase {
    public static array $accounts = [];

    protected function tearDown(): void {
        foreach (static::$accounts as $account){
            Account::delete($account->getID());
        }
        static::$accounts = [];
    }

    /**
     * Gets a fresh account which will be deleted afterwards.
     * 
     * This ensures that no cross contamination between tests
     * is happening.
     */
    public function getAccount(): Account {
        static::$accounts[] = $account = Account::create("Dummy", "Dummy");
        return $account;
    }

    /* ----------------- Transfer ------------------- */

    public static function validTransferProvider (): array {
        return [
            "100€ - 1€" => [100,1],
            "100€ - 100€" => [100,100],
            "100.000€ - 90.000€" => [100000,90000],
            "1€ - 56ct" => [1, 0.56],
            "0€ - 200€" => [0, 200]
        ];
    }

    /**
     * @dataProvider validTransferProvider
     */
    public function testAccountCanTransferValidAmount(
        float $initalAmount, float $transferAmount
    ): void {
        $a = $this->getAccount();
        if ($initalAmount != 0){
            $a->setBalance($initalAmount);
        }
        $b = $this->getAccount();

        $a->transfer($b, $transferAmount);

        $this->assertEquals(
            $b->getBalance(),
            $transferAmount
        );
        $this->assertEquals(
            $a->getBalance(),
            $initalAmount - $transferAmount
        );
    }

    public static function invalidTransferProvider (): array {
        return [
            "0€ - 0€" => [0,0, \UnderflowException::class],
            "0€ - 300€" => [0,300, OverdrawException::class],
            "200€ - 400€ 1ct" => [200,400.01, OverdrawException::class]
        ];
    }

    /**
     * @dataProvider invalidTransferProvider
     */
    public function testAccountCannotTransferInvalidAmount(
        float $initalAmount, float $transferAmount, string $exceptionClass
    ): void {
        $a = $this->getAccount();
        if ($initalAmount != 0){
            $a->setBalance($initalAmount);
        }
        $b = $this->getAccount();

        $this->expectException($exceptionClass);
        $a->transfer($b, $transferAmount);
    }

    public function testAccountCannotTransferToInactiveAccount(): void {
        $a = $this->getAccount();
        $a->setBalance(100);
        $a->setActive(true);
        $b = $this->getAccount();
        $b->setActive(false);

        $this->expectException(InactivityException::class);
        $a->transfer($b, 1);
    }

    public function testAccountCannotTransferFromInactiveAccount(): void {
        $a = $this->getAccount();
        $a->setBalance(100);
        $a->setActive(false);
        $b = $this->getAccount();
        $b->setActive(true);

        $this->expectException(InactivityException::class);
        $a->transfer($b, 1);
    }

    /* ----------------- Deposit -------------------- */

    public static function validDepositProvider (): array {
        return [
            "1€" => [1],
            "100€" => [100],
            "5000€" => [5000],
            "56ct" => [0.56],
            "4500€ + 70ct" => [4500.7]
        ];
    }

    /**
     * @dataProvider validDepositProvider
     */
    public function testAccountCanDepositValidAmount(float $amount): void {
        $a = $this->getAccount();
        $a->deposit($amount);

        $this->assertEquals(
            $amount,
            $a->getBalance()
        );
    }

    public static function invalidDepositProvider (): array {
        return [
            "0€" => [0, \UnderflowException::class],
            "6000€" => [6000, \OverflowException::class],
            "5000€ + 1ct" => [5000.01, \OverflowException::class],
            "-1€" => [-1, \UnderflowException::class],
            "-1ct" => [-0.01, \UnderflowException::class]
        ];
    }

    /**
     * @dataProvider invalidDepositProvider
     */
    public function testAccountCannotDepositInvalidAmount(float $amount, string $exceptionClass): void {
        $this->expectException($exceptionClass);
        $this->getAccount()->deposit($amount);
    }

    /* ----------------- Withdraw -------------------- */

    public static function validWithdrawProvider (): array {
        return [
            "1€ - 1€" => [1, 1],
            "100€ - 100€" => [100, 100],
            "10.000€ - 9.900€" => [10000, 9900],
            "1€ - 56ct" => [1, 0.56]
        ];
    }

    /**
     * @dataProvider validWithdrawProvider
     */
    public function testAccountCanWithdrawValidAmount(
        float $initialAmount, float $amount
    ): void {
        $a = $this->getAccount();
        if ($initialAmount != 0){
            $a->setBalance($initialAmount);
        }
        $a->withdraw($amount);

        $this->assertEquals(
            $initialAmount - $amount,
            $a->getBalance()
        );
    }

    public static function invalidWithdrawProvider (): array {
        return [
            "1€ - 0€" => [1, 0, \UnderflowException::class],
            "1€ - -1€" => [1, -1, \UnderflowException::class],
            "0€ - 1€" => [0, 1, OverdrawException::class],
            "1€ - 1€ 1ct" => [1, 1.01, OverdrawException::class]
        ];
    }

    /**
     * @dataProvider invalidWithdrawProvider
     */
    public function testAccountCannotWithdrawInvalidAmount(
        float $initalAmount, float $amount, string $exceptionClass
    ): void {
        $a = $this->getAccount();
        if ($initalAmount != 0){
            $a->setBalance($initalAmount);
        }
        $this->expectException($exceptionClass);
        $a->withdraw($amount);
    }
}
