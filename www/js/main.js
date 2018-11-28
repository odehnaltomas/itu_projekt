$(document).on("click", function(){
    $('[data-toggle="popover"]').popover();
});

// $(".test-ans-button").click(function(){
//     $(".test-ans-button").removeClass("test-btn-active");
//     $(this).addClass("test-btn-active");
//     $(this).removeClass("test-ans-active");
// });

$(document).ready(function(){
    $("#startTestButton").click(function(){
        $("#startTestModal").modal();
    });
});
