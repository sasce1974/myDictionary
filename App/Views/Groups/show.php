<?php
$title = "Dictionary | Manage Group";
include $base_page;
?>

    <div class="d-md-flex m-1">
        <div class="rounded p-2 m-2 flex-grow-1" style="background-color: rgba(105,105,205,0.6); ">
            <div class="">
                <h4 class="text-center text-uppercase text-warning d-inline-block"><?php print $group->name; ?></h4>
                <?php if(\App\Models\Auth::id() == $group->owner_id){ ?>
                <a class="btn btn-outline-warning btn-sm float-right" href="/groups/<?php print $group->id; ?>/edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <?php } ?>
            </div>
            <hr>
            <div class="px-5">
                <h5>Moderator: <?php print $group->owner()->name; ?></h5>
                <br>
                <p>
                    Country: <?php echo $group->country ?><br>
                    City: <?php echo $group->city ?></br>
                    Created: <?php echo date('d F Y', strtotime($group->created_at)); ?>
                </p>
                About this group:
                <p><?php print $group->about; ?></p>
            </div>
        </div>
        <div class="rounded flex-row flex-grow-1 p-2 m-2" style="background-color: rgba(105,105,205,0.6);min-height: 70vh">
            <?php
            if(\App\Models\Auth::id() == $group->owner_id){ ?>
                <h5>Invite New Member</h5>
            <form class="d-inline-flex" action="/groups/<?php echo $group->id; ?>/invite" method="get">
                <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
                    <input class='form-control form-control-sm' type='text' name='name'
                       placeholder='Insert new member name'>
                    <input class='form-control form-control-sm' type='email' name='email'
                            placeholder='Insert email (e.g. john.doe@someemail.com)'>
                    <button type='submit' class='btn btn-sm btn-primary'>Invite</button>
            </form>
            <?php }
            print "<hr>";
            print "<h5 class='d-inline-flex'>Group Members</h5>";
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