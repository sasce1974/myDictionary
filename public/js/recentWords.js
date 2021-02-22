

function recentWords(limit) {
    $.get('/words/mostRecentWords', {'limit':limit}, fillRows).fail(errorMessage);
}

function fillRows(data) {
    let tbody = document.getElementById('tbody');
    tbody.innerHTML = "";
    let body = "";
    if(data.length > 0){
        data = JSON.parse(data);
        for(let i=0; i < data.length; i++) {
            body += one_row(data, i);
        }
        tbody.innerHTML = body;
    }
    loader.style.display = "none";
    table.style.filter = "none";
}

function one_row(data, i)
{
    let output = "<tr class='bor_bottom' style='table-row'>" +
        "<td>" + data[i].lang1.replace(/^./, data[i].lang1[0].toUpperCase()) + "</td>" +
        "<td>" + data[i].lang2.replace(/^./, data[i].lang2[0].toUpperCase()) +
        "<span class='info-mark' onclick='getTranslation(\"" + data[i].lang2 + "\")'>?</span>" +
        "</td><td style='white-space: nowrap'>";
    if(auth_id == data[i].user_id) {
        output += "<i class='fas fa-edit fa-sm mr-3' onclick='getRow(" + data[i].id + ")'></i>" +
        "<a onclick='deleteWord(" + data[i].id + ")' " +
            "href='#'><i class='fas fa-times fa-sm text-danger'></i>" +
        "</a>";
    }else{
        output += "<span class='p-1 rounded' style='background:rgba(255, 255, 0, 0.6);font-size:50%;white-space: nowrap'>From group</span>";
    }
    output += "</td></tr> \n";

    return output;
}

function deleteWord(id){
    if(confirm("Delete record? This action can not be undone.")){
        window.location = "words/"+ id + "/delete";
    }
}

window.onload = function () {
    runLoader();
    recentWords(20);
};