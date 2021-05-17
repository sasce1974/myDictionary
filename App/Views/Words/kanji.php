<?php

$title = "MyDictionary | Kanji";
isset($base_page) ? include $base_page : null;
?>

<main class="mx-auto px-lg-1 pr-sm-0">
    <h3>MY KANJI LIST</h3>
    <table class="table table-dark text-light text-center">
        <thead>
        <tr>
            <th>Sign</th><th>Meaning</th><th>ON</th><th>KUN</th><th>N level</th>
        </tr>
        <form action="kanjies/store" method="post">
            <tr>
                <th><input type="text" name="sign" lang="ja" class="input-control"></th>
                <th><input type="text" name="meaning" class="input-control"></th>
                <th><input type="text" name="on_reading" class="input-control"></th>
                <th><input type="text" name="kun_reading" class="input-control"></th>
                <th><input type="number" name="n_level" class="input-control">
                    <input type="submit" value="Save" class="btn btn-sm btn-success">
                </th>
            </tr>
        </form>
        </thead>
    </table>
<!--    <table class="table text-light text-center border">-->
<!--        <tbody>-->

        <?php
        if(isset($kanji_list) && !empty($kanji_list)) {
//            $i = 0;
//            print "<tr>";
            foreach ($kanji_list as $key=>$k) {
                /*print "<tr>";
                print "<td class='bg-light text-dark'>{$kanji->sign}</td><td>{$kanji->meaning}</td>
                <td class='text-center'>{$kanji->on_reading}</td><td>{$kanji->kun_reading}</td>
                <td>{$kanji->n_level}</td>";
                print "</tr>";*/
                print "<h3>Level " . $key . "</h3>";
                print "<div class=\"border rounded p-1 d-flex flex-wrap\">";
                foreach ($k as $kanji) {
//                print "<td>";
                    print "<div class='border rounded p-1 m-2 text-center' style='width: 10em'>";
                    if($kanji->user_id == $auth_user->id){
                        print "<a href=\"/kanjies/$kanji->id/destroy/?token=$token\"
                                   class=\"btn btn-sm btn-outline-danger float-right py-0 border-0 m-0\">x</a>";
                    }
                    print "<div class='p-2'>";
                    print "<h2 class='text-warning'>{$kanji->sign}</h2>";
                    print "<span class='border-bottom'>" . $kanji->meaning . "</span><br>";
                    print $kanji->on_reading . " / " . $kanji->kun_reading . "<br>";
                    //print "JLPT Level:" . $kanji->n_level;
//                print "<td>";
//                $i++;
//                if($i == 8){
//                    print "</tr><tr>";
//                    $i = 0;
//                }
                    print "</div></div>";
                }
                print "</div>";
            }
//            print "</tr>";

        }else{
//            print "<tr><td colspan='5'><h4>There are no entries</h4></td></tr>";
            print "<h4>There is no data inserted</h4>";
        }
        ?>
<!--        </tbody>-->
<!--    </table>-->

</main>
