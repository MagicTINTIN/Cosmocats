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


<script>
$("#backgroundMapImg").on("click", function () {
    console.log("Background image clicked!");
    resetImages();
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

fullyinitialised = false;
globalPX = 0, globalPY = 0, globalCX = 0, globalCY = 0;

function resetPE() {
    <?php foreach ( $countrylistforjs as $color => $name) { ?>
    document.getElementById('<?php echo $color ?>Img').style.pointerEvents = "auto";
    <?php } ?>
}

function resetImages() {
    <?php foreach ( $countrylistforjs as $color => $name) { ?>
    document.getElementById('<?php echo $color ?>Img').src = 'images/map/elements/<?php echo $color ?>/base.png';
    <?php } ?>
}

<?php foreach ( $countrylistforjs as $color => $name) { ?>

var ctx<?php echo $color ?>;

$('#<?php echo $color ?>Img').on("click", function (event) {
    // console.log('Clicked on : ', this);
    
    var eventPX, eventPY, eventCX, eventCY;
    if (event.pageX == 0 && event.pageY == 0 && event.clientX == 0 && event.clientY == 0) {
        eventPX = globalPX;
        eventPY = globalPY;
        eventCX = globalCX;
        eventCY = globalCY;
    }
    else {
        eventPX = globalPX = event.pageX;
        eventPY = globalPY = event.pageY;
        eventCX = globalCX = event.clientX;
        eventCY = globalCY = event.clientY;
    }
    var x = eventPX - this.offsetLeft,
        y = eventPY - this.offsetTop;
        
    var alpha = ctx<?php echo $color ?>.getImageData(x, y, 1, 1).data[3];

    if (alpha === 0) {
        this.style.pointerEvents = "none";
        
        document.elementFromPoint(eventCX, eventCY).click();
        <?php if ($color == "black") { ?>resetPE();<?php } ?>
        
    } else {
        console.log(`<?php echo $color ?> clicked!`);
        resetPE();
        resetImages();
        this.src = 'images/map/elements/<?php echo $color ?>/selected.png';
    }
});

<?php
}
?>

function setCtx() {
<?php foreach ( array_reverse($countrylistforjs) as $color => $name) { ?>
    ////////////////// SET <?php echo $color ?> \\\\\\\\\\\\\\\\\\
    ctx<?php echo $color ?> = document.createElement("canvas").getContext("2d");
    object<?php echo $color ?> = document.getElementById('<?php echo $color ?>Img');
    

<?php } ?>
    setCtxDraw()
}

function setCtxDraw() {
<?php foreach ( array_reverse($countrylistforjs) as $color => $name) { ?>
    ////////////////// DRAW <?php echo $color ?> \\\\\\\\\\\\\\\\\\
    var w<?php echo $color ?> = ctx<?php echo $color ?>.canvas.width = object<?php echo $color ?>.width,
        h<?php echo $color ?> = ctx<?php echo $color ?>.canvas.height = object<?php echo $color ?>.height;

    ctx<?php echo $color ?>.drawImage(object<?php echo $color ?>, 0, 0, w<?php echo $color ?>, h<?php echo $color ?>);


<?php } ?>
    fullyinitialised = true;
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

        console.log('All images loaded successfully');
        setCtx()
    }
    else
        console.log('Some images failed to load, all finished loading\nIt might be a source of problem');
        setCtx()
});

</script>