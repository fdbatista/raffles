var ComingSoon = function () {

    return {
        //main function to initiate the module
        init: function () {

            //$.backstretch([], {
    		//          fade: 1000,
    		//          duration: 10000
    		//    });

            var austDay = new Date();
            austDay = new Date(austDay.getFullYear() + 1, 1 - 1, 26);
            $('.pi-price, .price').countdown({until: austDay, compact: true});
            
            //$('#year').text(austDay.getFullYear());
        }

    };

}();