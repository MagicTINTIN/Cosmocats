<?php session_start();
$language = (!empty($_COOKIE['language'])) ? $_COOKIE['language'] : 'fr';
$language = (isset($_POST['l'])) ? htmlspecialchars($_POST['l']) : $language;
if (isset($_POST['l'])) setcookie('language', $language, [ 'expires' => time() + 365*24*3600, 'secure' => true, 'httponly' => true,]);
if ($language == 'en')
    $lng = 1;
else
    $lng = 0;
$texts = [
    [ "Votre navigateur a l'air de bloquer le JavaScript<br>Essayez en désactivant votre bloqueur de scripts","Your web browser seems to block JavaScript<br>Try disabling your script blocker"],
    [ "EN", "FR" ],
    [ "en", "fr" ],
    [ "Connexion", "Connection" ],
    [ "Entrez un pseudonyme", "Enter a nickname" ],
    [ "Pseudo", "Nickname" ],
    [ "Entrer", "Enter" ],
    [ "Aucune partie trouvée", "No game found"]
];

include_once('includes/fcts/tools.php');
include_once('includes/fcts/db.php');
include_once('includes/fcts/lobby.php');

if (!isset($_REQUEST['gameid']) && !isset($_POST['create'])) {
    $_SESSION['infoMsg'] = $texts[7][$lng];
    $_SESSION['errorMsg'] = $texts[7][$lng];
    header("Location: ./");
    exit();
}

if (isset($_GET['gameid'])) {
    $joinData = getJoinLobby($lng, htmlspecialchars($_GET['gameid']));
    if ($joinData['found'])
        $infoMessageJG = 'game ' . $joinData['infos']['gameID'] . ' : ' . $joinData['infos']['gameName'] . ' (' . $joinData['infos']['quizzID'] 
                        . ')<br>Hosted by ' . $joinData['infos']['gameOwner'] . '<br>'
                        . ($joinData['infos']['nbConnected'] + 1) . '/' . $joinData['infos']['roomSize'] . ' players';
    else {
        $errorMessageJG = $joinData['reason'];
        $errorType = $joinData['type'];
    }
}

elseif (isset($_POST['gameIDconnect']) && isset($_POST['nickname'])) {
    $joinData = joinLobby($lng, htmlspecialchars($_POST['gameIDconnect']), htmlspecialchars($_POST['nickname']));
    if ($joinData['found']) {
        $infoMessageJG = 'You are connected in the lobby';
        $_SESSION['game'] = $joinData['infos'];
        $_SESSION['nickgame'] = htmlspecialchars($_POST['nickname']);
    }
        // $infoMessageJG = 'connected as '. htmlspecialchars($_POST['nickname'])
        //                 .'<br>in the game game ' . $joinData['infos']['gameID'] . ' : ' . $joinData['infos']['gameName'] . ' (' . $joinData['infos']['quizzID'] 
        //                 . ')<br>Hosted by ' . $joinData['infos']['gameOwner'] . '<br>'
        //                 . ($joinData['infos']['nbConnected'] + 1) . '/' . $joinData['infos']['roomSize'] . ' players';
    else {
        $errorMessageJG = $joinData['reason'];
        $errorType = $joinData['type'];
    }
}

if (isset($_SESSION['errorMsg'])) {
    $errorMessage = $_SESSION['errorMsg'];
    unset($_SESSION['errorMsg']);
}
if (isset($_SESSION['infoMsg'])) {
    $infoMessage = $_SESSION['infoMsg'];
    unset($_SESSION['infoMsg']);
}
?>
<!DOCTYPE html>
<html lang="fr" id="background" class="worldmap">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="viewport"
        content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <title><?php echo $texts[3][$lng] ?> | Cosmocats</title>

    <link href="styles/vars.css" rel="stylesheet">
    <link href="styles/styles.css" rel="stylesheet">
    <link href="styles/connection.css" rel="stylesheet">
    <meta name="author" content="ALC ProduXion/Softplus">
    <meta name="description" content="Connection page to a game with cats which want to go to space">

    <link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>

<body>
    <div id="centeringbg">
        <section id="connecttogame" class="centeringnav">

                <?php if(isset($errorMessage)) : ?>
                    <div id="errorMsg" role="alert" class="msg">
                        <span></span>
                        <span><?php echo $errorMessage; ?></span>
                        <span onclick="deleteMsg('error')" ontouchstart="deleteMsg('error')" class="closeMsg">X</span>
                    </div>
                <?php endif;
                if(isset($infoMessage)) : ?>
                    <div id="infoMsg" class="msg">
                        <span></span>
                        <span><?php echo $infoMessage; ?></span>
                        <span onclick="deleteMsg('info')" ontouchstart="deleteMsg('info')" class="closeMsg">X</span>
                    </div>
                <?php endif; ?>

                <h1><?php echo $texts[4][$lng] ?></h1>
                <form action="lobby" method="post">
                    <input type="text" id="pseudoinput" name="pseudo" required
                        placeholder="<?php echo $texts[5][$lng] ?>"
                        pattern="[a-zA-Z0-9_-]+"
                        minlength="1" maxlength="24" size="24" title="<?php echo $texts[4][$lng] ?>">
                        <br>
                    <input type="submit" id="enterpseudo" name="language" value="<?php echo $texts[6][$lng] ?>" />
                </form>
        </section>
        <div id="nojs">
            <div class="bfcodediv">
                <p>Hmmm....<br><br>
                <?php echo $texts[0][$lng] ?>
                </p>
            </div>
        </div>
        <form method="post" id="languageselection">
            <input type="hidden" name="l" value="<?php echo $texts[2][$lng] ?>" />
            <input type="submit" name="language" value="<?php echo $texts[1][$lng] ?>" />
        </form>
    </div>

    <script src="scripts/cookies.js"></script>
    <script src="scripts/mainfunctions.js"></script>
</body>

</html>