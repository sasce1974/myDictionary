<?php
$title = "Dictionary";
include $base_page;
?>

<main class="mx-auto px-lg-4 px-md-2 pr-sm-0 pr-sm-1 flex-column">

    <div class="row ml-0 mr-3 my-3 w-100">

<!--        <form class="col-md-4 p-0 pr-2" method="post" action="words/changeLanguage">-->
            <!-- <input type="text" class="form-control" name="chosen_language" id="chosen_language"
                           list="languages" placeholder="Insert your language" onchange="this.form.submit()"
                    value="<?php /*print $chosen_language; */?>">
                    <datalist id="languages">
                    <?php
            /*                   foreach ($languages as $language){
                                   print "<option>$language</option>";
                               }
                            */?>
                    </datalist>-->
            <!--<select class="form-control" name="chosen_language" id="chosen_language" onchange="this.form.submit()">
                <?php
/*                foreach ($languages as $language) {
                    print "<option value='$language'";
                    print $language === $chosen_language ? "selected" : '';
                    print ">$language</option>";
                }
                */?>
            </select>-->
<!--        </form>-->
        <div class="col-md-7 p-0 m-0"></div>
        <input class="form-control col-md-5" aria-label="Search" type="search" id="search" placeholder="Search..." onkeyup="delaySearch()">
    </div>
    <div class="row mx-0 px-0">
    <div class="col-md-3 push-md-3 border rounded pr-md-2">
        <h5>Your languages</h5>

        <form class="d-block" method="post" action="words/changeLanguage">
        <?php
        foreach ($languages as $language) { ?>
            <div class="custom-control custom-switch">
                <input name="chosen_language" type="radio" class="custom-control-input" id="customSwitch_<?php print $language->id; ?>"
                value="<?php print $language->id; ?>"
                <?php print ($language->id == $chosen_language->id ? "checked" : null) ?>
                onchange="this.form.submit()">
                <label class="custom-control-label" for="customSwitch_<?php print $language->id; ?>"><?php print $language->name; ?></label>
                <span class="small text-warning"><?php print "(" . $user->wordsPerLanguage($language->id) . " words)"; ?></span>
                <a href="/languages/<?php print $language->id; ?>/destroy/?token=<?php print $token; ?>"
                   class="btn btn-sm btn-outline-danger float-right py-0 border-0">x</a>
            </div>

            <!--<input class='input-control input-control-sm' type='radio' name='chosen_language' value='$language' "  . ($language == $chosen_language ? "checked" : '') . ">";
            <label>$language</label><br>";-->
        <?php }
        ?>
        </form>
        <hr>
        <form action="languages/store" method="post">
            <input type="hidden" value="<?php print $token; ?>" name="token">
            <div class="input-group mb-3">
                <select class="custom-select custom-select-sm" id="newLanguage" name="newLanguage">
                    <option>New dictionary</option>
                    <?php
                    foreach($all_lang as $lang){
                        print "<option value='{$lang->id}'>{$lang->name}</option>";
                    }
                    ?>
                </select>
                <!--                <input class="form-control form-control-sm mr-0" type="text" name="newLanguage" placeholder="New language">-->
                <div class="input-group-append">
                    <button class="btn btn-primary btn-sm ml-0" type="submit">Create</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-9 pull-md-9 px-0 pl-md-2">


        <table id="myTable" class="table table-sm text-light">
            <thead>
            <tr id="headRow">
                <th><?php print $chosen_language->name; ?><span onmousedown="runLoader()" onmouseup="sortTable(0)"> &#9662; </span></th>
                <th>English<span onmousedown="runLoader()" onmouseup="sortTable(1)"> &#9662; </span></th>
                <th><i class="fas fa-trash-alt"></i></th>
            </tr>
            <tr>
                <td colspan="3" id="word-save-form">
                    <form action="words/store" method="post">
                        <input type="hidden" value="<?php print $token; ?>" name="token">
    <!--                    <input aria-label="Language" type="hidden" placeholder="Language" name="language" id="language">-->
                        <input aria-label="New words1" type="text" placeholder="ex. とり" name="lang1">
                        <input aria-label="New words2" type="text" placeholder="ex. Bird" name="lang2">
                        <button type="submit"><i class="fas fa-paper-plane"></i> Insert</button>
                    </form>
                </td>
            </tr>
            <tr><td id='count_rows' colspan='3' class="text-center text-info small"></td></tr>
            </thead>
            <tbody id="tbody">

     <!--       --><?php

            /*if(count($words) === 0){
                print "<h3>NO DICTIONARY DATA FOUND!</h3>";
            }*/

            foreach ($words as $row){

                print "<tr class='bor_bottom' onclick='getRow(" . $row->id . ")'> \n";
                print "<td>" . ucfirst(trim($row->lang1)) . "</td>";
                print "<td>" . ucfirst(trim($row->lang2)) . "</td>";
                print "<td><a href='words/{$row->id}/delete'>&#215;</a></td>";
                print "</tr> \n";
            }
            //$con = $rows = $q = null;
            ?>
            </tbody>
        </table>
    </div>
    </div>
</main>
<script>

</script>
<script async src="js/scrollPercent.js"></script>
<script async src="js/sort_table.js?x=<?php print time(); ?>"></script>
<script>
    /*if(document.querySelector('input[name="chose_language"]')){
        document.querySelector('input[name="chose_language"]').addEventListener("click", function(){
            var item = document.querySelector('input[name="contract_duration"]').value;
            console.log(item);
        });
    }*/

    var checkbox = document.querySelector("input[name='chosen_language']");

    checkbox.addEventListener('change', function() {
        console.log("Checkbox value is: " + this.value);
        /*if (this.checked) {
            console.log("Checkbox value is: " + this.value);
        } else {
            console.log("Checkbox is not checked..");
        }*/
    });




    function getLanguage() {
        const chosen_lang = document.getElementById('chosen_language').value;
        let lang_input = document.getElementById('language');
        lang_input.value = chosen_lang;
    }

    function getRow(id){
        console.log(id);
        $.post('words/show', {'id':id}, setWordInput);
    }
    let old_input = "";
    function setWordInput(data) {
        data = JSON.parse(data);
        let form = document.getElementById('word-save-form');
        old_input = form.innerHTML;
        let input = "";
        input = "<form action=\"words/" + data['id'] + "/update\" method=\"post\">\n" +
            "   <input type=\"hidden\" value=\"<?php print $token; ?>\" name=\"token\">\n" +
            "   <input class='bg-warning' type=\"text\" name=\"language\" id=\"language\" value='" + data['language'] + "'>\n" +
            "   <input class='bg-info' type=\"text\" name=\"lang1\" value='" + data['lang1'] + "'>\n" +
            "   <input class='bg-info' type=\"text\" name=\"lang2\" value='" + data['lang2'] + "'>\n" +
            "   <button type=\"submit\"><i class=\"fas fa-paper-plane\"></i> Update</button>\n" +
            "   <button type=\"cancel\"><i class=\"fas fa-times\"></i> Cancel</button>\n" +
            "</form>";
        form.innerHTML = input;
    }
    /*document.body.onclick = function () {
        let form = document.getElementById('word-save-form');
        if(old_input !== ""){
            form.innerHTML = old_input;
        }
    }*/
</script>



</body>
</html>

