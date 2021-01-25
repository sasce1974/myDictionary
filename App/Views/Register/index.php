<?php
try {
    $token = bin2hex(random_bytes(64));
    if(isset($_SESSION['token'])) unset($_SESSION['token']);
    $_SESSION['token'] = $token;
} catch (Exception $e) {
    print $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>MyDictionary | Register New User</title>
  <meta charset = "UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">-->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <!--    Font Awesome 5 Icons-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/font-awesome.css">
    <!--<link rel="stylesheet" href="shards-dashboards.1.1.0.min.css">-->

    <!--<link rel="stylesheet" href="styles/all.css">-->
    <link rel="shortcut icon" href="../favicon.ico">

    <link href="/css/login_form.css" rel="stylesheet">

    <script src="js/jquery-3.3.1.min.js"></script>

</head>
<body>
<div id="loader" class="loader-dual-ring"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-light text-dark">
    <div><img alt="Logo" src="images/logo_w.svg" width="70px" height="auto"><span id="">MY DICTIONARY</span></div>
</nav>
<div class="wrap">
<div class="card mx-auto ml-0 mr-0" style="background: rgba(250, 250, 250, 0.7); position: relative">
    <div class="card-header">
        Register your account to <b>MyDictionary</b>
        <p class="float-right">
            To sign in, please click <a href="/login">here</a>
        </p>
    </div>
    <div class="card-body pt-0">
        <div class="mx-3" id="errorDiv"></div>
        <div class="mx-3">
        <?php
            if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                unset($_SESSION['formAttempt']);
                print <<<HERE
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <b><i class="far fa-frown mx-2"></i></b>
HERE;
                if (is_array($_SESSION['error'])) {
                    print "<b>Error: </b>";
                    foreach ($_SESSION['error'] as $error) {
                        print $error . "<br> \n";
                    }
                } else {
                    print "<b>Error: </b>" . $_SESSION['error'];
                }
                unset($error, $_SESSION['error']);
            }
            print "</div>";

            /*$user = new User;
            if($user->isLogged){
                $user->logout();
            }*/
        ?>
        </div>
        <div class="m-3">
            <form id="newUserForm" method="POST" action="/register/store">
                <input type="hidden" name="init" value="<?php print $_SESSION['token'];  ?>">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic1">E-mail</span>
                        </div>
                        <input placeholder="some@email.com" class="form-control p-2" aria-describedby="basic1"
                               onblur ="checkIfUserExist()" id="email" name="email" type="email"
                               value="<?php isset($_GET['email']) ? print $_GET['email'] : null; ?>" required>
                    </div>
                    <span id="user_check"></span>
                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic2">Password</span>
                        </div>
                        <input class="form-control p-2" aria-describedby="basic2" id="password1" name="password1" type="password"
                        pattern=".{1,20}" placeholder="Use strong password" title="1-20 characters" required>
                    </div>
                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic3">Repeat Password</span>
                        </div>
                        <input placeholder="Repeat the password" class="form-control p-2" aria-describedby="basic3" id="password2" name="password2" type="password"
                               required>
                    </div>
                    <small class="errorFeedback errorSpan" id="password2Error">Passwords don’t match</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic4">Name and Surname</span>
                        </div>
                        <input placeholder="John Doe" class="form-control p-2" aria-describedby="basic4" id="name"
                               name="name" type="text" value="<?php isset($_GET['name']) ? print $_GET['name'] : null ?>"
                               required>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-success" type="submit" id="submit" name="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    function checkIfUserExist() {
        let email = $("#email").val();
        if (email!=="") {
            $.post("/register/checkEmailExist",{"email":email},check_info);
        }
    }
    function check_info(data, textStatus){
        $("#user_check").html(data);
    }
</script>
<!--<script type="text/javascript" src="../js/form.js"></script>-->
</body>
</html>