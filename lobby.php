<?php session_start();
$language = (!empty($_COOKIE['language'])) ? $_COOKIE['language'] : 'fr';
$language = (isset($_POST['l'])) ? htmlspecialchars($_POST['l']) : $language;
if (isset($_POST['l'])) setcookie('language', $language, [ 'expires' => time() + 365*24*3600, 'secure' => true, 'httponly' => true,]);
if ($language == 'en')
    $lng = 1;
else
    $lng = 0;
$texts = [
    [ "fr", "en" ],
    [ "Choisissez votre pays", "Choose your country"],
];

if ( !(isset($_SESSION['ID']) && isset($_SESSION['gameID']) && isset($_SESSION['game']) && isset($_SESSION['nickname'])) ) {
    include('includes/clear.php');
    header("Location: ./");
    exit();
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
<html lang="<?php echo $texts[0][$lng] ?>" id="background" class="worldmap">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="viewport"
        content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <title>Lobby <?php echo $_SESSION['gameID'] ?> | Cosmocats</title>

    <link href="styles/vars.css" rel="stylesheet">
    <link href="styles/styles.css" rel="stylesheet">
    <link href="styles/lobby.css" rel="stylesheet">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="./scripts/localWS.js"></script>

    <meta name="author" content="ALC ProduXion/Softplus">
    <meta name="description" content="Game with cats which want to go to space">

    <link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>

<body>
    <div id="centeringbg">
        <section id="lobbycontainer" class="centeringnav">

            <?php include_once("includes/messages.php") ?>

                <h1 id="choosecountryTitle"><?php echo $texts[1][$lng] ?></h1>
                <div id="lobby"></div>
        </section>

        <?php include_once("includes/commonjslng.php") ?>
    </div>

    <script src="scripts/cookies.js"></script>
    <script src="scripts/mainfunctions.js"></script>

    <script type="text/javascript">
        // When the document has loaded
        function ctgExec() {
            sendGame('<?php echo $_SESSION['ID'] . '|'. $_SESSION['gameID'] ?>', 'connect');
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Connect to the websocket
            connect();
            inGame = 1;
        });
        
    </script>
</body>

</html>