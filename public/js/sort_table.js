
const table = document.getElementById("myTable");
const loader = document.getElementById('loader');
function runLoader(){
    loader.style.display = "block";
    table.style.filter = "blur(2px)";
}

let globalTimeout = null;
function delaySearch() {
    runLoader();
    if(globalTimeout != null) clearTimeout(globalTimeout);
    globalTimeout = setTimeout(searchWord, 500);
}

function errorMessage(xhr, status, error) {
    let element = document.createElement('div');
    element.style.cssText = "position:absolute;width:100%;height:100%;opacity:0.5;z-index:100;background:#000;display:flex;flex-direction:column;justify-content:center;font-size:300%";
    let errorMessage = xhr.status + ': ' + xhr.statusText;
    element.innerHTML ='<div class="text-center text-danger">Error - ' + errorMessage + '</div><a href="" class="btn btn-info mx-auto">Back</a>';
    document.body.prepend(element); //prependChild(element);
    loader.style.display = "none";
    table.style.filter = "none";
}


function searchWord() {
    globalTimeout = null;
    let word;
    word = document.getElementById('search').value.trim();
    $.get('/words/search', {'string': word}, fillTableRows).fail(errorMessage);
}

function fillTableRows(data) {
    let body="";
    let word = document.getElementById('search').value.trim();
    let patt = new RegExp(word, "i");
    let tbody = document.getElementById('tbody');
    tbody.innerHTML = "";
    let nlang1, nlang2;

    if(data.length > 0){
        data = JSON.parse(data);
        document.getElementById('count_rows').innerHTML = data.length + " record(s) found";
        for(let i=0; i < data.length; i++) {

            let ax = data[i].lang1.split(" ");
            let ay = data[i].lang2.split(" ");

            let newXCell = [];
            let newYCell = [];

            for(let z=0;z<ax.length;z++){
                if(patt.test(ax[z])){
                    ax[z] = `<span style='color:greenyellow'>` + ax[z] + `</span>`;
                }
                newXCell.push(ax[z]);
            }

            for(let z=0;z<ay.length;z++){
                if(patt.test(ay[z])){
                    ay[z] = `<span style='color:greenyellow'>` + ay[z] + `</span>`;
                }
                newYCell.push(ay[z]);
            }

            nlang1 = newXCell.join(" ");
            nlang2 = newYCell.join(" ");

            body += "<tr class='bor_bottom' style='table-row'>" +
                "<td>" + nlang1 + "</td>" +
                "<td>" + nlang2 +  "<span class='info-mark' onclick='getTranslation(\"" + data[i].lang2 + "\")'>?</span>" +
                "</td><td style='white-space: nowrap'>";

            if(auth_id == data[i].user_id) {
                body += "<i class='fas fa-edit fa-sm mr-3' onclick='getRow(" + data[i].id + ")'></i>" +
                    "<a onclick='deleteWord(" + data[i].id + ")' " +
                    "href='#'><i class='fas fa-times fa-sm text-danger'></i>" +
                    "</a>";
            }else{
                body += "<span class='p-1 rounded' style='background:rgba(255, 255, 0, 0.6);font-size:50%;white-space: nowrap'>From group</span>";
            }

            body += "</td>" +
                "</tr>";
        }
        tbody.innerHTML = body;
    }else{
        tbody.innerHTML = "";
        document.getElementById('count_rows').innerHTML = "";
    }
    loader.style.display = "none";
    table.style.filter = "none";

}

function sortTable(col){
    //const start = Date.now();
    const asc = 1;
    let tbody = document.getElementById("tbody");
    const rows = tbody.rows, rlen = rows.length, arr = [];
    let i, j, cells, clen;
// fill the array with values from the table
    for(i = 0; i < rlen; i++){
        cells = rows[i].cells;
        clen = cells.length;
        arr[i] = [];
        for(j = 0; j < clen; j++){
            arr[i][j] = cells[j].innerHTML;
        }
    }
// sort the array by the specified column number (col) and order (asc)
    arr.sort(function(a, b){
        return (a[col] === b[col]) ? 0 : ((a[col] > b[col]) ? asc : -1*asc);
    });
    for(i = 0; i < rlen; i++){
        arr[i] = "<td>"+arr[i].join("</td><td>")+"</td>";
    }
    tbody.innerHTML = "<tr>"+arr.join("</tr><tr>")+"</tr>";

    //console.log(`Sorting takes: ${Date.now()-start}ms`);
    loader.style.display = "none";
    table.style.filter = "none";

}