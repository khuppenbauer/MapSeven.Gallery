var OwlCarousel = function () {

    return {
        init: function (id, items, navigation, pagination, autoPlay, stopOnHover) {
            var owl = jQuery("#" + id).owlCarousel({
                navigation : navigation, // Show next and prev buttons
                pagination : pagination,
                autoPlay : autoPlay,
                stopOnHover : stopOnHover,
                items: items
            });
        }
    };

}();