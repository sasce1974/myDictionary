<?php

$title = "MyDictionary | User profile - $user->name";
isset($base_page) ? include $base_page : null;
$user_words = $user->countWords();
$progress_bar_color = 'primary';
if($user_words < 100){
    $progress_bar_color = 'danger';
}elseif ($user_words >=100 && $user_words < 500){
    $progress_bar_color = 'warning';
}elseif ($user_words >= 500){
    $progress_bar_color = 'success';
}
?>
    <div class="row m-1">
        <div class="col-lg-4">
            <div class="form-container text-center mb-4 pt-3">
                <?php if(isset($user->photo)){ ?>
                <div class="mb-3 mx-auto">
                    <img class="rounded-circle" height="150" src="<?php echo $user->photo; ?>" alt="User photo">
    <!--                    <button value="$photo_approved" id="approve-photo-btn" class="btn btn-default rounded-circle"><i class="fa fa-check"></i></button>-->
    <!--                    <input type="hidden" id="approve-photo-user" value="$user_id">-->
                </div>
                <?php } ?>
                <span class="text-muted d-block mb-2 text-uppercase">user profile</span>
                <h4 class="mb-0"><?php echo $user->name; ?></h4>

                <div class="progress-wrapper text-center mt-3">
    <!--                    <strong class="text-danger text-center d-block mb-2">USER REQUESTED ACCOUNT DELETION <br> $diff AGO</strong>
                    <form action="../../controllers/users/update_user.php" method="post">
                        <input type="hidden" name="restore_user" value="$user_id">
                        <button type="submit" class="btn btn-success">Restore Account</button>
                    </form>-->
                    <strong class="text-muted d-block mb-2">Words inserted: </strong>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-<?php echo $progress_bar_color; ?>" role="progressbar" aria-valuenow="6" aria-valuemin="0" aria-valuemax="1000" style="width: <?php print $user_words/1000 * 100; ?>%">
                            <span class="progress-value"><?php print $user_words . "/1000 (" . $user_words/1000 * 100 . "%)"; ?></span>
                        </div>
                    </div>
                </div>
            <?php if(isset($user->about) && !empty($user->about)){ ?>
                <div class="p-4">
                    <strong class="text-muted d-block mb-2">About</strong>
                    <span><?php echo nl2br($user->about); ?></span>
                </div>
            <?php } ?>
                <div class="p-2 text-left">
                    <strong class="text-muted d-block mb-2">My groups</strong>
                    <?php
                    foreach ($user->myGroups() as $group){
                        print "<div class='border-bottom'><em><a href='/groups/$group->id/show'>
                        $group->name</a></em>, <small>- {$group->countMembers()} member(s)
                        </small></div>";
                    }
                    ?>
                </div>

                <div class="p-2 text-left">
                    <strong class="text-muted d-block mb-2">Groups I belong</strong>
                    <?php
                    foreach ($user->groupsMember() as $group){
                        print "<div class='text-muted border-bottom'><em>
                        <a href='/groups/$group->id/show'>$group->name</a></em>, 
                        <small> Owner: {$group->owner()->name}, {$group->countMembers()} member(s)</small></div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="form-container">
                <h5 class="border-bottom py-2 mb-4">Account Details</h5>
                    <div class="row">
                        <div class="col">

                        <form action="/users/<?php echo $user->id; ?>/update" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?php if(isset($token)) echo $token; ?>">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic1">E-mail</span>
                                        </div>
                                        <input name="email" class="form-control" aria-describedby="basic1" type="text" value="<?php echo $user->email; ?>" required>
                                    </div>
                                </div>
                                <!--<div class="form-group text-left col-lg-5 mt-1">
                                    <label>Password</label>
                                    <a href="/reset/emailpass.php"> Change your password</a>
                                </div>-->

                                <div class="form-group col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic3">Name</span>
                                        </div>
                                        <input class="form-control" aria-describedby="basic3" type="text" name="name" value="<?php echo $user->name; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic4">Address</span>
                                    </div>
                                    <input class="form-control" aria-describedby="basic4" type="text" name="address" value="{$data['address']}">
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic5">Phone</span>
                                        </div>
                                        <input class="form-control" aria-describedby="basic5" type="number" name="phone" value="<?php echo $user->phone; ?>">
                                    </div>
                                </div>
                                <!--<div class="form-group col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic6">Birth Date</span>
                                        </div>
                                        <input class="form-control" aria-describedby="basic6" type="text" name="birth_date" value=$birth_date>
                                    </div>
                                </div>-->
                            </div>
                            <!--<div class="form-group">
                                <input type="hidden" name="MAX_FILE_SIZE" value="1200000">
                                <div class="custom-file text-left">
                                    <input type="file" name="image" id="image" class="custom-file-input">
                                    <label class="custom-file-label" for="image">Upload Photo</label>
                                </div>
                            </div>-->

                            <div class="form-group text-left">
                                <label>About user</label>
                                <textarea class="form-control" rows="5" name="about"><?php echo $user->about; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success float-left"><i class="fas fa-check"></i> Update</button>
                        </form>
                        <form action="/users/<?php echo $user->id; ?>/destroy" onsubmit="return confirm('Delete Account? \nAll data ' +
                         'including the dictionaries and words will be permanently deleted')" method="post">
                            <input type="hidden" name="token" value="<?php if(isset($token)) echo $token; ?>">
                            <button type="submit" class="btn btn-danger float-right"><i class="fas fa-trash"></i> Delete Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>