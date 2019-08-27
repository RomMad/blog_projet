class SelectAllCheckboxes {
    constructor() {
        this.selectAll = document.getElementById("select-all");
        this.checkboxElts = document.querySelectorAll("table .checkbox");
        this.checked = TRUE;
        this.init();
    }

    init() {
        this.selectAll.addEventListener("click", this.checkAll.bind(this));
    }

    checkAll() {
        if (this.selectAll.checked === TRUE) {
            this.checked = TRUE;
        } else {
            this.checked = FALSE;
        }
        this.checkboxElts.forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }.bind(this));
    }
}