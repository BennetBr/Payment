<?php

use PHPUnit\Framework\TestCase;
use Payment\Account;

final class AccountTest extends TestCase {
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

    public function testCanBeCreatedFromValidParameters(): void {
        $a = $this->getAccount();
        $this->assertInstanceOf(
            Account::class,
            $a
        );
    }

    public function testValidIdCanBeDeleted(): void {
        $id = Account::create("Dummy", "Dummy")->getID();
        Account::delete($id);

        $this->expectException(RuntimeException::class);
        Account::getFromID($id);
    }

    public function testInvalidIdCannotBeDeleted(): void {
        $this->expectException(RuntimeException::class);
        Account::delete(-1);
    }

    public function testNameCanBeChanged(): void {
        $a = $this->getAccount();
        $a->setFirstName("A2");
        $a->setLastName("A2");

        $b = Account::getFromID($a->getID());
        $this->assertSame(
            $a->getFirstName(),
            $b->getFirstName()
        );
        $this->assertSame(
            $a->getLastName(),
            $b->getLastName()
        );
    }

    public function testCanBeDeactivated(): Account {
        $a = Account::create("Dummy", "Dummy");
        $a->setActive(false);

        $b = Account::getFromID($a->getID());
        $this->assertFalse(
            $a->isActive()
        );
        $this->assertFalse(
            $b->isActive()
        );

        return $a;
    }

    /**
     * @depends testCanBeDeactivated
     */
    public function testCanBeReActivated(Account $a): void {
        $a->setActive(true);

        $b = Account::getFromID($a->getID());
        $this->assertTrue(
            $a->isActive()
        );
        $this->assertTrue(
            $b->isActive()
        );
        Account::delete($a->getID());
    }
}

?>