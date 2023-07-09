import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

import express from 'express'
import expressWs from 'express-ws'
import http from 'http'

let port = 3000;

// App and server
let app = express();
let server = http.createServer(app).listen(port);

// Apply expressWs
expressWs(app, server);

app.use(express.static(__dirname + '/views'));

// Get the route / 
app.get('/', (req, res) => {
    res.status(200).send("Welcome to Cosmocats app");
});

var connections = [];
var gamesConnections = {}

function addConnection(gameid, ws) {
    console.log('Add connection', gameid);
    if (!gamesConnections[gameid]) gamesConnections[gameid] = [];
    gamesConnections[gameid].push(ws);
}

function broadcast(message) {
    connections.forEach((ws) => {
        ws.send(message)
    })
}

function gameBroadcast(gameID, message) {
    if (!gamesConnections[gameID]) return 1;
    gamesConnections[gameID].forEach((ws) => {
        ws.send(message)
    })
}

function wsMessageProcess(ws, sender, object) {
    // console.log("wmp");
    if (!object["type"]) return ws.send(JSON.stringify({
        "append": true,
        "dataText": "Missing type object"
    }));

    if (sender == 'katest')
        katestProcess(ws, object["type"], object);
    if (sender == 'cosmocats')
        cosmocatsProcess(ws, object["type"], object);

}

function katestProcess(ws, type, info) {
    // console.log("kp");
    if (type == 'load') {
        // console.log('load send');
        return ws.send(JSON.stringify({
            "append": true,
            "dataText": "Connexion OK!"
        }));
    }
    else if (type == 'test') {
        return ws.send(JSON.stringify({
            "append": true,
            "dataText": `Test OK (${(Date.now() - info["senttime"]) / 1000}ms)`
        }));
    }
    else if (type == 'testAll') {
        //console.log("TESTING ALL");
        return broadcast(JSON.stringify({
            "append": true,
            "dataText": `Test All OK (${(Date.now() - info["senttime"]) / 1000}ms)`
        }));
    }
}

function cosmocatsProcess(ws, type, info) {
    // console.log("kp");
    if (type == 'load') {
        // console.log('load send');
        return ws.send(JSON.stringify({
            "append": true,
            "dataText": "WSConnectionOK"
        }));
    }
    else if (type == 'connect') {
        console.log('COSMOCATS - Connection request', info['gameid']);
        if (!info['gameid']) return 1;
        addConnection(info['gameid'], ws)
        ws.send(JSON.stringify({
            "append": true,
            "dataText": `Connexion to ${info['gameid']} OK!`
        }));
        return gameBroadcast(info['gameid'], JSON.stringify({
            "append": true,
            "dataText": `refreshLobby|join`
        }));
    }
    else if (type == 'ping') {
        if (!info['gameid']) return 1;
        return gameBroadcast(info['gameid'], JSON.stringify({
            "append": true,
            "dataText": `Game ping for ${info['gameid']}`
        }));
    }
    else if (type == 'playerJoin') {
        if (!info['gameid']) return 1;
        return gameBroadcast(info['gameid'], JSON.stringify({
            "append": true,
            "dataText": `refreshLobby|join`
        }));
    }
    else if (type == 'playerQuit') {
        if (!info['gameid']) return 1;
        return gameBroadcast(info['gameid'], JSON.stringify({
            "append": true,
            "dataText": `refreshLobby|quit`
        }));
    }
    else if (type == 'startGame') {
        if (!info['gameid']) return 1;
        return gameBroadcast(info['gameid'], JSON.stringify({
            "append": true,
            "dataText": `refreshGame|start`
        }));
    }
}

// Get the /ws websocket route
app.ws('/ws', async function (ws, req) {
    connections.push(ws);
    ws.on('message', async function (msg) {
        //console.log(typeof (msg), msg);
        // Send back some data
        const msgdata = JSON.parse(msg);
        console.log(msgdata)
        if (!msgdata['from'])
            return ws.send(JSON.stringify({
                "append": true,
                "dataText": "Unknown sender"
            }));

        else wsMessageProcess(ws, msgdata['from'], msgdata);
    });

});

console.log("WS is running...")