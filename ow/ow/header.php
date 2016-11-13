<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <title>
        <?php wp_title('|', true, 'right'); ?>
    </title>

    <?php wp_head(); ?>
</head>

<body>

<div class="blog-masthead">
    <div class="container">
        <nav class="blog-nav">
            <ul class="topnav" id="myTopnav">

                <!--
                <a href="http://localhost/overwatch.ee/"><img id="navimg" style="height:50px;margin-left:5px;"
                                                              src="http://localhost/overwatch.ee/wp-content/themes/ow/imgs/logotryout.png"
                                                              alt=""></a>
                -->
                <a href="https://www.facebook.com/eestiow/"><img id="navfb"
                                                                 src="http://localhost/overwatch.ee/wp-content/themes/ow/imgs/fb.png"
                                                                 alt=""></a>
                <?php wp_list_pages('&title_li='); ?>
                <li class="icon">
                    <a href="javascript:void(0);" style="font-size:15px;" onclick="myFunction()">☰</a>
                </li>
                <ul>
        </nav>
    </div>
</div>


<div class="container2">
    <div class="blog-header">
        <a href="http://localhost/overwatch.ee">
            <div class="logo"><img id="peidetud"
                                   src="http://localhost/overwatch.ee/wp-content/themes/ow/imgs/sininelogohover.png"
                                   alt=""></div>
        </a>
    </div>
</div>

<script>
    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script>