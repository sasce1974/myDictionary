<?php
$title = "Dictionary | Manage Group";
include $base_page;
?>

    <div class="d-md-flex m-1">
        <div class="rounded p-2 m-2 flex-grow-1" style="background-color: rgba(105,105,205,0.6); ">
            <h4 class="text-center text-uppercase text-warning"><?php print $group->name; ?></h4>
            <hr>
            <div class="px-5">
                <h5>Moderator: <?php print $group->owner()->name; ?></h5>
                <br>
                <p>
                    Country: <?php echo $group->country ?><br>
                    City: <?php echo $group->city ?></br>
                    Created: <?php echo date('d F Y', strtotime($group->created_at)); ?>
                </p>
            </div>
        </div>
        <div class="rounded flex-row flex-grow-1 p-2 m-2" style="background-color: rgba(105,105,205,0.6);min-height: 70vh">
            <h4 class="d-inline-flex">Members</h4>
            <form class="d-inline-flex" action="/groups/invite" method="get">
                <?php
                if(\App\Models\Auth::id() == $group->owner_id){
                    print "<input class='form-control form-control-sm' type='email' name='email' 
                            placeholder='Invite new user by email (e.g. john.doe@someemail.com)'>";
                    print "<button type='submit' class='btn btn-sm btn-primary'>Invite</button>";
                }
                ?>
            </form>

            <hr>
            <?php
            $x=1;
            foreach ($group->members() as $member){
                print "<div class='d-flex flex-row flex-grow-1 align-content-center'>";
                print "<h6 class='flex-grow-1'>$x. $member->name, <em><a href='mailto:$member->email'>$member->email</a></em></h6>";
                $x++;
                if(\App\Models\Auth::id() == $group->owner_id) print "<button class='btn btn-sm btn-outline-danger py-0'>x</button>";
                print "</div>";
            }
            ?>
        </div>
    </div>

</body>
</html>