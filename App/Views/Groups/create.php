<?php
$title = "Dictionary | Manage Group";
include $base_page;
?>

<h2 class="text-center"><?php isset($group) ? print "UPDATE GROUP $group->name" : print "CREATE GROUP"; ?></h2>

<div class="row m-0">
    <div class="col-md-4 text-center">
        <h4><?php print \App\Models\Auth::user()->name; ?></h4>
        <p class="text-secondary">Group Moderator</p>
    </div>
    <div class="mt-3 col-md-8">

        <form class="d-block" id="team_create_form" action="<?php isset($group) ? print "/groups/$group->id/update" : print "/groups/store"; ?>" method="post">
            <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
            <div class="row m-2">
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


            <div class="row m-2">
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

            <div class="row m-2">
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
            <div class="row m-2">
                <button type="submit" class="btn btn-success"><?php isset($group) ? print "Update" : print "Create"; ?></button>
            </div>
        </form>

        <?php if(isset($group)){ ?>
        <form class="" action="/groups/<?php echo $group->id; ?>/destroy" method="post">
            <input type="hidden" name="token" value="<?php isset($token) ? print $token : null; ?>">
            <button type="submit" class="btn btn-danger ml-auto" style="transform: translate(-20px, -55px)" onclick="return confirm('Do you want to delete the group? This action can not be undone!');"
                    title="Delete Group">Delete
            </button>
        </form>
        <?php } ?>

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
        console.log(data);
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
