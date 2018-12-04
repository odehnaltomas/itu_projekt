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

    $("#sound").click(function() {
        play();
    });

    $("#exitTestButton").click(function(){
        $("#exitTestModal").modal();
    });

    var maxHeight = 300;

    // $(".jmb-test").each(function(){
    //     maxHeight = $(this).height() /20;
    // });
    //
    // $(".test-img").height(maxHeight);
});
