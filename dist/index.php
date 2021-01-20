<?php

require "autoload.php";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Payment</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="bundle.js" defer></script>
</head>
<body>
<div class="container-fluid">

    <div class="row">
        <div class="col align-self-center">
            <h1>Payment - A reference project</h1>
        </div>
    </div>
    <div class="row justify-content-around">
        <div class="col-4">
            <h2>Accounts</h2>
            <ul id="account-list"></ul>
        </div>
        <div class="col-4" id="account-info">
            <h2>Account info</h2>
            <div class="row">
                <label for="first-name">First name</label>
                <input id="first-name" type="text" name="firstname"/>
                <button id="change-first-name">change</button>
            </div>
            <div class="row">
                <label for="last-name">Last name</label>
                <input id="last-name" type="text" name="lastname"/>
                <button id="change-last-name">change</button>
            </div>

            <div class="row">
                <label for="balance">Balance</label>
                <input id="balance" type="number" disabled name="balance"/>
            </div>
            <div class="row">
                <label>
                    <input id="active" type="checkbox" name="active"/>
                    Account is active?
                </label>
            </div>

            <div class="row">
                <label>
                    Transfer
                    <input type="number" name="transfer-amount" default="10"/>
                </label>
                <label>
                    to
                    <select name="transfer-target"></select>
                </label>
                <button id="transfer-money">send</button>
            </div>
            <div class="row">
                <label>
                    Deposit
                    <input type="number" name="deposit-amount" default="10"/>
                </label>
                <button id="deposit-money">deposit</button>
            </div>
            <div class="row">
                <label>
                    withdraw
                    <input type="number" name="withdraw-amount" default="10"/>
                </label>
                <button id="withdraw-money">withdraw</button>
            </div>
        </div>
        <div class="col-4" id="account-transactions">
            <h2>Transaction history</h2>
            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>From</th>
                        <th>To</th>
                    <tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>