<?php
require "autoload.php";

header('Content-type:application/json;charset=utf-8');

use Payment\Account;
use Payment\Transaction;

$response = [
    "error" => NULL,
    "content" => []
];

try{
    switch ($_GET["resource"]){
        case "get_all_accounts":
            $response["content"] = Account::getAllAccounts();
            break;
        case "get_account":
            $response["content"] = [
                "account" => Account::getFromID((int) $_GET["id"])
                    ->toArray(),
                "transactions" => Transaction::getAllFor((int) $_GET["id"])
            ];
            break;
        case "create_new_account":
            Account::create($_GET["first_name"], $_GET["last_name"]);
            break;
        case "change_first_name":
            Account::getFromID((int) $_GET["id"])
                ->setFirstName($_GET["first_name"]);
            break;
        case "change_last_name":
            Account::getFromID((int) $_GET["id"])
                ->setLastName($_GET["last_name"]);
            break;
        case "change_active":
            Account::getFromID((int) $_GET["id"])
                ->setActive($_GET["active"] == "true");
            break;
        case "transfer":
            Account::getFromID((int) $_GET["from"])
                ->transfer(
                    Account::getFromID((int) $_GET["to"]),
                    (float) $_GET["amount"]
                );
            break;
        case "deposit":
            Account::getFromID((int) $_GET["id"])
                ->deposit((float) $_GET["amount"]);
            break;
        case "withdraw":
            Account::getFromID((int) $_GET["id"])
                ->withdraw((float) $_GET["amount"]);
            break;
        default:
            $response["error"] = "No valid resource specified.";
            break;
    }
} catch (\Exception $e){
    $response["error"] = $e->getMessage();
    http_response_code(400);
}

$json = json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE){
    http_response_code(500);
    $json = '{"error":"'.json_last_error_msg().'","content":[]}';
}
echo $json;