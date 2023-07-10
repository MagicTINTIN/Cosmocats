<div id="lbPlayerList">
    <div id="sharelink">
        <div id="sharetexts">
        <h3 id="qrtitle"><?php echo $updtLBtexts[5][$lng] ?></h3>
        <p id="qrlink" onclick="cplink()" ontouchstart="cplink()" title="<?php $updtLBtexts[6][$lng] ?>"></p>
        </div>
        <div id="qrcode"></div>
    </div>
    <h3><?php echo $updtLBtexts[1][$lng] ?></h3>
    <ul>
        <?php
            if (strlen($_SESSION['game']['playerList'] > 0)) {
                $playersArray = explode('┇', $gameData['game']['playerList']);
                $nb = 0;
                foreach ($playersArray as $playerobj) {
                    $nb++;
                    $playerArr = explode('┊', $playerobj);
                    if ($playerArr[0] == $_SESSION['nickname'])
                        echo '<li class="li' . ($nb % 2) . '"><span class="listnumber">' . $nb . '</span>' . $playerArr[0] . '<span class="listyou">' . $updtLBtexts[3][$lng] . '<span></li>';
                    else 
                        echo '<li class="li' . ($nb % 2) . '"><span class="listnumber">' . $nb . '</span>' . $playerArr[0] . '</li>';
                }
            }
        ?>
    </ul>
</div>