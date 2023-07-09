<?php 

function stopRunning ( array $gameData) : bool
{
    $db = dbConnect();

    # Games started 48h ago are deleted
    if (time() - $gameData['timestamp'] > 3600 * 24 * 2) {
            $sqlQuery = 'UPDATE game SET gameState = 3 WHERE ID = :ID';

            $updateGame = $db->prepare($sqlQuery);
            $updateGame->execute([
                'ID' => $gameData['ID'],
            ]);
            return true;
    }
    else
        return false;
}

function stopRunningAll( array $gamesData ) : array
{
    $rtnval = ['val' => true, 'active' => -1];
    $gamenb = -1;
    foreach ($gamesData as $gameData) {
        $gamenb++;
        if (!stopRunning($gameData))
        {
            $rtnval = ['val' => false, 'active' => $gamenb];
        }
    }
    return $rtnval;
    
}

function getLobby( string $gameID ) : array
{
    $db = dbConnect();
    $gamesStatement = $db->prepare('SELECT ID, gameID, timestamp, nbConnected, gameState, playerList, Player1, Player2, Player3, Player4, Player5, Player6, Player7 FROM game WHERE gameID = :gameID AND gamestate < 3');
    $gamesStatement->execute([ 'gameID' => $gameID ]);
    $games = $gamesStatement->fetchAll();

    $runninggame = stopRunningAll($games);
    if (!$runninggame['val'])
        return [
            'found' => true,
            'game' => $games[$runninggame['active']]
        ];
    else 
        return [
            'found' => false,
            'reason' => 'No game found with this ID'
        ];
}

function getJoinLobby(int $lng,  string $gameID) : array
{
    $jlbtxt = [
        [ "Aucune partie avec le code %s trouvée","No game found with the code %s" ],
        [ "La partie %s a déjà commencé ! Vous ne pouvez plus la rejoindre...", "The game %s has already started ! You can no longer join it..." ],
        [ "La partie %s est déjà pleine.", "The game %s is already full." ],
    ];

    $lobbydata = getLobby($gameID);
    if ($lobbydata['found']) {
        $actgame = $lobbydata['game'];
        if ($actgame['nbConnected'] >= 7)
            return [
                'found' => false,
                'type' => 'full',
                'reason' => sprintf($jlbtxt[2][$lng], $gameID)
            ];
        elseif ($actgame['gameState'] > 0)
            return [
                'found' => false,
                'type' => 'started',
                'reason' => sprintf($jlbtxt[1][$lng], $gameID)
            ];
        else
            return [
                'found' => true,
                'infos' => $actgame
            ];
    }
    else
        return [
            'found' => false,
            'type' => 'exist',
            'reason' => sprintf($jlbtxt[0][$lng], $gameID)
        ];
}

function joinLobby( int $lng, string $gameID, string $nickname ) : array
{   
    $jlntxt = [
        [ "Ce pseudo est déjà pris !","This nickname is already taken!" ],
    ];
    $db = dbConnect();
    $lobby = getJoinLobby( $lng, $gameID );

    if (!$lobby['found'])
        return $lobby;
    
    if (strlen($lobby['infos']['playerList'] > 0)) {
        $playersArray = explode('┇', $lobby['infos']['playerList']);

        $nbfound = 0;
        foreach ($playersArray as $playerobj) {
            if (str_starts_with($playerobj, $nickname . '┊')) {
                $nbfound++;
            }
        }
        if ($nbfound>0)
            return [
                'found' => false,
                'type' => 'pseudo',
                'reason' => $jlntxt[0][$lng],
            ];
    }
    else
        $playersArray = [];
    # nickname┊skin┊points┊nb good answers┊good answer streak┊question value 0 if not answered yet, 1 if true, 2 if false
    
    $playersArray[] = sprintf('%s┊0┊0', $nickname);
    $playersUpdated = implode('┇', $playersArray);

    $sqlQuery = 'UPDATE game SET playerList = :players, nbConnected = :nbco  WHERE gameID = :gameID';

    $updateGame = $db->prepare($sqlQuery);
    $updateGame->execute([
        'gameID' => $gameID,
        'players' => $playersUpdated,
        'nbco' => count($playersArray),
    ]);
    
    return $lobby;
}

