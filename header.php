<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Edetabel Eesti Overwatch mängjatest ning statistika iga mängja kohta.">
    <meta name="author" content="Indro Malleus">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
    <link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

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
                <a href="<?= get_site_url();?>">
                    <img id="navimg" style="height:40px;margin-left:-50px;"
                         src="https://overwatch.ee/wp-content/themes/ow/imgs/logotryout.png"
                         alt=""></a>

                <a href="https://www.facebook.com/groups/947381852026808/">
                    <img id="navfb" src="<?= get_site_url() ?>/wp-content/themes/ow/imgs/fb.png" alt="">
                </a>
                <?php wp_list_pages('&title_li='); ?>
                <li class="icon">
                    <a href="javascript:void(0);" style="font-size:15px;" onclick="myFunction()">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </a>
                </li>
                <ul>
        </nav>
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