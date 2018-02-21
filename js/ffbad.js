
/**
 * Update the table players list with the one from server
 * 
 * @return void
 */
function updatePlayersList() {
    console.log("updatePlayersList");
    $("#loader-container").show();
    $.getJSON("ws/getPlayers.php", function (data) {
        console.log("success getting players");

        // empty table
        var tbody = $("#players");
        tbody.empty();

        // iterate on services to create rows
        $.each(data, function (id, obj) {
            console.log("player : " + id);
            tbody.append(
                    '<tr id="' + encodeURI(id) + '">'
                    + '<td><a href="http://verybad.fr/joueur/detail/' + encodeURI(id) + '" target="_blank">' + htmlEntities(id) + '</a></td>'
                    + '<td>' + htmlEntities(obj.name) + '</td>'
                    + '<td>' + htmlEntities(obj.age) + '</td>'
                    + '<td>' + htmlEntities(obj.rankings.s.points)
                    + ' <span class="badge badge-dark" style="background:' + getRankingColor(obj.rankings.s.ranking) + ';">' + htmlEntities(obj.rankings.s.ranking)
                    + '</span></td>'
                    + '<td>' + htmlEntities(obj.rankings.d.points)
                    + ' <span class="badge badge-dark" style="background:' + getRankingColor(obj.rankings.d.ranking) + ';">' + htmlEntities(obj.rankings.d.ranking)
                    + '</span></td>'
                    + '<td>' + htmlEntities(obj.rankings.m.points)
                    + ' <span class="badge badge-dark" style="background:' + getRankingColor(obj.rankings.m.ranking) + ';">' + htmlEntities(obj.rankings.m.ranking)
                    + '</span></td>'
                    + '<td>'
                    + '<button type="button" class="btn btn-danger btn-sm" onclick="deletePlayer(\'' + id + '\')"><i class="fas fa-trash"></i></button>'
                    + '</td>'
                    + '</tr>');
        });

    })
            .done(function () {
                $("#loader-container").hide();
            })
            .fail(function () {
                console.log("Failed getting players list");
            });
}

/**
 * get a color according to the ranking
 * 
 * @param string ranking a ranking
 * @returns string hexa color
 */
function getRankingColor(ranking) {
    switch (ranking) {
        case 'NC':
            return '#BF328E';
            break;
        case 'P12':
            return '#C2F165';
            break;
        case 'P11':
            return '#74AC04';
            break;
        case 'P10':
            return '#5A8700';
            break;
        case 'D9':
            return '#508DB1';
            break;
        case 'D8':
            return '#2F749D';
            break;
        case 'D7':
            return '#094D76';
            break;
        case 'R6':
            return '#FFE03C';
            break;
        case 'R5':
            return '#FFD600';
            break;
        case 'R4':
            return '#BFA100';
            break;
        case 'N3':
            return '#FF7B3C';
            break;
        case 'N2':
            return '#FF5300';
            break;
        case 'N1':
            return '#BF3E00';
            break;

        default:
            return '#BF328E';
            break;
    }
}

/**
 * Add a player to the current session then refresh the list (if success)
 */
function addPlayer() {
    var search = $("#search").val();
    var week = $("#week").val();
    console.log("addPlayer : " + search + "/" + week);
    $.ajax("ws/addPlayer.php?search=" + encodeURI(search) + "&week=" + encodeURI(week))
            .done(function (data) {
                console.log("success adding players");
                updatePlayersList();
            })
            .fail(function (data) {
                console.log("Failed adding player");
            });
}

/**
 * Delete a player from the current session then refresh the list (if success)
 * @param string id
 */
function deletePlayer(id) {
    console.log("deletePlayer : " + id);
    $.ajax("ws/deletePlayer.php?id=" + encodeURI(id))
            .done(function (data) {
                console.log("success deleting player");
                updatePlayersList();
            })
            .fail(function (data) {
                console.log("Failed deleting player");
            });
}


/**
 * HTML escape a String.
 * 
 * @param {String} str string to html escape
 * @returns {String} escaped string
 */
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


/**
 * Get the new player order from sortable table, then ask for update on server.
 * 
 * @returns void
 */
function updatePlayersOrder() {
    var order = [];
    $("#players").children().each(function (id, child) {
        order.push(child.id);
    });
    console.log(order);

    $.post(
            "ws/updateOrder.php",
            {idList : order},
            function() {
                console.log("Order success");
            }
    ).fail(function () {
        console.log("Failed order");
    });
}



