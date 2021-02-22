<?php

use Core\DB_connection;

$title = "MyDictionary";
isset($base_page) ? include $base_page : null;
isset($chosen_language) ? : $chosen_language = null;
isset($languages) ? : $chosen_language = null;
isset($user) ? : $user = new User();
isset($all_lang) ? : $all_lang = null;
isset($featured_groups) ? : $featured_groups = array();
//print DB_connection::getCalls();
?>
<div id="word-info"></div>
<main class="mx-auto px-lg-4 px-md-2 pr-sm-0 pr-sm-1">

    <div class="row m-0 mt-1">
        <div class="words-column col-md-3 border rounded mb-3">
            <div class="py-2 mb-2">
                <?php
                if($chosen_language) {
                    ?>
                    <input class="form-control" aria-label="Search" type="search" id="search" placeholder="Search for word or phrase..."
                           onkeyup="delaySearch()">
                    <?php
                }
                ?>
            </div>


            <div class="pos-f-t">
                <div class="collapse" id="navbarToggleExternalContent">

                    <div id="dictionary" class="p-1 rounded">
                        <h6 class="mb-1">Your Dictionaries</h6>
                        <form id="dictionaries" class="d-block mb-2" method="post" action="words/changeLanguage">
                        <?php
                        foreach ($languages as $language) { ?>
                            <div class="custom-control custom-switch border-bottom">
                                <input name="chosen_language" type="radio" class="custom-control-input" id="customSwitch_<?php print $language->id; ?>"
                                value="<?php print $language->id; ?>"
                                <?php print ($chosen_language && $language->id == $chosen_language->id ? "checked" : null) ?>
                                onchange="this.form.submit()">
                                <label class="custom-control-label" for="customSwitch_<?php print $language->id; ?>"><?php print $language->name; ?></label>
                                <span class="small text-warning"><?php print "(" . $user->wordsPerLanguage($language->id) . " words)"; ?></span>
                                <a href="/languages/<?php print $language->id; ?>/destroy/?token=<?php print $token; ?>"
                                   class="btn btn-sm btn-outline-danger float-right py-0 border-0">x</a>
                            </div>
                        <?php }
                        ?>
                        </form>

                        <form id="new_dictionary" action="languages/store" method="post">
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
                                    <button class="btn btn-primary btn-sm ml-0" type="submit"><i class="fas fa-plus fa-sm"></i> Create</button>
                                </div>
                            </div>
                        </form>
                    </div>


                <div class="border-top px-1">
                    <div class="d-flex mt-2">
                        <h6 class="flex-grow-1">Your Groups</h6>
                        <a href="/groups/create" class="btn btn-sm btn-primary px-3" title="Create Group"><i class="fas fa-plus fa-sm"></i> </a>
                    </div>

                    <?php
                    $x = 1;
                    $myGroups = $user->myGroups();
                    if(count($myGroups) > 0) {
                        foreach ($myGroups as $group) {
                            print "<div class='text-warning border-bottom'><em>$x. <a href='/groups/$group->id/show'>$group->name</a></em>, <small>- {$group->countMembers()} member(s)</small></div>";
                            $x++;
                        }
                    }
                print "</div>";
                print "<div class=\"px-1 mt-3\">";
                    $belongInGroups = $user->groupsMember();
                    if(count($belongInGroups) > 0) {
                        $x = 1;
                        print "<h6 class=\"flex-grow-1\">Groups you belong</h6>";
                        foreach ($user->groupsMember() as $group) {
                            print "<div class='text-warning border-bottom'><em>$x. <a href='/groups/$group->id/show'>$group->name</a></em>, <small>- {$group->countMembers()} member(s)</small></div>";
                            $x++;
                        }
                    }
                    ?>
                </div>
                </div>
                <nav class="navbar navbar-dark pl-0">
                    <button id="option-toggle" class="navbar-toggler" type="button"
                            data-toggle="collapse" data-target="#navbarToggleExternalContent"
                            aria-controls="navbarToggleExternalContent" aria-expanded="false"
                            aria-label="Toggle navigation" title="Options to create new or choose existing dictionary">
                        <span><i class="fas fa-cog"></i> </span>
                    </button>
                </nav>
            </div>


        </div>

