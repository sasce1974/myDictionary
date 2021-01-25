<?php
$title = "Dictionary";
include $base_page;
?>

<main class="px-lg-4 px-md-2 px-sm-1">
    <div class="row mx-0 my-3">
<!--        <div><img alt="Logo" src="logo_w.svg" width="90px" height="auto"><h2 id="top">MY DICTIONARY</h2></div>-->
        <form class="col-md-4 p-0 pr-2" method="post" action="">
            <select class="form-control" name="chosen_language" id="chosen_language" onchange="this.form.submit()">
                <?php
                foreach ($languages as $language){
                    print "<option value='$language'";
                    print $language === $chosen_language ? "selected" : '';
                    print ">$language</option>";
                }
                ?>
            </select>
        </form>
        <div class="col-md-3"></div>
        <input class="form-control col-md-5" aria-label="Search" type="search" id="search" placeholder="Search..." onkeyup="delaySearch()">
    </div>

    <table id="myTable">
        <thead>
        <tr id="headRow">
            <th><?php print $chosen_language; ?><span onmousedown="runLoader()" onmouseup="sortTable(0)"> &#9662; </span></th>
            <th>English<span onmousedown="runLoader()" onmouseup="sortTable(1)"> &#9662; </span></th>
            <th><i class="fas fa-trash-alt"></i></th>
        </tr>
        <tr>
            <td colspan="3">
                <form action="" method="post" onmousedown="getLanguage()">
                    <input type="hidden" value="<?php print $token; ?>" name="token">
                    <input aria-label="Language" type="hidden" placeholder="Language" name="language" id="language">
                    <input aria-label="New words1" type="text" placeholder="ex. とり" name="input1">
                    <input aria-label="New words2" type="text" placeholder="ex. Bird" name="input2">
                    <button type="submit"><i class="fas fa-paper-plane"></i> Insert</button>
                </form>
            </td>
        </tr>
        <tr><td id='count_rows' colspan='3' class="text-center text-info small"></td></tr>
        </thead>
        <tbody id="tbody">

 <!--       --><?php
/*
        if(count($words) === 0){
            print "<h3>NO DICTIONARY DATA FOUND!</h3>";
        }

        foreach ($words as $row){

            print "<tr class='bor_bottom'> \n";
            print "<td>" . ucfirst(trim($row->lang1)) . "</td>";
            print "<td>" . ucfirst(trim($row->lang2)) . "</td>";
            print "<td><a href='?delete_line={$row->id}&token=$token'>&#215;</a></td>";
            print "</tr> \n";
        }
        //$con = $rows = $q = null;
        */?>
        </tbody>
    </table>

</main>
<script>

</script>
<script async src="js/scrollPercent.js"></script>
<script async src="js/sort_table.js?x=1"></script>
<script>
    function getLanguage() {
        const chosen_lang = document.getElementById('chosen_language').value;
        let lang_input = document.getElementById('language');
        lang_input.value = chosen_lang;
    }
</script>



</body>
</html>

