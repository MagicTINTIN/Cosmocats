<?php session_start();
$language = (!empty($_COOKIE['language'])) ? $_COOKIE['language'] : 'fr';

if ($language == 'en')
    $lng = 1;
else
    $lng = 0;

$updtLBtexts = [
    [ "Carte du monde", "World map" ],
    [ "Liste des joueurs", "Player list"],
    [ "Jouer !", "Play !" ],
    [ " (vous) ", " (you) " ],
    [ "Quelque chose de bizarre s'est passé...", "Something wrong has occured..."],
    [ "Partagez la partie", "Share the game" ],
    [ "Cliquez pour copier", "Click to copy" ],
];

if ( !(isset($_SESSION['ID']) && isset($_SESSION['gameID']) && isset($_SESSION['game']) && isset($_SESSION['nickname'])) ) {
    include('includes/clear.php');
    header("Location: ../../");
    exit();
}

include_once('../fcts/db.php');
include_once('../fcts/lobby.php');

$gameData = gameStatus($_SESSION['ID'], $_SESSION['gameID']);
if (!$gameData['found'])
{
    $_SESSION['errorMsg'] = $updtLBtexts[4][$lng];
    header("Location: ../../");
    exit();
}

$_SESSION['game'] = $gameData['game']

?>

<div id="lobbyDiv">
    <div id="lbMap">
        <h3><?php echo $updtLBtexts[0][$lng] ?></h3>
    </div>
    <div id="lbSeparator"></div>
    <div id="lbLeftPanel">
        <div id="lbPlayerList">
            <div id="sharelink">
                <div id="sharetexts">
                <h3 id="qrtitle"><?php echo $updtLBtexts[5][$lng] ?></h3>
                <p id="qrlink" onclick="cplink()" ontouchstart="cplink()" title="<?php $updtLBtexts[6][$lng] ?>"></p>
                </div>
                <div id="qrcode"></div>
            </div>
            <h3><?php echo $updtLBtexts[1][$lng] ?></h3>
            <ol>
                <?php
                    if (strlen($_SESSION['game']['playerList'] > 0)) {
                        $playersArray = explode('┇', $gameData['game']['playerList']);
                        $nbejected = 0;
                        foreach ($playersArray as $playerobj) {
                            $playerArr = explode('┊', $playerobj);
                            if ($playerArr[0] == $_SESSION['nickname'])
                                echo '<li>' . $playerArr[0] . $updtLBtexts[3][$lng] . '</li>';
                            else 
                                echo '<li>' . $playerArr[0] . '</li>';
                        }
                    }
                ?>
            </ol>
        </div>
        <?php if (isset($_SESSION['gameOwner']) && $_SESSION['gameOwner'] == $_SESSION['ID']) { ?>
        <hr id="lbhr">
        <div id="lbCommand">
            <button><?php echo $updtLBtexts[2][$lng] ?></button>
        </div>
        <?php } ?>
    </div>
</div>

<script>
    var webpage = `http://${window.location.hostname}/Cosmocats/<?php echo $_SESSION['gameID'] ?>`;
    var qrc = new QRCode(document.getElementById("qrcode"), webpage);
    document.getElementById("qrlink").innerText = webpage;
    
    function cplink() {
        copytcb(webpage)
    }
</script>