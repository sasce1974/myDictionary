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
                        <input class="mr-0 form-control<?php if(isset($_SESSION['errors["group_name"]']) && !empty($_SESSION['errors["group_name"]'])) print ' is_invalid'; ?>"
                               aria-describedby="basic3" type="text" name="group_name"
                               value="<?php isset($_GET['group_name']) ? print filter_var($_GET['group_name'], FILTER_SANITIZE_STRING) : null; ?>"
                               title="Insert Group Name" placeholder="Insert some memorable group name">
                        <?php if(isset($_SESSION['errors["group_name"]']) && !empty($_SESSION['errors["group_name"]'])) { ?>
                            <div class="invalid-feedback">
                                <strong><?php print $_SESSION['errors["group_name"]']; ?></strong>
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
                              placeholder="Insert any information about this team, to whom is intended, purpose..."><?php if(isset($_GET['about'])) print filter_var($_GET['about'], FILTER_SANITIZE_STRING); ?></textarea>
                    <?php if(isset($_SESSION['errors["about"]']) && !empty($_SESSION['errors["about"]'])) { ?>
                    <div class="invalid-feedback">
                        <strong><?php print $_SESSION['errors["about"]']; ?></strong>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row m-2 d-flex justify-content-center">
                <button type="submit" class="btn btn-success float-left"><?php isset($group) ? print "Update" : print "Create"; ?></button>
            </div>
        </form>

        <?php if(isset($group)){ ?>
        <form class="float-right mr-3" action="/groups/<?php echo $group->id; ?>/destroy', $user->id)}}" method="post">
<!--                    <input type="hidden" name="token" value="--><?php //isset($token) ? print $token : null; ?><!--">-->
            <button type="submit" class="btn btn-danger" onclick="return confirm('Do you want to delete the group? This action can not be undone!');"
                    title="Delete Group">Delete
            </button>
        </form>
        <?php } ?>

    </div>
</div>
<script>
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
            output += "<option value='" + data[i] + "'>" + data[i] + "</option>";
        }
        $("#citi_block").removeClass('d-none');
        city.html(output);
    }
</script>
</body>
</html>
