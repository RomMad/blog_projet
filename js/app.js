class NameClass {
    constructor() {

        this.init();
    }
    // Initialise 
    init() {

    };

    Method() {

    };
};

let nameClass = new NameClass();


// Active Toolips Bootstrap
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

// Active Popover Bootstrap
$(function () {
    $('[data-toggle="popover"]').popover()
})

// $(function () {
//     $('.example-popover').popover({
//         container: 'header'
//     })
// }