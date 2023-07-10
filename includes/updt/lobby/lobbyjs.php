<script>
    var webpage = `http://${window.location.hostname}/Cosmocats/<?php echo $_SESSION['gameID'] ?>`;
    var qrc = new QRCode(document.getElementById("qrcode"), webpage);
    qrlink = document.getElementById("qrlink");
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