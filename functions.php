<?php

// Add scripts and stylesheets
function startwordpress_scripts()
{
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.3.6');
    wp_enqueue_style('blog', get_template_directory_uri() . '/css/blog.css');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.3.6', true);
}

add_action('wp_enqueue_scripts', 'startwordpress_scripts');

// Add Google Fonts
function startwordpress_google_fonts()
{
    wp_register_style('OpenSans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800');
    wp_enqueue_style('OpenSans');
}

add_action('wp_print_styles', 'startwordpress_google_fonts');

// WordPress Titles
function startwordpress_wp_title($title, $sep)
{
    global $paged, $page;
    if (is_feed()) {
        return $title;
    }
    // Add the site name.
    $title .= get_bloginfo('name');
    // Add the site description for the home/front page.
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && (is_home() || is_front_page())) {
        $title = "$title $sep $site_description";
    }
    return $title;
}

add_filter('wp_title', 'startwordpress_wp_title', 10, 2);

// Custom settings
function custom_settings_add_menu()
{
    add_menu_page('Custom Settings', 'Custom Settings', 'manage_options', 'custom-settings', 'custom_settings_page', null, 99);
}

add_action('admin_menu', 'custom_settings_add_menu');

// Create Custom Global Settings
function custom_settings_page()
{ ?>
    <div class="wrap">
        <h1>Custom Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('section');
            do_settings_sections('theme-options');
            submit_button();
            ?>
        </form>
    </div>
<?php }

// Twitter
function setting_twitter()
{ ?>
    <input type="text" name="twitter" id="twitter" value="<?php echo get_option('twitter'); ?>"/>
<?php }

function setting_github()
{ ?>
    <input type="text" name="github" id="github" value="<?php echo get_option('github'); ?>"/>
<?php }


//widgetizing

function arphabet_widgets_init()
{

    register_sidebar(array(
        'name' => 'Home right sidebar',
        'id' => 'home_right_1',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ));

}

add_action('widgets_init', 'arphabet_widgets_init');


//battletag profiil
add_filter('query_vars', 'init_custom_rewrite_query_vars');
function init_custom_rewrite_query_vars($query_vars)
{
    $query_vars[] = 'battletag';
    return $query_vars;
}


add_action('init', 'init_custom_rewrite');


function init_custom_rewrite()
{
    // Remember to flush the rules once manually after you added this code!
    add_rewrite_rule(
    // The regex to match the incoming URL
        'profiil/([^/]+)/?',
        // The resulting internal URL: `index.php` because we still use WordPress
        // `pagename` because we use this WordPress page
        // `designer_slug` because we assign the first captured regex part to this variable
        'index.php/profiil?battletag=$1&submit=Esita',
        // This is a rather specific URL, so we add it to the top of the list
        // Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
        'top');
}

function custom_excerpt_length($length)
{
    return 20;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);

function custom_settings_page_setup()
{
    add_settings_section('section', 'All Settings', null, 'theme-options');
    add_settings_field('twitter', 'Twitter URL', 'setting_twitter', 'theme-options', 'section');
    add_settings_field('github', 'GitHub URL', 'setting_github', 'theme-options', 'section');

    register_setting('section', 'twitter');
    register_setting('section', 'github');
}

add_action('admin_init', 'custom_settings_page_setup');

// Support Featured Images
add_theme_support('post-thumbnails');
set_post_thumbnail_size(50, 50);
add_image_size('single-post-thumbnail', 600, 250);

