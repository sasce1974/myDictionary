<?php

$title = "MyDictionary | Users";
isset($base_page) ? include $base_page : null;

?>
    <div class="row m-1">
        <div class="col-lg-4">

        </div>

        <div class="col-lg-8">
            <div class="form-container">
                <h5 class="py-2 mb-4">USERS</h5>
                <table class="table table-striped text-light">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>IP</th>
                        <th>CREATED</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($users as $user){
                        print "<tr>\n";
                        print "<td>$user->id</td><td>$user->name</td><td>$user->email</td><td>$user->ip</td><td>$user->created_at</td>";
                        print "<td><form action='/users/$user->id/destroy' method='post'>
                                        <input type='hidden' name='token' value='$token'>
                                        <button type='submit' class='btn btn-outline-danger btn-sm py-0'>Delete</button>
                                    </form> </td>\n";
                        print "</tr>\n";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>