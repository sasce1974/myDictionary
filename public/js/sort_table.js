/*
function sortTable(a) {
    const start = Date.now();
    var rows, switching, i, x, y, shouldSwitch;
    switching = true;
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 2; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            //x = rows[i].getElementsByTagName("TD")[a];
            //y = rows[i + 1].getElementsByTagName("TD")[a];
            x = rows[i].cells[a];
            y = rows[i + 1].cells[a];
            if (x.innerHTML > y.innerHTML) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
    console.log(`Sorting takes: ${Date.now()-start}ms`);
    loader.style.display = "none";
    table.style.filter = "none";
}
*/

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
    $.get('/words/search', {'string': word}, fillTableRows).fail(errorMessage);;
}

function fillTableRows(data) {
    let body="";
    let word = document.getElementById('search').value.trim();
    let patt = new RegExp(word, "i");
    let tbody = document.getElementById('tbody');
    tbody.innerHTML = "";

    if(data.length > 0 && word !== ""){
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

            data[i].lang1 = newXCell.join(" ");
            data[i].lang2 = newYCell.join(" ");

            body += "<tr class='bor_bottom' style='table-row' onclick='getRow(" + data[i].id + ")'>" +
                "<td>" + data[i].lang1 + "</td><td>" + data[i].lang2 +
                "</td><td class='text-danger h4'><a href='words/" + data[i].id + "/delete'>x</a></td>" +
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


/*function search(){
    globalTimeout = null;
    let word, rows, x,y, i, z;
    word = document.getElementById('search').value.trim();
    let patt = new RegExp(word, "i");
    rows = table.rows;
    for(i=2; i < rows.length; i++){
        x = rows[i].getElementsByTagName("TD")[0];
        const initial_x = x.innerText;
        y = rows[i].getElementsByTagName("TD")[1];
        const initial_y = y.innerText;
        if(word.length > 0) {
            if (patt.test(x.innerHTML) || patt.test(y.innerHTML)) {
                rows[i].style.display = 'table-row';

                let ax = x.innerText.split(" ");
                let ay = y.innerText.split(" ");

                let newXCell = [];
                let newYCell = [];
                for(z=0;z<ax.length;z++){
                    if(patt.test(ax[z])){
                        ax[z] = `<span style='color:greenyellow'>` + ax[z] + `</span>`;
                    }
                    newXCell.push(ax[z]);
                }

                for(z=0;z<ay.length;z++){
                    if(patt.test(ay[z])){
                        ay[z] = `<span style='color:greenyellow'>` + ay[z] + `</span>`;
                    }
                    newYCell.push(ay[z]);
                }

                x.innerHTML = newXCell.join(" ");
                y.innerHTML = newYCell.join(" ");
            } else {
                rows[i].style.display = 'none';
            }
        }else{
            x.innerText = initial_x;
            y.innerText = initial_y;
            rows[i].style.display = 'table-row';
        }
    }
}*/




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