

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
            body += "<tr class='bor_bottom' style='table-row' onclick='getRow(" + data[i].id + ")'>" +
                "<td>" + data[i].lang1.replace(/^./, data[i].lang1[0].toUpperCase()) + "</td>" +
                "<td>" +  data[i].lang2.replace(/^./, data[i].lang2[0].toUpperCase()) +
                "<span class='info-mark' onclick='getTranslation(\"" + data[i].lang2 + "\")'>?</span>" +
                "</td><td class='text-danger h4'><a href='words/" + data[i].id + "/delete'>x</a></td>" +
                "</tr> \n";
        }
        tbody.innerHTML = body;
    }
    loader.style.display = "none";
    table.style.filter = "none";
}

window.onload = function () {
    runLoader();
    recentWords(15);
};