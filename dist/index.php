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
        <div class="col align-self-center mb-4">
            <h1>Payment - A reference project</h1>
        </div>
    </div>
    <div class="row card-deck">
        <div class="col-12 col-md-4 card">
            <div class="card-body">
                <h2 class="card-title">Accounts</h2>
                <div class="row">
                    <div class="col align-self-left my-2">
                        <h5>Create a new account</h5>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label for="new-account-first-name">First name</label>
                        <input id="new-account-first-name" type="text" class="form-control"/>
                    </div>
                    <div class="col">
                        <label for="new-account-last-name">Last name</label>
                        <input id="new-account-last-name" type="text" class="form-control"/>
                    </div>
                    <div class="col-2 flex">
                        <button id="create-new-account" class="btn btn-outline-primary mt-auto mx-auto">create</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col align-self-left my-2">
                        <h5>Inspect/Edit an existing account</h5>
                    </div>
                </div>
                <div id="account-list" class="list-group"></div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4 card" id="account-info">
            <div class="card-body collapse">
                <h2 class="card-title">Account info</h2>
                <div class="row justify-content-around mb-2">
                    <div class="col">
                        <label for="first-name">First name</label>
                        <input id="first-name" type="text" name="firstname" class="form-control"/>
                    </div>
                    <div class="col-2 flex">
                        <button id="change-first-name" class="btn btn-outline-primary mt-auto mx-auto">save</button>
                    </div>
                </div>
                <div class="row justify-content-between mb-2">
                    <div class="col">
                        <label for="last-name">Last name</label>
                        <input id="last-name" type="text" name="lastname" class="form-control"/>
                    </div>
                    <div class="col-2 flex">
                        <button id="change-last-name" class="btn btn-outline-primary mt-auto mx-auto">save</button>
                    </div>
                </div>

                <div class="row justify-content-between mb-2">
                    <div class="col">
                        <label for="balance">Current balance</label>
                        <input id="balance" type="number" disabled name="balance" class="form-control"/>
                    </div>
                    <div class="col-auto flex">
                        <label class="mt-auto mx-auto">
                            <input id="active" type="checkbox" name="active"/>
                            Account is active
                        </label>
                    </div>
                </div>

                <div class="row justify-content-between mb-2">
                    <div class="col">
                        <label for="transfer">Transfer</label>
                        <input id="transfer" type="number" name="transfer-amount" min="0.01" step="0.01" class="form-control"/>
                    </div>
                    <div class="col">
                        <label>to</label>
                        <select name="transfer-target" class="form-control"></select>
                    </div>
                    <div class="col-2 flex">
                        <button id="transfer-money" class="btn btn-outline-primary mt-auto mx-auto">transfer</button>
                    </div>
                </div>
                <div class="row justify-content-between mb-2">
                    <div class="col">
                        <label for="deposit">Deposit</label>
                        <input id="deposit" type="number" name="deposit-amount" min="0.01" max="5000" step="0.01" class="form-control"/>
                    </div>
                    <div class="col-2 flex">
                        <button id="deposit-money" class="btn btn-outline-primary mt-auto mx-auto">deposit</button>
                    </div>
                </div>
                <div class="row justify-content-between mb-2">
                    <div class="col">
                        <label for="withdraw">Withdraw</label>
                        <input id="withdraw" type="number" name="withdraw-amount" min="0.01" step="0.01" class="form-control"/>
                    </div>
                    <div class="col-2 flex">
                        <button id="withdraw-money" class="btn btn-outline-primary mt-auto mx-auto">withdraw</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4 card" id="account-transactions">
            <div class="card-body collapse table-responsive">
                <h2 class="card-title">Transaction history</h2>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
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

</div>
</body>
</html>