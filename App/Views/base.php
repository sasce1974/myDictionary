<?php

use App\Models\Auth;

try {
    $token = bin2hex(random_bytes(64));
    if(isset($_SESSION['token'])) unset($_SESSION['token']);
    $_SESSION['token'] = $token;
} catch (Exception $e) {
    print $e->getMessage();
}

if(!Auth::check()){
    header("Location: /login");
    exit();
}

?>
<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php  isset($title) ? print $title : print 'My Personal Dictionary'; ?></title>

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

    <?php
    if(isset($additional_css)) print $additional_css;
    ?>

</head>
<body>
<div id="loader" class="loader-dual-ring"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-light text-dark">
    <div><img alt="Logo" src="images/logo_w.svg" width="70px" height="auto"><h3 id="top">MY DICTIONARY</h3></div>
<!--    <a class="nav-link" href="/">Home</a>-->
<!--    <a class="nav-link" href="/boards/1/students">Board 1</a> |-->
<!--    <a class="nav-link" href="/boards/2/students">Board 2</a>-->
    <?php
        if(isset($user)){
        ?>
        <div class="pull-right"><?php print "Welcome " . $user->name; ?>
            <a href="/logout" style="background:blue;color:#fff;border-radius:5px;padding:3px;font-size:small;margin-left: 10px">Log off</a>
        </div>
        <?php
    }else{
         ?>
            <div class="pull-right">
                <a href="/login" style="color: #D4DFE6;margin-left: 10px">Log in</a>
            </div>
    <?php
        }
    ?>
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
