<script>
var webpage = `http://${window.location.hostname}/Cosmocats/<?php echo $_SESSION['gameID'] ?>`;
// var qrc = new QRCode(document.getElementById("qrcode"), webpage);
// qrlink = document.getElementById("qrlink");
qrlink.innerText = webpage;

let timer1, timer2, timer3;
    
function copyAnim(success) {
    let color = (success) ? 'green' : 'red';
    let msg = (success) ? "<?php echo $updtLBtexts[7][$lng] ?>" : "<?php echo $updtLBtexts[8][$lng] ?>";
    qrlink.style.width = `${qrlink.getBoundingClientRect().width - 10}px`;
    qrlink.style.textAlign = "center";
    qrlink.style.backgroundColor = `var(--${color})`;
    qrlink.innerText = msg;

    timer1 = setTimeout(() => {
        qrlink.style.backgroundColor = `var(--dark-${color})`;
    }, 400);
    timer2 = setTimeout(() => {
        qrlink.style.backgroundColor = "";
    }, 700);
    timer3 = setTimeout(() => {
        document.getElementById("qrlink").innerText = webpage;
        qrlink.style.textAlign = "left";
    }, 2000);
}

async function cplink() {
    clearTimeout(timer1);
    clearTimeout(timer2);
    clearTimeout(timer3);

    let copiedstatus = await copytcb(webpage);
    copyAnim(!copiedstatus);
}

</script>

<?php if (true) { ?>
<!-- <script>jQuery.noConflict();</script> -->

<script>



$("#backgroundMapImg").on("click", function () {
    console.log("Background image clicked!");
});

<?php 
$countrylistforjs = [
    'black' => 'end',
    'gray' => 'black',
    'red' => 'gray',
    'green' => 'red',
    'yellow' => 'green',
    'blue' => 'yellow',
    'white' => 'blue'
];

?>

<?php foreach ( $countrylistforjs as $color => $name) { ?>

var ctx<?php echo $color ?>;

$('#<?php echo $color ?>Img').on("click", function (event) {
    var x = event.pageX - this.offsetLeft,
        y = event.pageY - this.offsetTop;

    var alpha = ctx<?php echo $color ?>.getImageData(x, y, 1, 1).data[3]; // [0]R [1]G [2]B [3]A

    // If pixel is transparent,
    // retrieve the element underneath and trigger it's click event
    console.log('alpha <?php echo $color ?>', alpha);
    if (alpha === 0) {
        this.style.pointerEvents = "none";
        console.log(document.elementFromPoint(event.clientX, event.clientY));
        $(document.elementFromPoint(event.clientX, event.clientY)).trigger("click");
        this.style.pointerEvents = "auto";
    } else {
        console.log(`<?php echo $color ?> clicked!`);
        this.style.transition = 'all .5s';
        this.style.filter = 'brightness(10) invert(1)';
        setTimeout(() => {
            this.style.filter = 'brightness(10) invert(.5)';
            this.style.transition = 'all 8s';
        }, 1000);
    }
});

<?php
}
?>




function setCtxold() {
<?php foreach ( array_reverse($countrylistforjs) as $color => $name) { ?>
    ////////////////// <?php echo $color ?> \\\\\\\\\\\\\\\\\\

    

    ctx<?php echo $color ?> = document.createElement("canvas<?php echo $color ?>").getContext("2d");

    var object<?php echo $color ?> = $('#<?php echo $color ?>Img');
    // Get click coordinates
    var w<?php echo $color ?> = ctx<?php echo $color ?>.canvas.width = object<?php echo $color ?>.width,
        h<?php echo $color ?> = ctx<?php echo $color ?>.canvas.height = object<?php echo $color ?>.height;
    // Draw image to canvas
    // and read Alpha channel value

    ctx<?php echo $color ?>.drawImage(object<?php echo $color ?>, 0, 0, w<?php echo $color ?>, h<?php echo $color ?>);
    console.log('ctx<?php echo $color ?> : ', ctx<?php echo $color ?>)
    
    var checkExist<?php echo $color ?> = setInterval(function() {
        if ($('#canvas<?php echo $color ?>').length) {
            console.log("Exists!");
            clearInterval(checkExist<?php echo $color ?>);
        }
    }, 100)


<?php } ?>    
}


Promise.all(Array.from(document.images).map(img => {
    if (img.complete)
        return Promise.resolve(img.naturalHeight !== 0);
    return new Promise(resolve => {
        img.addEventListener('load', () => resolve(true));
        img.addEventListener('error', () => resolve(false));
    });
})).then(results => {
    if (results.every(res => res)) {

        console.log('all images loaded successfully');
        setCtxold()
    }
    else
        console.log('some images failed to load, all finished loading');
});

</script>

<?php } ?>