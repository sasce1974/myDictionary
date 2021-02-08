
function getTranslation(word) {
    $.get("/words/getWordInfo", {'word':word}, wordDetails).fail(errorMessage);
}

function wordDetails(data) {
    let div = $("#word-info");
    let output = "<span class='close-box' onclick='closeWordInfo()'>x</span>";
    if(data.length > 0) {
        data = JSON.parse(data);

        output += "<h4 class='text-info font-weight-bold mx-2'>" + data.word + "</h4>";
        if (data.results[0].partOfSpeech != undefined) output += "<b><em>[" + data.results[0].partOfSpeech + "]</em></b>";
        output += "<div class='w-i-inner'>";

        //output = iterate(data, output);

        if (data.results != undefined) {

            data.results.forEach(function (item, index) {
                if (item.antonyms != undefined) output += "<b>Antonyms:</b> " + item.antonyms[0] + "&#09;";
                if (item.definition != undefined) output += "<b>Definition:</b> " + item.definition + "&#09;";
                if (item.entails != undefined) {
                    output += "<b>Entails:</b> ";
                    item.entails.forEach(function (it, ind) {
                        output += it + ", ";
                    });
                    output += "\t";
                }
                if (item.synonyms != undefined) {
                    output += "<b>Synonyms:</b> ";
                    item.synonyms.forEach(function (it, ind) {
                        output += it + ", ";
                    });
                    output += ".\t";
                }
                if (item.hasTypes != undefined) {
                    output += "<b>Similar:</b> ";
                    item.hasTypes.forEach(function (it, ind) {
                        output += it + ", ";
                    });
                    output += ".\t";
                }
                output += "<hr class='my-0 py-0'>";
            });
        }

        output += "</div>";
        // div.fadeIn(300);

    }else{
        output += "<p>We apologise, there are no information for this word</p>";
    }
    div.slideDown(300);
    div.html(output);
}


/*function iterate(obj, output) {
    for (var property in obj) {
        if (obj.hasOwnProperty(property)) {
            output += "<b>" + property + ":</b>";
            if (typeof obj[property] == "object") {
                iterate(obj[property], output);
            }else {
                console.log(property + "   " + obj[property]);
                output += obj[property] + ", ";
            }
            output += "<br>";
        }
    }
    return output;
}*/

function closeWordInfo() {
    // $("#word-info").fadeOut(300);
    $("#word-info").slideUp(300);
}