<!--        TABLE -->

        <div class="words-column col-md-7 pull-md-7 px-0 pl-md-2">
            <?php
            if($chosen_language){
                //print DB_connection::getCalls();

                ?>
            <table id="myTable" class="table table-sm text-light">
                <thead>
                <tr id="headRow">
                    <th><?php print $chosen_language->name; ?><span onmousedown="runLoader()" onmouseup="sortTable(0)"> &#9662; </span>
                    </th>
                    <th colspan="2">English<span onmousedown="runLoader()" onmouseup="sortTable(1)"> &#9662; </span></th>

                </tr>
                <tr>
                    <td colspan="3" id="word-save-form">
                        <form class="form-row w-100 m-0" action="words/store" method="post">
                            <input type="hidden" value="<?php print $token; ?>" name="token">
                            <!--                    <input aria-label="Language" type="hidden" placeholder="Language" name="language" id="language">-->
                            <div class="col m-0 p-0 pr-1">
                                <input class="form-control form-control-sm w-100" aria-label="New words1" type="text" placeholder="ex. とり" name="lang1">
                            </div>
                            <div class="col m-0 p-0 pr-1">
                                <input class="form-control form-control-sm w-100" aria-label="New words2" type="text" placeholder="ex. Bird" name="lang2">
                            </div>
                            <div class="col-1 m-0 p-0">
                                <button class="btn btn-sm btn-outline-success w-100" style="white-space: nowrap" type="submit"><i class="fas fa-check d-xl-none d-inline"></i> <span class="d-none d-xl-inline">Insert</span></button>
                            </div>

                        </form>
                    </td>
                </tr>
                <tr>
                    <td id='count_rows' colspan='3' class="text-center text-info small"></td>
                </tr>
                </thead>
                <tbody id="tbody">


                </tbody>
            </table>
                <script defer src="/js/recentWords.js?x=5"></script>
            <?php
            }else {

                print "<div class=\"d-flex\" style=\"flex-direction: column; min-height:70vh;align-items: center; justify-content: center\">";
                print "    <h5>Please choose your dictionary language.</h5>";
                if($languages && count($languages) > 0){
                    print "    <p>Click on <i class='fas fa-cog'></i>, then select existing or create new dictionary</p>";
                }else {
                    print "    <p>Click on <i class='fas fa-cog'></i>, then \"New dictionary\", then \"+ Create\"</p>";
                }
                print "</div>";
            ?>
                <script defer>
                    let formBorder =  document.getElementById('dictionary');
                    let optionToggle = document.getElementById('option-toggle');

                    let int = setInterval(function(){
                        //formBorder.style.border = '2px solid red';
                        formBorder.style.backgroundColor = 'orange';
                        optionToggle.style.backgroundColor = 'orange';
                        setTimeout(function () {
                            //formBorder.style.border = '2px solid #001B3E';
                            formBorder.style.backgroundColor = '#001B3E';
                            optionToggle.style.backgroundColor = 'transparent';
                        }, 500);
                    }, 1000);
                    setTimeout(function () {
                        clearInterval(int);
                    }, 10000);

                </script>
            <?php
            }
            //print DB_connection::getCalls();

            ?>
        </div>

        <div class="col-md-2 text-center" style="font-size: smaller">
            <h5>Recent groups</h5>
            <?php
            foreach ($featured_groups as $group){
                print "<div class='border rounded m-2 p-1'>";
                print "<a href='/groups/$group->id/show'>";
                print "<h6 class='text-primary'>$group->name</h6>";
                print "</a>";
                print "<div class='small'>{$group->owner()->name}</div>";
                print "<div class='small text-muted'>$group->city, $group->country</div>";
                print "<div class='small text-truncate'>$group->about</div>";

                print "</div>";
            }
            ?>
        </div>

    </div>

</main>
<script>

</script>
<script>let auth_id = <?php echo $auth_user->id; ?>;</script>
<script async src="js/scrollPercent.js"></script>
<script defer src="js/sort_table.js?x=<?php print time(); ?>">
</script>
<script defer src="js/wordsapi.js"></script>
<script>
    var checkbox = document.querySelector("input[name='chosen_language']");

    function getLanguage() {
        const chosen_lang = document.getElementById('chosen_language').value;
        let lang_input = document.getElementById('language');
        lang_input.value = chosen_lang;
    }

    function getRow(id){
        $.post('words/show', {'id':id}, setWordInput);
    }
    let old_input = "";
    let form = document.getElementById('word-save-form');
    form ? old_input = form.innerHTML : null;
    function setWordInput(data) {
        data = JSON.parse(data);

        let input = "";
        input = "<form class=\"form-row w-100 m-0\" action=\"words/" + data['id'] + "/update\" method=\"post\">\n" +
            "   <input type=\"hidden\" value=\"<?php print $token; ?>\" name=\"token\">\n" +
            "   <div class=\"col m-0 p-0 pr-1\">\n" +
            "       <input class='form-control form-control-sm w-100' type=\"text\" name=\"lang1\" value='" + data['lang1'] + "'>\n" +
            "   </div>\n" +
            "   <div class=\"col m-0 p-0 pr-1\">\n" +
            "       <input class='form-control form-control-sm w-100' type=\"text\" name=\"lang2\" value='" + data['lang2'] + "'>\n" +
            "   </div>\n" +
            "   <div class=\"col-2 m-0 p-0\">\n" +
            "       <div class='row m-0'>" +
            "       <button class='col btn btn-link btn-sm text-success' type=\"submit\"><i class=\"fas fa-check\"></i> <span class='d-none d-xl-block'>Update</span></button>\n" +
            "       <button class='col btn btn-link text-secondary btn-sm' onclick='cancelUpdate()' type=\"button\"><i class=\"fas fa-times\"></i> <span class='d-none d-xl-block'>Cancel</span></button>\n" +
            "       </div>" +
            "   </div>\n" +
            "</form>\n";
        form.innerHTML = input;
    }

    function cancelUpdate() {
        form.innerHTML = old_input;
    }
</script>
</body>
</html>

