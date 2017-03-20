<?php
/*
Template Name: Update user profile
*/

global $wpdb;

$battle_tag = $_POST['battle_tag'];
$battle_tag_id = $_POST['battle_tag_id'];
$encoded_battletag = urlencode($battle_tag);

$result = $wpdb->get_results("SELECT * FROM wp_ranking where battle_tag = '$battle_tag'");

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
    'ip_address' => $ip
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