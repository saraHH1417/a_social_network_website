$(document).ready(function() {
    // Button for profile post

    $('#submit_profile_post').click(function() {
        $.ajax({
            type: "POST",
            url: "includes/handlers/ajax_submit_profile_post.php",
            data: $("form.profile_post").serialize(),
            success: function(msg) {
                $("#post_form").modal('hide');
                location.reload();
            },
            error : function () {
                alert('failure');
            }
        });
    });

});
    function getUsers(value, user) {
        $.post("includes/handlers/ajax_friend_search.php" , {query:value , userLoggedin:user} , function(data){
            $('.results').html(data);
        });
    };

    function getDropdownData(username , type) {
        if ($(".dropdown_data_window").css("height") == "0px") {
            let pageName;

            if (type == 'notification') {
                pageName = "ajax_load_notifications.php";
                $("span").remove("#unread_notification");
            }
            else if (type == 'message')
            {
                pageName = "ajax_load_messages.php";
                $("span").remove("#unread_message");
            }

            let ajaxreq = $.ajax({
                url: "includes/handlers/" + pageName,
                type: "POST",
                data: "page=1&userLoggedin=" + username,
                cache: false,

                success: function (response) {
                    $(".dropdown_data_window").html(response);
                    $(".dropdown_data_window").css({
                        "padding": "0",
                        "height": "280px",
                        "border": "1px solid #DADADA"
                    });
                    $("#dropdown_data_type").val(type);
                }
            })

        }else {
            $(".dropdown_data_window").css({"padding" : "0px" , "height" : "0px" , "border" : "none"});
        }
    }
