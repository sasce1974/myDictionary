<?php

use App\Models\Auth;

$title = "Dictionary | Manage Group";
include $base_page;

?>

    <div class="row m-1">
        <div class="p-1 col-md-6">
            <div class="h-100 rounded p-1" style="background-color: rgba(105,105,205,0.6);">
                <div class="">
                    <h4 class="text-center d-inline-block"><?php print "Group: <span class='text-uppercase text-warning'>" . $group->name . "</span>"; ?></h4>
                    <?php if($auth_user->id == $group->owner_id){ ?>
                        <a class="btn btn-outline-warning btn-sm float-right" href="/groups/<?php print $group->id; ?>/edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    <?php }elseif (!$group->hasUser($auth_user->id)){
                        print "<form class=\"float-right\" action='/groups/$group->id/join\' method='get'>";
                            print "<input type='hidden' name='token' value='$token'>";
                            if(!$group->joinRequestExists($auth_user->id)){
                                print "<button type=\"submit\" class=\"btn btn-primary btn-sm\">Join Group <i class='fas fa-plus'></i></button>";
                            }else{
                                print "<button type='button' class='btn btn-secondary btn-sm'>Join Request Sent <i class='fas fa-check'></i></button>";
                            }

                        print "</form>";
                    } ?>
                </div>
                <hr>
                <div class="px-1">
                    <?php
                    print "<h5>Moderator: " . ($group->owner() ? $group->owner()->name : 'Unknown') . "</h5>";
                    print "<br>";
                    print "<p>";
                    print "Country: $group->country<br>";
                    print "City: $group->city</br>";
                    print "Created: " . date('d F Y', strtotime($group->created_at));
                    print "</p>";
                    print "About this group:";
                    print "<p>" . nl2br($group->about) . "</p>";
                    ?>
                </div>
            </div>

        </div>
        <div class="col-md-6 p-1">
            <div class="h-100 rounded p-1" style="background-color: rgba(105,105,205,0.6);min-height: 70vh">
                <?php
                if($auth_user->id == $group->owner_id){ ?>
                    <h5 class="m-1">Invite New Member</h5>
                    <form class="d-inline-flex" action="/groups/<?php echo $group->id; ?>/invite" method="get">
                        <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
                        <input class='form-control form-control-sm m-1' type='text' name='name'
                               placeholder='Insert new member name'>
                        <input class='form-control form-control-sm m-1' type='email' name='email'
                               placeholder='Insert email (e.g. john.doe@someemail.com)'>
                        <button type='submit' class='btn btn-sm btn-primary m-1'>Invite</button>
                    </form>
                <?php }
                print "<hr>";
                print "<div class='m-1'>";
                print "<h5 class='d-inline-flex'>Group Members</h5>";
                $x=1;
                foreach ($group->members() as $member){
                    print "<div class='d-flex flex-row flex-grow-1 align-content-center'>";
                    print "<h6 class='flex-grow-1 border-bottom'>$x. $member->name";
                    if($auth_user->id == $group->owner_id) print ", <em><a href='mailto:$member->email'>$member->email</a></em>";
                    if($auth_user->id == $member->id) print " (me) ";
                    print "</h6>";
                    $x++;
                    if(($auth_user->id == $group->owner_id && $auth_user->id !==$member->id) ||
                        ($auth_user->id != $group->owner_id && $auth_user->id ==$member->id)){ ?>
                    <form onsubmit="return confirm('Cancel member from the group?')"
                          action="/groups/<?php echo $group->id; ?>/destroyMember/<?php echo $member->id; ?>" method="post">
                        <input type='hidden' name='token' value='<?php print $token; ?>'>
                        <button type="submit" class='btn btn-sm btn-outline-danger border-0 py-0 mb-2'>x</button>
                    </form>
                    <?php
                    }
                    print "</div>";
                }

                $invited = $group->invitedMembers();
                if(count($invited) > 0) {
                    print "<hr>";
                    print "<div class='text-secondary'>";
                    print "<h6 class=''>Invited Users</h6>";
                    $x = 1;
                    foreach ($invited as $member) {
                        print "<div class='d-flex flex-row flex-grow-1 align-content-center'>";
                        print "<h6 class='flex-grow-1 border-bottom border-secondary'>$x. $member->name";
                        if($auth_user->id == $group->owner_id) print ", <em><a href='mailto:$member->email'>$member->email</a></em>";
                        print "</h6>";
                        $x++;
                        if ($auth_user->id == $group->owner_id){
                            ?>
                            <form onsubmit="return confirm('Cancel the invitation?')"
                                  action="/groups/<?php echo $group->id; ?>/destroyMember/<?php echo $member->id; ?>" method="post">
                                <input type='hidden' name='token' value='<?php print $token; ?>'>
                                <button type="submit" class='btn btn-sm btn-outline-danger border-0 py-0 mb-2'>x</button>
                            </form>
                        <?php
                        }
                        print "</div>";
                    }
                    print "</div>";
                }

                //show join requests
                if($auth_user->id == $group->owner_id) {
                    $users_requested_to_join = $group->joinRequests();
                    if (count($users_requested_to_join) > 0) {
                        print "<hr>";
                        print "<div class='text-secondary'>";
                        print "<h6 class=''>Join requests</h6>";
                        $x = 1;
                        foreach ($users_requested_to_join as $member) {
                            $title = "No user message";
                            if ($member->user_message) $title = $member->user_message;
                            print "<div class='d-flex flex-row flex-grow-1 align-content-center' title='$title'>";
                                print "<h6 class='flex-grow-1 border-bottom border-secondary'>$x. $member->name";
                                print ", <em><a href='mailto:$member->email'>$member->email</a></em>";
                                print "</h6>";
                                $x++;

                                print "<a class='btn btn-success btn-sm py-0 mb-2' href='/groups/$group->id/joinAccept/$member->user_id'><span class='small'><i class='fas fa-check'></i> Accept</span></a>";
                                print "<a class='btn btn-danger btn-sm py-0 mb-2 ml-2' href='/groups/$group->id/joinDecline/$member->user_id'><span class='small'><i class='fas fa-times'></i> Decline</span></a>";

                                /*print "<form onsubmit=\"return confirm('Reject the join request?')\"
                                      action=\"/groups/$group->id/joinDecline/$member->user_id\"
                                      method=\"post\">";
                                    print "<input type='hidden' name='token' value='$token'>";
                                    print "<button type=\"submit\" class='btn btn-sm btn-danger py-0 mb-2 ml-2'><span class='small'><i class='fas fa-times'></i> Decline</span></button>";*/
                                print "</form>";
                            print "</div>";
                        }
                        print "</div>";
                    }
                }

                ?>
            </div>

        </div>
    </div>

</body>
</html>