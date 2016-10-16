<?php
/*
Template Name: Heroes list
*/
?>

<?php get_header(); ?>

    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div class="lisa"><input class="lisakasutaja" placeholder="BattleTag#2413" type="text">
                    <button class="nupplisa">Lisa</button>
                </div>
                <?php

                $url = "https://api.lootbox.eu/pc/eu/indro-2407/profile";
                $json_string = file_get_contents($url);
                $parsed_json = json_decode($json_string);

                //var_dump($parsed_json->data;
                echo '<br>';
                echo '<table class="rank">';
                echo '<tr><th></th><th>Avatar</th><th>Nimi</th><th>Level</th><th>Rank</th><th></th></tr>';
                echo '<tr><td>1</td>';
                echo ' <td><img class="avatar" src="' . $parsed_json->data->avatar . '" alt=""></td>';
                echo '<td class="tabl">' . $parsed_json->data->username . '</td>';
                echo ' <td class="tabl">' . $parsed_json->data->level . '</td>';
                echo '<td class="tabl";>' . $parsed_json->data->competitive->rank . '</td>';
                echo '<td><img class="avatar" src="' . $parsed_json->data->competitive->rank_img . '" alt=""></td></tr>';
                echo '</table>';
                ?>

            </div> <!-- /.col -->

        </div> <!-- /.row -->
    </div>
<?php get_footer(); ?>