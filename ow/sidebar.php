<div class="col-sm-3 col-sm-offset-1 blog-sidebar">

    <div class="sidebar-module">
        <p style="text-align: center;">Eesti TOP#3 <br>
            <?php global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC LIMIT 3");
            foreach ($result as $print) {
                echo $print->nimi . " " . $print->rank . "<br>";
            } ?>
        </p>
        <ol class="list-unstyled">
            <li class="sidebox"><a class="btn" href="">Eesti ranking</a></li>
            <li class="sidebox2"><a class="btn" href="">Tiimid</a></li>
            <li class="sidebox3"><a class="btn" href="">Heroes</a></li>
        </ol>
    </div>
</div><!-- /.blog-sidebar -->


