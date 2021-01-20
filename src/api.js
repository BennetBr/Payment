import $ from 'jquery';

const apiBasePath = "API.php";

let accounts = [];
let currentAccountID = null;

function updateAccountList (){
    $.ajax({
        url: `${apiBasePath}?resource=get_all_accounts`
    }).done((data) => {
        console.log(data);
        let $list = $("#account-list")
            .empty();
        let $transferSelect = $('#account-info select[name="transfer-target"')
            .empty();
        data.content.map((account) => {
            accounts.push(account);
            $list.append(
                $(`<li>${account.firstname} ${account.lastname}</li>`)
                    .addClass("account")
                    .data("id", account.id)
            )
            $transferSelect.append(`<option value="${account.id}">${account.firstname} ${account.lastname}</option>`);
        });
    });
}

//Update when document has become interactive
$(()=>{
    updateAccountList();
});

function selectAccount (accountID){
    currentAccountID = accountID;
    $.ajax({
        url: `${apiBasePath}?resource=get_account&id=${accountID}`
    }).done((data) => {
        console.log(data);
        let $account = $("#account-info");

        for (name in data.content.account){
            $account.find(`:input[name="${name}"]:not([type="checkbox"])`)
                .val(data.content.account[name]);
            $account.find(`:input[name="${name}"][type="checkbox"]`)
                .prop("checked", data.content.account[name]);
        }

        let $transactions = $("#account-transactions tbody").empty();
        data.content.transactions.map((transaction) => {
            if (transaction.account_from === null){
                transaction.account_from = "DEPOSIT";
            }
            if (transaction.account_to === null){
                transaction.account_to = "WITHDRAW";
            }

            let cls = (
                transaction.account_from_id == data.content.account.id
                ? "bg-danger" : "bg-success"
            );

            $transactions.prepend(
                `<tr class="${cls}"><td>${transaction.amount}</td>`
                +`<td>${transaction.account_from}</td>`
                +`<td>${transaction.account_to}</td></tr>`
            );
        });
    });
}

$("#account-list").on("click", (event)=>{
    selectAccount (
        $(event.target).data("id")
    );
})

function requestAction (action, parameters){
    let query = action;
    for(let key in parameters){
        query += "&"+key+"="+parameters[key];
    }

    $.ajax({
        url: `${apiBasePath}?resource=${query}`
    }).done((data) => {
        console.log(data);
        //Refresh selected account
        selectAccount(currentAccountID);
        if (
            action == "change_first_name"
            || action == "change_last_name"
        ){
            updateAccountList();
        }
    });
}

$("#change-first-name").on("click", (event)=>{
    requestAction("change_first_name", {
        "id": currentAccountID,
        "first_name": $("#first-name").val()
    });
});

$("#change-last-name").on("click", (event)=>{
    requestAction("change_last_name", {
        "id": currentAccountID,
        "last_name": $("#last-name").val()
    });
});

$("#active").on("change", (event)=>{
    requestAction("change_active", {
        "id": currentAccountID,
        "active": ($("#active").prop("checked") ? "true" : "false")
    });
});

$("#transfer-money").on("click", (event)=>{
    let $account = $("#account-info");
    let target = $account.find('select[name="transfer-target"]').val();
    let amount = $account.find('input[name="transfer-amount"]').val();

    if (
        currentAccountID != null
        && target.length > 0
        && amount > 0
    ){
        requestAction("transfer", {
            "from": currentAccountID,
            "to": target,
            "amount": amount
        });
    }
});

$("#deposit-money").on("click", (event)=>{
    let $account = $("#account-info");
    let amount = $account.find('input[name="deposit-amount"]').val();

    if (
        currentAccountID != null
        && amount > 0
    ){
        requestAction("deposit", {
            "id": currentAccountID,
            "amount": amount
        });
    }
});

$("#withdraw-money").on("click", (event)=>{
    let $account = $("#account-info");
    let amount = $account.find('input[name="withdraw-amount"]').val();

    if (
        currentAccountID != null
        && amount > 0
    ){
        requestAction("withdraw", {
            "id": currentAccountID,
            "amount": amount
        });
    }
});