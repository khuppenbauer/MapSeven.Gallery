var OwlCarousel = function () {

    return {
        init: function (id, items, navigation, pagination, autoPlay, stopOnHover, autoHeight, singleItem) {
            var owl = jQuery("#" + id).owlCarousel({
                navigation : navigation, // Show next and prev buttons
                pagination : pagination,
                navigationText: [
                    "<a class='left carousel-control' role='button' data-slide='prev'><span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span><span class='sr-only'>Previous</span></a>",
                    "<a class='right carousel-control' role='button' data-slide='next'><span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span><span class='sr-only'>Next</span></a>"
                ],
                autoPlay : autoPlay,
                stopOnHover : stopOnHover,
                items: items,
                autoHeight: autoHeight,
                singleItem : singleItem
            });
        }
    };

}();