// Custom Post Type
function create_my_custom_post()
{
    register_post_type('my-custom-post',
        array(
            'labels' => array(
                'name' => __('My Custom Post'),
                'singular_name' => __('My Custom Post'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'custom-fields'
            )
        ));
}

add_action('init', 'create_my_custom_post');


// Custom ajax calls

add_action('wp_head', 'ajaxurl');
function ajaxurl()
{
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}

wp_enqueue_script('jquery');

function battletagApiToDatabase()
{

    global $wpdb;
    $input_battle_tag = trim($_POST['battletag']);
    $encoded_battletag = urlencode($input_battle_tag);

    // Check if input is empty
    if (empty($input_battle_tag)) {
        exit('Palun sisesta battletag!');
    }

    $battle_tag = $wpdb->get_results("SELECT * FROM wp_ranking where battle_tag = '$input_battle_tag'");

    // Get data for battle tag from API if battletag does not exist in database
    if (empty($battle_tag)) {
        $options = array('http' => array('user_agent' => 'custom user agent string'));
        $context = stream_context_create($options);
        $response = @file_get_contents("https://owapi.net/api/v3/u/$encoded_battletag/blob", false, $context);
        $parsed_json = json_decode($response);

        $overall_stats = $parsed_json->eu->stats->competitive->overall_stats;
        $average_stats = $parsed_json->eu->stats->competitive->average_stats;
        $game_stats = $parsed_json->eu->stats->competitive->game_stats;

        $parsed_to_array = json_decode($response, true);

        // Check if battletag exists in API
        $check = $parsed_json->eu;
        if (empty($check)) {
            exit('Sellist battletagi ei eksisteeri: ' . $input_battle_tag);
        }

        preg_match("/(.*?)(?=[-])/", $input_battle_tag, $name);

        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Insert API data to db.
        $overall = array(
            'battle_tag' => $input_battle_tag,
            'name' => $name['0'],
            'lvl' => ($overall_stats->prestige * 100 + $overall_stats->level),
            'avatar' => $overall_stats->avatar,
            'rank' => $overall_stats->comprank,
            'tier' => $overall_stats->tier,
            'wins' => $overall_stats->wins,
            'lost' => $overall_stats->losses,
            'ties' => $overall_stats->ties,
            'played' => $overall_stats->games,
            'last_updated' => date('Y-m-d H:i:s', current_time('timestamp', 0)),
            'ip_address' => $ip_address
        );

        $insert_overall = $wpdb->insert('wp_ranking', $overall);
        $battle_tag_id = $wpdb->insert_id;

        if (!empty($battle_tag_id)) {

            $obj_time = gmdate('H:i:s', floor($average_stats->objective_time_avg * 3600));
            $timeonfire = gmdate('H:i:s', floor($average_stats->time_spent_on_fire_avg * 3600));


            // Insert API user stats to db.
            $player_stats = array(
                'battle_tag_id' => $battle_tag_id,
                'melee_final_blows' => $average_stats->melee_final_blows_avg,
                'time_spent_on_fire' => $timeonfire,
                'solo_kills' => $average_stats->solo_kills_avg,
                'objective_time' => $obj_time,
                'objective_kills' => $average_stats->objective_kills_avg,
                'healing_done' => $average_stats->healing_done_avg,
                'final_blows' => $average_stats->final_blows_avg,
                'deaths' => $average_stats->deaths_avg,
                'damage_done' => $average_stats->damage_done_avg,
                'eliminations' => $average_stats->eliminations_avg
            );

            $insert_average_stats = $wpdb->insert('wp_average_stats', $player_stats);

            $player_medals = array(
                'battle_tag_id' => $battle_tag_id,
                'gold' => $game_stats->medals_gold,
                'silver' => $game_stats->medals_silver,
                'bronze' => $game_stats->medals_bronze,
            );

            $insert_medals = $wpdb->insert('wp_medals', $player_medals);

            // Add heroes playtime to database
            $all_heroes_playtime = $parsed_to_array['eu']['heroes']['playtime']['competitive'];

            foreach ($all_heroes_playtime as $hero_name => $playtime) {
                $insert_heroes = $wpdb->insert('wp_heroes', ['hero_name' => $hero_name, 'playtime' => $playtime, 'battle_tag_id' => $battle_tag_id]);
            }

            // Add heroes stats to database
            $all_heroes_stats = $parsed_to_array['eu']['heroes']['stats']['competitive'];
            foreach ($all_heroes_stats as $hero_name => $hero_stats) {
                $data = array(
                    'battle_tag_id' => $battle_tag_id,
                    'hero_name' => $hero_name,
                    'objective_time_average' => $hero_stats['average_stats']['objective_time_average'],
                    'objective_kills_average' => $hero_stats['average_stats']['objective_kills_average'],
                    'deaths_average' => $hero_stats['average_stats']['deaths_average'],
                    'eliminations_average' => $hero_stats['average_stats']['eliminations_average'],
                    'final_blows_average' => $hero_stats['average_stats']['final_blows_average'],
                    'damage_done_average' => $hero_stats['average_stats']['damage_done_average'],
                    'healing_done_average' => $hero_stats['average_stats']['healing_done_average'],
                    'solo_kills_average' => $hero_stats['average_stats']['solo_kills_average'],
                    'weapon_accuracy' => $hero_stats['general_stats']['weapon_accuracy'],
                    'eliminations_per_life' => $hero_stats['general_stats']['eliminations_per_life'],
                    'games_played' => $hero_stats['general_stats']['games_played'],
                    'games_won' => $hero_stats['general_stats']['games_won'],
                    'games_lost' => $hero_stats['general_stats']['games_lost'],
                );
                $insert_heroes = $wpdb->insert('wp_hero_avg_stats', $data);
            }
            exit('Ok');
        } else {
            echo 'Andmeid ei saa sisestada';
        }

    } else {
        echo 'Ok';
    }

    die();
}

add_action('wp_ajax_battletag_from_api_to_database', 'battletagApiToDatabase');
add_action('wp_ajax_nopriv_battletag_from_api_to_database', 'battletagApiToDatabase');

function updateProfile()
{
    global $wpdb;

    $battle_tag = $_POST['data']['battle_tag'];
    $battle_tag_id = $_POST['data']['battle_tag_id'];
    $encoded_battletag = urlencode($battle_tag);

    $result = $wpdb->get_row("SELECT * FROM wp_ranking where battle_tag = '$battle_tag'");

    if(!empty($result->team_id)) {
        $team_id = $result->team_id;
    }

    if (empty($result)) {
        exit('Battletag not found!');
    }

    $options = array('http' => array('user_agent' => 'custom user agent string'));
    $context = stream_context_create($options);
    $response = @file_get_contents("https://owapi.net/api/v3/u/$encoded_battletag/blob", false, $context);
    $parsed_json = json_decode($response);

    $overall_stats = $parsed_json->eu->stats->competitive->overall_stats;
    $average_stats = $parsed_json->eu->stats->competitive->average_stats;
    $game_stats = $parsed_json->eu->stats->competitive->game_stats;

    $parsed_to_array = json_decode($response, true);

    // Check if battletag exists in API
    $check = $parsed_json->eu;
    if (empty($check)) {
        exit('Battletag not found on API');
    }

    $delete_user = $wpdb->query("DELETE FROM wp_ranking where battle_tag = '$battle_tag'");
    $delete_hero = $wpdb->query("DELETE FROM wp_heroes where battle_tag_id = $battle_tag_id");
    $delete_medals = $wpdb->query("DELETE FROM wp_medals where battle_tag_id = $battle_tag_id");
    $delete_hero_data = $wpdb->query("DELETE FROM wp_hero_avg_stats where battle_tag_id = $battle_tag_id");

    preg_match("/(.*?)(?=[-])/", $battle_tag, $name);

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }


// Insert API data to db.
    $overall = array(
        'battle_tag' => $battle_tag,
        'name' => $name['0'],
        'lvl' => ($overall_stats->prestige * 100 + $overall_stats->level),
        'avatar' => $overall_stats->avatar,
        'rank' => $overall_stats->comprank,
        'tier' => $overall_stats->tier,
        'wins' => $overall_stats->wins,
        'lost' => $overall_stats->losses,
        'ties' => $overall_stats->ties,
        'played' => $overall_stats->games,
        'last_updated' => date('Y-m-d H:i:s', current_time('timestamp', 0)),
        'ip_address' => $ip,
        'team_id' => $team_id
    );

    $insert_overall = $wpdb->insert('wp_ranking', $overall);
    $battle_tag_id = $wpdb->insert_id;

    $obj_time = gmdate('H:i:s', floor($average_stats->objective_time_avg * 3600));
    $timeonfire = gmdate('H:i:s', floor($average_stats->time_spent_on_fire_avg * 3600));

// Insert API user stats to db.
    $player_stats = array(
        'battle_tag_id' => $battle_tag_id,
        'melee_final_blows' => $average_stats->melee_final_blows_avg,
        'time_spent_on_fire' => $timeonfire,
        'solo_kills' => $average_stats->solo_kills_avg,
        'objective_time' => $obj_time,
        'objective_kills' => $average_stats->objective_kills_avg,
        'healing_done' => $average_stats->healing_done_avg,
        'final_blows' => $average_stats->final_blows_avg,
        'deaths' => $average_stats->deaths_avg,
        'damage_done' => $average_stats->damage_done_avg,
        'eliminations' => $average_stats->eliminations_avg
    );

    $insert_average_stats = $wpdb->insert('wp_average_stats', $player_stats);

    $player_medals = array(
        'battle_tag_id' => $battle_tag_id,
        'gold' => $game_stats->medals_gold,
        'silver' => $game_stats->medals_silver,
        'bronze' => $game_stats->medals_bronze,
    );

    $insert_medals = $wpdb->insert('wp_medals', $player_medals);

// Add heroes playtime to database
    $all_heroes_playtime = $parsed_to_array['eu']['heroes']['playtime']['competitive'];

    foreach ($all_heroes_playtime as $hero_name => $playtime) {
        $insert_heroes = $wpdb->insert('wp_heroes', ['hero_name' => $hero_name, 'playtime' => $playtime, 'battle_tag_id' => $battle_tag_id]);
    }

// Add heroes stats to database
    $all_heroes_stats = $parsed_to_array['eu']['heroes']['stats']['competitive'];

    foreach ($all_heroes_stats as $hero_name => $hero_stats) {
        $data = array(
            'battle_tag_id' => $battle_tag_id,
            'hero_name' => $hero_name,
            'objective_time_average' => $hero_stats['average_stats']['objective_time_average'],
            'objective_kills_average' => $hero_stats['average_stats']['objective_kills_average'],
            'deaths_average' => $hero_stats['average_stats']['deaths_average'],
            'eliminations_average' => $hero_stats['average_stats']['eliminations_average'],
            'final_blows_average' => $hero_stats['average_stats']['final_blows_average'],
            'damage_done_average' => $hero_stats['average_stats']['damage_done_average'],
            'healing_done_average' => $hero_stats['average_stats']['healing_done_average'],
            'solo_kills_average' => $hero_stats['average_stats']['solo_kills_average'],
            'weapon_accuracy' => $hero_stats['general_stats']['weapon_accuracy'],
            'eliminations_per_life' => $hero_stats['general_stats']['eliminations_per_life'],
            'games_played' => $hero_stats['general_stats']['games_played'],
            'games_won' => $hero_stats['general_stats']['games_won'],
            'games_lost' => $hero_stats['general_stats']['games_lost'],
        );
        $insert_heroes = $wpdb->insert('wp_hero_avg_stats', $data);
    }

    exit('Ok');
}

