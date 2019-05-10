function hideLogin() {
    $("#authentication").hide();
    $("#authorPage").show();
}

function returnToLogin() {
    $("#authorPage").hide();
    $("#authentication").show();
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
                     //showData(response.token);
              },
             error: function (errorData) {
                     alert(errorData);
             },
        });
}

function showData(token) {
    $("#authentication").hide();

    $url = "http://ceclnx01.cec.miamioh.edu/~yangj17/cse383/383-finalproject-yangj17-schroe16/rest.php/items/" + token;
    $.ajax({ url: url,
    method: "POST",
    contentType: "application/json",
    success: function (response) {
            alert("Response:" + response.message);
            console.log(errorData);
     },
    error: function (errorData) {
            alert(errorData);
            console.log(errorData);
    },
});
}