var userToken = "";
function hideLogin() {
    $("#authentication").hide();
    $("#authorPage").show();
}

function returnToLogin() {
    $("#authorPage").hide();
    $("#authentication").show();
    $("#app").hide();
}


$(document).ready(function() {
    $("#authenticateBtn").click(function() {;
        authenticateUser();
    });
   
});

function authenticateUser() {
    var username = $("#username").val();
    var password = $("#password").val();
    dataObj = {username: username, password: password};
    $.ajax({ url: "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/v1/user",
             method: "POST",
             data: JSON.stringify(dataObj),
             contentType: "application/json",
             success: function (response) {
                     alert("Response:" + response.message);
                     if(response.message == "Valid credentials") {
                     userToken = response.token;
                     showData();
                     hideLogin();
                     }
              },
             error: function (errorData) {
                     alert(errorData);
             },
        });
}

function showData() {
    $("#authentication").hide();

    var url = "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/v1/items";
    $.ajax({ url: url,
    method: "GET",
    contentType: "application/json",
    success: function (response) {
            var array = response.items;
            var d = "   <table id=\"itemTable\" class=\"table\"> <tr><th>Key</th><th>Item</th>";
            array.forEach(element=> {
                    var pk = JSON.stringify(element.pk).replace(/\"/g, "");
                    var item = JSON.stringify(element.item).replace(/\"/g, "");
                    d += "<tr><td>" + pk + "</td>";
                    d += "<td><button class=\"btn btn-primary\" pk=" + pk + " onclick='recordItem(this)'>" + item + "</button></td></tr>";
            })
            d += "</table>";
            $("#data").html(d);
     },
    error: function (errorData) {
            alert(errorData);
            console.log(errorData);
    },
});
}

function recordItem(whichButton) {
    var itemPk = $(whichButton).attr('pk');
    dataObj = {pk: itemPk, token: userToken};
    var url = "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/v1/items";
    $.ajax({ url: url,
        method: "POST",
        data: JSON.stringify(dataObj),
        contentType: "application/json",
        success: function (response) {
                alert("Response:" + response.msg);
                console.log(response);
                getItemSummary();
         },
        error: function (errorData) {
                alert(errorData);
                console.log(errorData);
        },
    });
}

function getItemSummary() {
    var url = "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/v1/itemsSummary/" + userToken;
    $.ajax({ url: url,
    method: "GET",
    contentType: "application/json",
    success: function (response) {
            var array = response.summary;
            var d = "<table id=\"summaryTable\" class=\"table\"> <tr><th>Item</th><th>Count</th>";
            array.forEach(element=> {
                    var item = JSON.stringify(element.item).replace(/\"/g, "");
                    var count = JSON.stringify(element.count).replace(/\"/g, "");
                    d += "<tr><td>" + item + "</td>";
                    d += "<td>" + count + "</td></tr>";
            })
            d += "</table>";
            $("#diarySummary").html(d);
            getItemsByUser();

     },
    error: function (errorData) {
            alert(errorData);
            console.log(errorData);
    },
});
}

function getItemsByUser() {
    var url = "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/v1/items/" + userToken;
    $.ajax({ url: url,
        method: "GET",
        contentType: "application/json",
        success: function (response) {
                var array = response.items;
                var d = "<table id=\"diaryTable\" class=\"table\"> <tr><th>Item</th><th>Timestamp</th>";
                array.forEach(element=> {
                var item = JSON.stringify(element.item).replace(/\"/g, "");
                var timestamp = JSON.stringify(element.timestamp).replace(/\"/g, "");
                d += "<tr><td>" + item + "</td>";
                d += "<td>" + timestamp + "</td></tr>";
            })
            d += "</table>";
            $("#diary").html(d);
                
    
         },
        error: function (errorData) {
                alert(errorData);
                console.log(errorData);
        },
    });   
}
