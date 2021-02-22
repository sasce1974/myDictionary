<?php
$title = "Dictionary | Manage Group";
isset($base_page) ? include $base_page : null;
?>

<h3 class="text-center mt-2"><?php isset($group) ? print "Update group: <span class='text-warning'>$group->name</span>" : print "Create New Group"; ?></h3>

<div class="row m-1">
    <div class="p-1 col-md-6 text-center mb-2">
        <div class="p-1 h-100 rounded d-flex flex-column justify-content-center" style="background-color: rgba(105,105,205,0.6);">
            <?php if(isset($group)){

            print "<div class=\"px-1 text-left\">";
            print "<h5>Moderator: " . $group->owner()->name . "</h5>";
            print "<br>";
            print "<p>";
            print "Country: $group->country<br>";
            print "City: $group->city</br>";
            print "Created: " . date('d F Y', strtotime($group->created_at));
            print "</p>";
            print "About this group:";
            print "<p>$group->about</p>";

            print "</div>";
            }else{
            print "<h4 class=\"mb-0\">$auth_user->name</h4>";
            print "<p class=\"text-secondary\">Group Moderator</p>";
            }
            ?>
        </div>
    </div>
    <div class="p-1 col-md-6 mb-2">
        <div class="p-1 h-100 rounded p-2" style="background-color: rgba(105,105,205,0.6);">
            <form class="d-block mt-2" id="team_create_form" action="<?php isset($group) ? print "/groups/$group->id/update" : print "/groups/store"; ?>" method="post">
                <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
                <div class="row m-0">
                    <div class="form-group col-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic3">Group Name</span>
                            </div>
                            <input class="mr-0 form-control<?php if(isset($_SESSION['errors["name"]']) && !empty($_SESSION['errors["name"]'])) print ' is_invalid'; ?>"
                                   aria-describedby="basic3" type="text" name="group_name"
                                   value="<?php isset($group) ? print $group->name : null; ?>"
                                   title="Insert Group Name" placeholder="Insert some memorable group name">
                            <?php if(isset($_SESSION['errors["name"]']) && !empty($_SESSION['errors["name"]'])) { ?>
                                <div class="invalid-feedback">
                                    <strong><?php print $_SESSION['errors["name"]']; ?></strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="row m-0">
                    <div class="form-group col-lg-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic1a">Country</span>
                            </div>
                            <select name="country" id="country" class="form-control <?php if(isset($_SESSION['errors["country"]']) && !empty($_SESSION['errors["country"]'])) print 'is-invalid';?>" aria-describedby="basic1a" onchange="getCities()">
                                <option>Select Country</option>
                                <?php
                                if(isset($countries)) {
                                    foreach($countries as $country){
                                        print "<option value='$country'";
                                        if(isset($group) && $country == $group->country) print "selected";
                                        print ">$country</option>";
                                    }
                                }
                                ?>
                            </select>
                            <?php if(isset($_SESSION['errors["country"]']) && !empty($_SESSION['errors["country"]'])) { ?>
                                <div class="invalid-feedback">
                                    <strong><?php print $_SESSION['errors["country"]']; ?></strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div id="citi_block" class="form-group col-lg-6 d-none">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic5a">City</span>
                            </div>
                            <select name="city" id="city" class="form-control <?php if(isset($_SESSION['errors["city"]']) && !empty($_SESSION['errors["city"]'])) print 'is-invalid';?>" aria-describedby="basic5a">
                                <option></option>
                                <?php
                                if(isset($group, $cities)) {
                                    for ($i = 0; $i < count($cities['id']); $i++) {
                                        print "<option value='{$cities['id'][$i]}'";
                                        if ($cities['id'][$i] == $group->city_id) print 'selected';
                                        print ">{$cities['city'][$i]}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <?php if(isset($_SESSION['errors["city"]']) && !empty($_SESSION['errors["city"]'])) { ?>
                                <div class="invalid-feedback">
                                    <strong><?php print $_SESSION['errors["city"]']; ?></strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row m-0">
                    <div class="col-12 text-left">
                        <label>About The Group</label>
                        <textarea class="form-control <?php if(isset($_SESSION['errors["about"]']) && !empty($_SESSION['errors["about"]'])) print 'is-invalid';?>" rows="5"
                                  name="about" title="Insert any information about this team, to whom is intended, purpose..."
                                  placeholder="Insert any information about this team, to whom is intended, purpose..."><?php if(isset($group)) print $group->about; ?></textarea>
                        <?php if(isset($_SESSION['errors["about"]']) && !empty($_SESSION['errors["about"]'])) { ?>
                            <div class="invalid-feedback">
                                <strong><?php print $_SESSION['errors["about"]']; ?></strong>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row mt-2 ml-3">
                    <button type="submit" class="btn btn-success"><?php isset($group) ? print "<i class='fas fa-check'></i> Update" : print "Create"; ?></button>
                </div>
            </form>

            <?php if(isset($group)){ ?>
                <form onsubmit="return confirm('Do you want to delete the group? This action can not be undone!')" action="/groups/<?php echo $group->id; ?>/destroy" method="post">
                    <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
                    <button type="submit" class="btn btn-danger ml-auto float-right"
                            style="transform: translate(-15px, -40px)"
                            title="Delete Group"><i class='fas fa-trash'></i> Delete
                    </button>
                </form>
            <?php } ?>
        </div>




    </div>
</div>
<script>
    $(window).on('load', function () {
        let country = "<?php if(isset($group)) print $group->country; ?>"; //$("#country").html();
        //console.log(country);
        if(country !== ""){
            getCities();
        }
    });


    function getCities() {
        const country = $("#country").val();
        $.get('/groups/cities', {'country': country}, populateCities);
    }

    function populateCities(data) {
        data = JSON.parse(data);

        let city = $("#city");
        let output = "";
        output = "<option>Choose City</option>";
        for(let i = 0; i < data.length; i++){
            let chosen = "";
            if(data[i] == "<?php if(isset($group)) print $group->city; ?>") chosen = 'selected';
            output += "<option value='" + data[i] + "'" + chosen + ">" + data[i] + "</option>";
        }
        $("#citi_block").removeClass('d-none');
        city.html(output);
    }
</script>
</body>
</html>
