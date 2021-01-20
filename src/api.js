import $ from 'jquery';
import collapse from 'bootstrap';

const apiBasePath = "API.php";

let accounts = [];
let currentAccountID = null;

function updateAccountList (){
    $.ajax({
        url: `${apiBasePath}?resource=get_all_accounts`
    }).done((data) => {
        let $list = $("#account-list")
            .empty();
        let $transferSelect = $('#account-info select[name="transfer-target"')
            .empty();
        data.content.map((account) => {
            accounts.push(account);
            $list.append(
                $(`<button>${account.firstname} ${account.lastname}</button>`)
                    .addClass("list-group-item list-group-item-action")
                    .attr("data-id", account.id)
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
    $("#account-list > *")
        .removeClass("active")
        .filter(`[data-id="${accountID}"]`)
            .addClass("active");
    $("#account-info > .collapse, #account-transactions > .collapse")
        .collapse("show");
    $.ajax({
        url: `${apiBasePath}?resource=get_account&id=${accountID}`
    }).done((data) => {
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
                ? "table-danger" : "table-success"
            );

            let date = new Date(Date(transaction.timestamp));
            $transactions.append(
                `<tr class="${cls}"><td>${date.toLocaleDateString()} - ${date.toLocaleTimeString()}</td>`
                +`<td>${transaction.amount}<span class="monetary"></span></td>`
                +`<td>${transaction.account_from}</td>`
                +`<td>${transaction.account_to}</td></tr>`
            );
        });
    });
}

$("#account-list").on("click", (event)=>{
    selectAccount (
        $(event.target).attr("data-id")
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
        //Refresh selected account
        selectAccount(currentAccountID);
        if (
            action == "change_first_name"
            || action == "change_last_name"
            || action == "create_new_account"
        ){
            updateAccountList();
        }
    });
}

$("#create-new-account").on("click", (event)=>{
    requestAction("create_new_account", {
        "first_name": $("#new-account-first-name").val(),
        "last_name": $("#new-account-last-name").val()
    });
});

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