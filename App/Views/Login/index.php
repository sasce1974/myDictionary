<?php

use App\Models\Auth;

try {

    $token = bin2hex(random_bytes(64));
    if(isset($_SESSION['token'])) unset($_SESSION['token']);
    $_SESSION['token'] = $token;
} catch (Exception $e) {
    print $e->getMessage();
}
if(Auth::check()) {
    Auth::logout();
}

?>
<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>MyDictionary | Login</title>

    <meta name="description" content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
    <meta name="keywords" content="Dictionary, English, Language, Study language, Share dictionary with team, Multilanguage">
    <meta name="author" content="3Delacto">

    <meta property="og:title" content="MyDictionary | Login">
    <meta property="og:image" content="https://3delacto.com/img/logo.png">
    <meta property="og:url" content="https://dictionary.papucraft.com">
    <meta property="og:site_name" content="MyDictionary">
    <meta property="og:description" content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
    <meta name="twitter:title" content="MyDictionary | Login">
    <meta name="twitter:image" content="https://3delacto.com/img/logo.png">
    <meta name="twitter:url" content="https://dictionary.papucraft.com">
    <meta name="twitter:card" content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
    <link rel="icon" type="image/png" href="https://3delacto.com/favicon.ico">



    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- Scripts -->
<!--    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
<!--    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>-->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css?x=1">
    <link rel="stylesheet" href="css/loader.css?x=1">
    <link rel="stylesheet" href="css/login_form.css?x=1">

</head>
<body>
<div id="loader" class="loader-dual-ring"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-light text-dark">
    <div><img alt="Logo" src="images/logo_w.svg" width="70px" height="auto"><h5 id="top">MY DICTIONARY</h5></div>
</nav>

<div>
    <?php
    if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
        if(is_array($_SESSION['error'])){
            foreach ($_SESSION['error'] as $error){ ?>
                <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>ERROR! </strong> <?php print $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
<?php
            }
        }else {
            ?>
            <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                <strong>ERROR! </strong> <?php print $_SESSION['error']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        }
    }

    if(isset($_SESSION['message']) && !empty($_SESSION['message'])){
        ?>
        <div class="m-3 alert alert-success alert-dismissible fade show" role="alert">
            <strong>SUCCESS! </strong> <?php print $_SESSION['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }
    $_SESSION['error'] = $_SESSION['message'] = null;
    ?>
</div>
    <div class="card mt-5 mx-auto ml-0 mr-0" style="background: rgba(250, 250, 250, 0.7)">
        <div class="card-header">
            Login to <b>MY DICTIONARY</b>
        </div>
        <div class="card-body">
            <div class="m-3">
                <form class="row m-0" id="loginForm" method="POST" action="/login/new">
                    <input type="hidden" name="init" value="<?php if(isset($_SESSION['token'])) print $_SESSION['token']; ?>">
                    <div class="form-group col-lg-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic1">E-mail</span>
                            </div>
                            <input class="form-control" aria-describedby="basic1" id="email" name="email" type="email" required>
                        </div>
                        <span id="info_login"></span>
                        <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                    </div>
                    <div class="form-group col-lg-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic2">Password</span>
                            </div>
                            <input class="form-control" aria-describedby="basic2" id="password" name="password" type="password" required>
                        </div>
                        <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                    </div>
                    <div class="mx-auto">
                        <button class="btn btn-success" type="submit" id="submit" name="submit">Login</button>
                    </div>
                </form><hr>
                <p class="card-text">
                    Forgotten password? Click <a class="card-link" href="/reset">here</a> <br>
                    If no account, sign up for free <a class="card-link" href="/register">here</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>