function quitLobby( int $lng, int $ID, string $gameID, string $nickname, int $reason = 0 ) : array
{
    $qlbtxt = [
        [ "La partie dans laquelle se trouve %s n'a pas été trouvée","The game %s is in was not found" ],
        [ "%s n'était déjà plus dans la partie", "%s was already out of the game" ],
        [ [ "Vous (%s) avez correctement été déconnecté de la partie %s", "%s a bien été expulsé de la partie %s"], ["You (%s) have been successfully disconnected from the game %s", "%s has been successfully ejected from the game %s"] ],
        [ "La partie %s a bien été supprimée en vous déconnectant", "The game %s have successfully been deleted when you disconnected yourself" ],
    ];

    $db = dbConnect();
    $lobby = getLobby( $gameID );

    if (!$lobby['found'])
        return [
            'found' => false,
            'reason' => sprintf($qlbtxt[0][$lng], $nickname)
        ];
    
    $nbejected = 0;

    if (strlen($lobby['game']['playerList'] > 0)) {
        $playersArray = explode('┇', $lobby['game']['playerList']);
        
        foreach ($playersArray as $playerobj) {
            if (str_starts_with($playerobj, $nickname . '┊')) {
                $playersArray = array_diff($playersArray, array("$playerobj"));
                $nbejected++;
            }
        }
    }
    else {
        $sqlQuery = 'UPDATE game SET gameState = 3 WHERE gameID = :gameID AND ID = :ID';

        $updateGame = $db->prepare($sqlQuery);
        $updateGame->execute([
            'ID' => $ID,
            'gameID' => $gameID,
        ]);

        return [
            'found' => false,
            'infos' => sprintf($qlbtxt[3][$lng], $gameID)
        ];
    }
    
    $playersUpdated = implode('┇', $playersArray);

    if ($nbejected > 0) {

        $newcount = count($playersArray);
        $newstate = ($newcount == 0) ? 3 : $lobby['game']['gameState'];

        $sqlQuery = 'UPDATE game SET playerList = :players, nbConnected = :nbco, gameState = :gameState WHERE gameID = :gameID AND ID = :ID';

        $updateGame = $db->prepare($sqlQuery);
        $updateGame->execute([
            'ID' => $ID,
            'gameID' => $gameID,
            'players' => $playersUpdated,
            'nbco' => $newcount,
            'gameState' => $newstate,
        ]);

        if ($newstate == 3)
            return [
                'found' => true,
                'infos' => sprintf($qlbtxt[3][$lng], $gameID)
            ];
        else
            return [
                'found' => true,
                'infos' => sprintf($qlbtxt[2][$lng][$reason], $nickname, $gameID)
            ];
    }
    else
        return [
            'found' => false,
            'infos' => sprintf($qlbtxt[1][$lng], $nickname)
        ];
}

function createLobby(int $lng) : array
{
    $db = dbConnect();

    $lbtxt = [
        [ "Partie créée avec succès, code de la partie : ","Game successfully created, game code : " ],
        [ "Impossible de générer une nouvelle partie pour le moment", "Impossible to generate a new game for the moment" ],
        [ "Une erreur est survenue lors de la création de la partie", "An error has occured while creating the new game" ],
    ];


    $try = 0;
    $maxTry = 10000;
    $gameID = generateRandomGameID();
    $gameIDfound = false;
    
    while (!$gameIDfound && $try < $maxTry) {
        $try++;
        $gamesStatement = $db->prepare('SELECT ID, timestamp FROM game WHERE gameID = :gameID AND gameState < 3');
        $gamesStatement->execute([ 'gameID' => $gameID ]);
        $games = $gamesStatement->fetchAll();

        $nbgames = count($games);
        if ($nbgames == 0)
            $gameIDfound = true;
        elseif (stopRunningAll($games)['val'])
            $gameIDfound = true;
        else
            $gameID = generateRandomGameID();
    }
    if ($gameIDfound) {
        
        $sqlQuery = 'INSERT INTO game(gameID, timestamp) VALUES (:gameID, :timestamp)';

        $insertGame = $db->prepare($sqlQuery);
        $insertGame->execute([
            'gameID' => $gameID,
            'timestamp' => time()
        ]);

        $gamesStatement = $db->prepare('SELECT ID FROM game WHERE gameID = :gameID AND gameState < 3');
        $gamesStatement->execute([ 'gameID' => $gameID ]);
        $games = $gamesStatement->fetchAll();

        $nbgames = count($games);

        if ($nbgames == 1)
            return [
                    'success' => true,
                    'reason' => $lbtxt[0][$lng] . $gameID,
                    'ID' => $games[0]['ID'],
                    'gameID' => $gameID
            ];
        else
            return [
                    'success' => false,
                    'reason' => $lbtxt[2][$lng],
            ];
    }
    else
        return [
                'success' => false,
                'reason' => $lbtxt[1][$lng]
        ];
}

function gameStatus(int $id, string $gameid) : array
{
    $db = dbConnect();

    $gameStatement = $db->prepare('SELECT gameState, nbConnected, playerList, Player1, Player2, Player3, Player4, Player5, Player6, Player7 FROM game WHERE ID = :ID AND gameID = :gameid');
    $gameStatement->execute([ 'ID' => $id, 'gameid' => $gameid ]);
    $games = $gameStatement->fetchAll();

    $nbgame = count($games);
    if ($nbgame != 1) return [ 'found' => false ];
    
    return [ 
        'found' => true,
        'game' => $games[0]
    ];
}

?>