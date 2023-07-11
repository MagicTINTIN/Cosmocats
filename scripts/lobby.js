var ctx = document.createElement("canvas").getContext("2d");

const colors = {
    black: 'Jacata',
    gray: 'Ayacat',
    red: 'Reskat',
    green: 'Gatils',
    yellow: 'Ermao',
    blue: 'Stagat',
    white: 'Freezcat'
}

for (const color in colors) {

    $(`#${color}`).on("mousedown", function (event) {

        // Get click coordinates
        var x = event.pageX - this.offsetLeft,
            y = event.pageY - this.offsetTop,
            w = ctx.canvas.width = this.width,
            h = ctx.canvas.height = this.height,
            alpha;

        // Draw image to canvas
        // and read Alpha channel value
        ctx.drawImage(this, 0, 0, w, h);
        alpha = ctx.getImageData(x, y, 1, 1).data[3]; // [0]R [1]G [2]B [3]A

        // If pixel is transparent,
        // retrieve the element underneath and trigger it's click event
        if (alpha === 0) {
            this.style.pointerEvents = "none";
            $(document.elementFromPoint(event.clientX, event.clientY)).trigger("click");
            this.style.pointerEvents = "auto";
        } else {
            console.log(`${color} clicked!`);
        }
    });

}


$("#backgroundMapImg").on("click", function () {
    console.log("Background image clicked!");
});