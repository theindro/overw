<div class="col-sm-3 col-sm-offset-1 blog-sidebar">

    <div class="sidebar-module">
        <div id="top3">
            <?php global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC LIMIT 3");
            echo '<table><th colspan="2">Eesti top 3</th>';
            foreach ($result as $print) {
                echo '<tr><td>' . $print->nimi . ' </td><td style="text-align:right;"> ' . $print->rank . ' <img id="top3size" src="' . $print->pilt . '" alt=""></td></tr>';
            }
            echo '</table>'?>
            </p>
        </div>
        <ol class="list-unstyled">
            <li class="sidebox"><a class="btn" href="">Eesti ranking</a></li>
            <li class="sidebox2"><a class="btn" href="">Tiimid</a></li>
            <li class="sidebox3"><a class="btn" href="">Heroes</a></li>
        </ol>
    </div>
</div><!-- /.blog-sidebar -->


