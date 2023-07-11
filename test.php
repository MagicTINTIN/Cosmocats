<html>
<head>
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
    $( document ).ready(function() {
        console.log( "document loaded" );
    });
 
    $( window ).on( "load", function() {
        console.log( "window loaded" );
    });
    </script>
</head>
<body>
    <img id="test" src="images/map/elements/black/base.png">
    <script>
        $('#test').on("click", function (event) {
            console.log(event);
            console.log("x", event.pageX, event.clientX, this.offsetLeft,  event.pageX - this.offsetLeft);
            console.log("y", event.pageY, event.clientY, this.offsetTop,  event.pageY - this.offsetTop);
        });

        function click1(x, y) {
            document.elementFromPoint(x, y).click();
        }
        function click2(x, y) {
            $(document.elementFromPoint(x, y)).trigger("click");
        }
    </script>
</body>
</html>