add_action('wp_ajax_update_profile', 'updateProfile');
add_action('wp_ajax_nopriv_update_profile', 'updateProfile');


function addTournament()
{
    global $wpdb;

    $data = array(
        'name' => $_POST['data']['tournament_name'],
        'link' => $_POST['data']['tournament_link'],
        'date' => date("Y-m-d"),
    );
    $insert_tournament = $wpdb->insert('wp_tournaments', $data);

    exit('Ok');
}

add_action('wp_ajax_add_new_tournament', 'addTournament');

function addPlayerToTeam(){

    global $wpdb;

    $result = $wpdb->update('wp_ranking', array('team_id' => $_POST['data']['team_id']), array( 'battle_tag' => $_POST['data']['battletag'] ));

    if($result == '1'){
        exit('Ok');
    } else {
        exit('error');
    }
}

add_action('wp_ajax_add_player_to_team', 'addPlayerToTeam');


function removePlayerFromTeam() {
    global $wpdb;

    $result = $wpdb->update('wp_ranking', array('team_id' => null), array( 'battle_tag_id' => $_POST['battletag_id'] ));

    if($result == '1'){
        exit('Ok');
    } else {
        exit('error');
    }
}

add_action('wp_ajax_remove_player_from_team', 'removePlayerFromTeam');

function addNewTeam() {

    global $wpdb;

    $team = array(
        'team_name' => $_POST['data']['team_name'],
        'team_logo' => $_POST['data']['team_logo'],
    );

    $result = $wpdb->insert('wp_teams', $team);

    if($result == '1'){
        exit('Ok');
    } else {
        exit('error');
    }
}

add_action('wp_ajax_add_new_team', 'addNewTeam');

