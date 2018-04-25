$(document).ready(function () {

    // ADD IMAGE
    $('.image-uploader').change(function (event) {
        $(this).parents('.images-upload-block').append('<div class="uploaded-block"><img src="' + URL.createObjectURL(event.target.files[0]) + '"><button class="close">&times;</button></div>');
    });

    // REMOVE IMAGE
    $('.images-upload-block').on('click', '.close', function () {
        $(this).parents('.uploaded-block').remove();
    });


    // Start Owl Carousel 

    $("#owl-demo").owlCarousel({

        navigation: false, // Show next and prev buttons
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true,
        pagination: true

    });

    // Start Family Tree

    $(".family-tree .father").click(function () {
        $(".family-tree .sons").slideDown();
    });

    $(".family-tree .sons .son.first").click(function () {
        $(".family-tree .bases").fadeIn(function () {
            $(".family-tree .bases #bas2").fadeOut(function () {
                $(".family-tree .bases #bas1").fadeIn();
            });
        });

        $(".family-tree .sons .son.second").click(function () {
            $(".family-tree .bases #bas1").fadeOut(function () {
                $(".family-tree .bases #bas2").fadeIn();
            });

        });
    });

    //$(".family-tree .sons .son.second").click(function () {
    //    $(".family-tree .bases #bas1").fadeOut();
    //    $(".family-tree .bases #bas2").fadeIn();
    //});


});