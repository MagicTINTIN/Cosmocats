
// Connect to the websocket
inGame = 0;
let socket;

const connect = function () {
    // Return a promise, which will wait for the socket to open
    return new Promise((resolve, reject) => {

        const socketProtocol = (window.location.protocol === 'https:' ? 'wss:' : 'ws:')
        const port = 3000;
        const socketUrl = `${socketProtocol}//${window.location.hostname}:${port}/ws/`

        socket = new WebSocket(socketUrl);

        socket.onopen = (e) => {
            // Connection message
            socket.send(JSON.stringify({
                "from": "cosmocats",
                "type": "load",
                "loaded": true
            }));
            // connection established
            resolve();
        }

        socket.onmessage = (data) => {
            console.log('websocket sent', data);

            let parsedData = JSON.parse(data.data);
            if (parsedData.append === true) {
                if (parsedData.dataText.startsWith('WSConnectionOK'))
                    ctgExec();
                else if (inGame == 1 && parsedData.dataText.startsWith('refreshLobby'))
                    $('#lobby').load('includes/updt/lobby.php');
                // if (isInGame && parsedData.dataText.startsWith('Game ping'))
                //     $('#gameDiv').load('includes/updateParts/ping.php');
            }
        }

        socket.onerror = (e) => {
            // Return an error if any occurs
            console.log(e);
            resolve();
            // Try to connect again
            connect();
        }
    });
}

// check if a websocket is open
const isOpen = function (ws) {
    return ws.readyState === ws.OPEN
}

function sendGame(gameid, type = 'ping') {
    console.log('sending val ping', gameid);
    if (!gameid || gameid == 0) return console.log("No gameid")
    if (isOpen(socket)) {
        socket.send(JSON.stringify({
            "from": "cosmocats",
            "type": type,
            "senttime": Date.now(),
            "gameid": gameid
        }));
        console.log(`${type} sent`);
    }
}

