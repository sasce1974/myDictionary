<?php

use App\Models\Auth;

$token = bin2hex(random_bytes(64));
if(isset($_SESSION['token'])) unset($_SESSION['token']);
$_SESSION['token'] = $token;


if(!Auth::check()){
    header("Location: /login");
    exit();
}
$auth_user = Auth::user();
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
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    
<!--    <link rel="preload" href="/images/3436600.jpg" as="image">-->
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="/css/style.css?x=123456">
    <link rel="stylesheet" href="/css/loader.css?x=2">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css"
          integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu"
          crossorigin="anonymous">
<!--    <link rel="icon" type="image/svg+xml" href="/images/logo_w.svg.">-->

<!--    <link rel="alternate icon" type="image/png" href="/images/favicon.ico">-->
<!--    <link rel="icon" type="image/png" href="/images/favicon_d.png">-->
    <link rel="icon" type="image/png" href="/images/MDlogo.png">
    <?php
    if(isset($additional_css)) print $additional_css;
    ?>

</head>
<body>
<div id="loader" class="loader-dual-ring"></div>

<nav class="navbar navbar-expand-md navbar-light bg-light py-0">
    <a class="navbar-brand" href="#">
<!--        <img alt="Logo" src="/images/logo_w.svg" width="70px" height="auto">-->
        <img alt="Logo" src="/images/logo_dictionary1.svg" width="auto" height="50">
        MY DICTIONARY
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/groups/create">Create Group</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
    <?php
        if(Auth::check()){
            ?>
            <li class="nav-item dropdown float-right">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php print "Welcome " . $auth_user->name; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="/users/<?php echo $auth_user->id; ?>/account">My account</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/logout">Log off</a>
                </div>
            </li>



           <!-- <span class="navbar-text mr-2">
                <?php /*print "Welcome " . $auth_user->name; */?>
            </span>
            <a class="btn btn-sm btn-primary" href="/logout">Log off</a>-->
            <?php
        }else{
            ?>
            <li class="nav-item">
                <a class="btn btn-sm btn-primary" href="/login">Log in</a>
            </li>

            <?php
        }
    ?>
        </ul>
    </div>
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
