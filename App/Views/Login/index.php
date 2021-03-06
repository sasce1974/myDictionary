<?php

use App\Models\Auth;

try {

    $token = bin2hex(random_bytes(64));
    if (isset($_SESSION['token'])) unset($_SESSION['token']);
    $_SESSION['token'] = $token;
} catch (Exception $e) {
    print $e->getMessage();
}
if (Auth::check()) {
    Auth::logout();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description"
          content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
    <meta name="keywords"
          content="Dictionary, English, Language, Study language, Share dictionary with team, Multilanguage">
    <meta name="author" content="3Delacto">

    <meta property="og:title" content="MyDictionary | Login">
    <meta property="og:image" content="https://3delacto.com/img/logo.png">
    <meta property="og:url" content="https://dictionary.papucraft.com">
    <meta property="og:site_name" content="MyDictionary">
    <meta property="og:description"
          content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
    <meta name="twitter:title" content="MyDictionary | Login">
    <meta name="twitter:image" content="https://3delacto.com/img/logo.png">
    <meta name="twitter:url" content="https://dictionary.papucraft.com">
    <meta name="twitter:card"
          content="Multi language dictionary to English dictionary - Personal and Team/Classroom shared dictionary">
<!--    <link rel="icon" type="image/png" href="https://3delacto.com/favicon.ico">-->
    <link rel="icon" type="image/png" href="/images/favicon_d.png">

    <title>My Dictionary - Let's Language Together</title>

    <!-- Bootstrap core CSS -->
    <!--  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- Custom fonts for this template -->
    <!--  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css"
          integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu"
          crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet'
          type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'
          rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="/css/clean-blog.css" rel="stylesheet">

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img alt="Logo" src="/images/logo_w.svg" width="70px" height="auto">
            MyDictionary
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
            Menu
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/login">LOGIN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register">REGISTER</a>
                </li>

<!--                <li class="nav-item">
                    <a class="nav-link" href="/contact">Contact</a>
                </li>-->
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<header class="masthead mb-3" style="background-image: url('/images/home-bg.jpg')">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="site-heading">
                    <h1>MyDictionary</h1>
                    <span class="subheading">Let's Language Together</span>
                </div>

                <div class="card mt-5 mx-auto ml-0 mr-0" style="background: rgba(250, 250, 250, 0.7);">
                    <!--          <div class="card-header">-->
                    <!--            Login to <b>MY DICTIONARY</b>-->
                    <!--          </div>-->
                    <div>
                        <?php
                        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                            if (is_array($_SESSION['error'])) {
                                foreach ($_SESSION['error'] as $error) { ?>
                                    <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>ERROR! </strong> <?php print $error; ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php
                                }
                            } else {
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

                        if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
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


                    <div class="card-body" style="">
                        <div class="m-3">
                            <form class="row m-0" id="loginForm" method="POST" action="/login/new">
                                <input type="hidden" name="init"
                                       value="<?php if (isset($_SESSION['token'])) print $_SESSION['token']; ?>">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <!--<div class="input-group-prepend">
                                          <span class="input-group-text" id="basic1">E-mail</span>
                                        </div>-->
                                        <input class="form-control form-control-sm" aria-describedby="basic1" id="email"
                                               name="email" type="email" placeholder="Username/Email" required>
                                    </div>
                                    <span id="info_login"></span>
                                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <!--<div class="input-group-prepend">
                                          <span class="input-group-text" id="basic2">Password</span>
                                        </div>-->
                                        <input class="form-control form-control-sm" aria-describedby="basic2"
                                               id="password" name="password" type="password" placeholder="Password"
                                               required>
                                    </div>
                                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                                </div>
                                <div class="mx-auto">
                                    <button class="btn btn-success" type="submit" id="submit" name="submit">Login
                                    </button>
                                </div>
                            </form>
<!--                            <hr>-->
<!--                            <p class="card-text text-right mt-0">-->
                                <!--                  Forgotten password? Click <a class="card-link" href="/reset">here</a> <br>-->
<!--                                Or sign up for free account <a class="card-link" href="/register">here</a>-->
<!--                            </p>-->
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
</header>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="post-preview text-center">

                <h2 class="post-title">
                    Dictionary? Another one? Why do I need it?
                </h2>
                <a href="#post-subtitle">
                    <img src="/images/png_arrow_down.png" width="80px" height="auto">
                </a>
                <h4 class="post-subtitle" id="post-subtitle">
                    This is not just a dictionary! We don't give you translation. Here YOU are the one that
                    will form your own dictionary. And guess what? It can be shared with your group, classroom,
                    friends, teacher... or just keep private.
                </h4>
                <strong>Features:</strong>
                <div class="row m-2 p-3 d-flex align-items-center mr-md-5"
                     style="border-radius: 30px; background-color: #faa">
                    <div class="col-8 text-right">
                        Create and manage different language dictionaries
                    </div>
                    <div class="col-4">
                        <img width="100px" height="auto" src="/images/favpng_book-stack.png" alt="dictionaries">
                    </div>
                </div>
                <div class="row m-2 p-3 d-flex align-items-center ml-md-5"
                     style="border-radius: 30px; background-color: #aaf">
                    <div class="col-4 text-right">
                        <img width="100px" height="auto" src="/images/write_words.png" alt="write words">
                    </div>
                    <div class="col-8">
                        Insert and save words or whole phrases
                    </div>
                </div>

                <div class="row m-2 p-3 d-flex align-items-center mr-md-5"
                     style="border-radius: 30px; background-color: #afa">
                    <div class="col-8 text-right">
                        Create and manage own group / invite other members, then share all the dictionary within the
                        group
                    </div>
                    <div class="col-4">
                        <img width="100px" height="auto" src="/images/png_group.png" alt="dictionaries">
                    </div>
                </div>
                <div>

                    <div class="row m-2 p-3 d-flex align-items-center ml-md-5"
                         style="border-radius: 30px; background-color: #ffa">
                        <div class="col-4 text-right">
                            <img width="100px" height="auto" src="/images/png_search.png" alt="search words">
                        </div>
                        <div class="col-8 text-left">
                            Search for words/phrases trough own or group dictionaries
                        </div>
                    </div>

                    <div class="row m-2 p-3 d-flex align-items-center mr-md-5"
                         style="border-radius: 30px; background-color: #11b">
                        <div class="col-8 text-right text-light">
                            <b>Thesaurus</b><br> Get Definitions, Synonyms, Antonyms, Similar words from your English
                            translation
                        </div>
                        <div class="col-4">
                            <img width="100px" height="auto" src="/images/png_thesaurus.png" alt="thesaurus">
                        </div>
                    </div>

                    <div class="text-center p-3">
                        Many more futures to come...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <!--          <ul class="list-inline text-center">-->
                <!--            <li class="list-inline-item">-->
                <!--              <a href="#">-->
                <!--                <span class="fa-stack fa-lg">-->
                <!--                  <i class="fas fa-circle fa-stack-2x"></i>-->
                <!--                  <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>-->
                <!--                </span>-->
                <!--              </a>-->
                <!--            </li>-->
                <!--            <li class="list-inline-item">-->
                <!--              <a href="#">-->
                <!--                <span class="fa-stack fa-lg">-->
                <!--                  <i class="fas fa-circle fa-stack-2x"></i>-->
                <!--                  <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>-->
                <!--                </span>-->
                <!--              </a>-->
                <!--            </li>-->
                <!--            <li class="list-inline-item">-->
                <!--              <a href="#">-->
                <!--                <span class="fa-stack fa-lg">-->
                <!--                  <i class="fas fa-circle fa-stack-2x"></i>-->
                <!--                  <i class="fab fa-github fa-stack-1x fa-inverse"></i>-->
                <!--                </span>-->
                <!--              </a>-->
                <!--            </li>-->
                <!--          </ul>-->
                <p class="copyright text-muted">Copyright &copy; dictionary.papucraft.com 2021</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap core JavaScript -->
<script src="/js/jquery-3.3.1.min.js"></script>
<!--  <script src="vendor/jquery/jquery.min.js"></script>-->
<!--  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"></script>
<!-- Custom scripts for this template -->
<script src="/js/clean-blog.min.js"></script>

</body>

</html>
