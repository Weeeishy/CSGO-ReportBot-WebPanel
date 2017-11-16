var fs = require("fs"),
    Steam = require("steam"),
    SteamID = require("steamid"),
    IntervalInt = null,
    readlineSync = require("readline-sync"),
    Protos = require("./protos/protos.js"),
    CountCommends = 0,
    Long = require("long"),
    process = require("process"),
    steamID = process.argv[2],
    accounts_list_id = process.argv[3];
	prefix = process.argv[4];



var ClientHello = 4006,
    ClientWelcome = 4004;
 
var accounts = [];
 
var arrayAccountsTxt = fs.readFileSync("accounts_commend/accounts_" + accounts_list_id +".txt").toString().split("\n");
for (i in arrayAccountsTxt) {
    var accInfo = arrayAccountsTxt[i].toString().trim().split(":");
    var username = accInfo[0];
    var password = accInfo[1];
    accounts[i] = [];
    accounts[i].push({
        username: username,
        password: password
    });
}
 
function loginAndCommend(steamID) {
    if ((steamID == "") || !(steamID.indexOf("765") > -1) || (steamID.length < 17)) {
        console.log("That's not a valid SteamID!");
        process.exit();
    }
    if (accounts[0]) {
        var account = accounts[0][0];
        var account_name = account.username;
        var password = account.password;
        Client = new Steam.SteamClient();
        User = new Steam.SteamUser(Client);
        GC = new Steam.SteamGameCoordinator(Client, 730);
        Friends = new Steam.SteamFriends(Client);
 
        Client.connect();
 
        Client.on("connected", function() {
            User.logOn({
                account_name: account_name,
                password: password
            });
        });
 
        Client.on("logOnResponse", function(res) {
            if (res.eresult !== Steam.EResult.OK) {
                if (res.eresult == Steam.EResult.ServiceUnavailable) {
                    console.log("\n[STEAM CLIENT - ***_" +  account_name.substring(9, 20) + " Login failed - STEAM IS DOWN!");
                    console.log(res);
                    Client.disconnect();
                    process.exit();
                } else {
                    console.log("\n[STEAM CLIENT - ***_" +  account_name.substring(9, 20) + "] Login failed!");
                    console.log(res);
                    Client.disconnect();
                    accounts.splice(0, 1);
                    loginAndCommend(steamID);
                }
            } else {
                //console.log("\n[STEAM CLIENT - ***_" +  account_name.substring(9, 20) + " Logged in!");
 
                Friends.setPersonaState(Steam.EPersonaState.Offline);
 
                User.gamesPlayed({
                    games_played: [{
                        game_id: 730
                    }]
                });
 
                if (GC) {
                    IntervalInt = setInterval(function() {
                        GC.send({
                            msg: ClientHello,
                            proto: {}
                        }, new Protos.CMsgClientHello({}).toBuffer());
                    }, 2000);
                } else {
                    console.log("[" + prefix + " - ***_" +  account_name.substring(9, 20) + " Not initialized!");
                    Client.disconnect();
                    accounts.splice(0, 1);
                    loginAndCommend(steamID);
                }
            }
        });
 
        Client.on("error", function(err) {
            console.log("[STEAM CLIENT - ***_" +  account_name.substring(9, 20) + " " + err);
            console.log("[STEAM CLIENT - ***_" +  account_name.substring(9, 20) + " Account is probably ingame!");
            Client.disconnect();
            accounts.splice(0, 1);
            loginAndCommend(steamID);
        });
 
        GC.on("message", function(header, buffer, callback) {
            switch (header.msg) {
                case ClientWelcome:
                    clearInterval(IntervalInt);
                    sendCommend(GC, Client, account_name, steamID);
                    break;
                case Protos.ECsgoGCMsg.k_EMsgGCCStrike15_v2_MatchmakingGC2ClientHello:
                    break;
                default:
                    console.log(header);
                    break;
            }
        });
    } else {
        console.log("\n\n" + CountCommends + " commend(s) successfully sent!");
        Client.disconnect();
    }
}
 
function sendCommend(GC, Client, account_name) {
    var account_id = new SteamID(steamID).accountid;
	
	var commend_payload = new Protos.PlayerCommendationInfo({
		cmdFriendly: 1,
		cmdTeaching: 2,
		cmdLeader: 4
	});
	
	var commendProto = new Protos.CMsgGCCStrike15_v2_ClientCommendPlayer({
        accountId: account_id,
        matchId: 8,
		tokens: 10,
		commendation: commend_payload
    }).toBuffer();
	
    GC.send({
        msg: Protos.ECsgoGCMsg.k_EMsgGCCStrike15_v2_ClientCommendPlayer,
        proto: {}
    }, commendProto);
	console.log("[" + prefix + " - " + CountCommends + "] Commendation Sent!");
	Client.disconnect();
    accounts.splice(0, 1);
    CountCommends++;
    loginAndCommend(steamID);
}
 
process.on('uncaughtException', function (err) {
});

console.log("Commending SteamID: " + steamID + "\nStarting Accounts...\nUsing Commendbot #" + accounts_list_id + "\nThis may take some time, so please be patient.\n");
loginAndCommend(steamID);
