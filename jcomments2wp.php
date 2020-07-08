<?php
ini_set("display_errors", 1);
$dbhost          = "localhost";
$dbusername      = "root";
$dbpassword      = "root";
$database_joomla = "myjoomla25db";
$jtable_prefix   = "j25_"; //Joomla Table Prefix
$mysqli          = new mysqli($dbhost, $dbusername, $dbpassword, $database_joomla); //Connection for Joomla DB


$database_wordpress = "wordpress";
$wptable_prefix     = "wp_"; //Wordpress Table Prefix
$mysqli_wp          = new mysqli($dbhost, $dbusername, $dbpassword, $database_wordpress); //Connection for Wordpress DB


$print_query = false; //Set to true if you want to print the sql queries on screen

// Change character set to utf8
$mysqli->set_charset("utf8");
$mysqli_wp->set_charset("utf8");
echo "<br /> Joomla Character Encoding " . $mysqli->character_set_name();

echo "<br /> Wordpress Character Encoding " . $mysqli_wp->character_set_name();

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}



$query = "SELECT DISTINCT object_id FROM {$jtable_prefix}jcomments";
if ($print_query) {
    echo $query . "\n";
}
$results = $mysqli->query($query);

$pids = array();
while ($row = $results->fetch_assoc()) {
    $pids[] = $row['object_id'];
}
$get_total_rows = count($pids);

if (!$pids) {
    echo "There is No comments in {$jtable_prefix}jcomments table";
}

$num = $get_total_rows;
$i   = 0;
for ($i = 0; $i < $get_total_rows; $i++) {
    $pid   = $pids[$i];
    $query = "SELECT created FROM  {$jtable_prefix}content WHERE id = " . $pid;
    
    if ($print_query) {
        echo $query . "\n";
    }
    
    
    $created = $mysqli->query($query);
    if (!$created) {
        echo "Record Not created...\n";
    }
    
    $ct = $created->fetch_array(MYSQLI_ASSOC);
    
    $query = "SELECT ID FROM  " . $database_wordpress . ".{$wptable_prefix}posts WHERE  post_date =  '" . $ct['created'] . "' AND post_type =  'post'";
    if ($print_query) {
        echo $query . "\n";
    }
    
    $wpids = $mysqli_wp->query($query);
    if (!$wpids) {
        echo "wpids Error! \n";
        continue;
    }
    $wpid  = $wpids->fetch_array(MYSQLI_ASSOC);
    if(!isset($wpid["ID"])) {
        continue;
    }
    $wpid  = $wpid["ID"];
    $query = "SELECT * FROM {$jtable_prefix}jcomments WHERE object_id = " . $pid;
    if ($print_query) {
        echo $query . "\n";
    }
    
    $comments       = $mysqli->query($query);
    $comments_count = $comments->num_rows;
    $j              = 0;
    
    while ($j < $comments_count) {
        $comment = $comments->fetch_array(MYSQLI_ASSOC);
        // print_r($comment);
        $author  = $comment["name"];
        $email   = $comment["email"];
        $url     = $comment["homepage"];
        $ip      = $comment["ip"];
        $cdate   = $comment["date"];
        $content = $comment["comment"];
        //$content = mysqli_real_escape_string($content); //TODO: Find alternative
        $query   = "INSERT INTO " . $database_wordpress . ".{$wptable_prefix}comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content) VALUES (" . $wpid . ", '" . $author . "', '" . $email . "', '" . $url . "', '" . $ip . "', '" . $cdate . "', '" . $cdate . "', '" . $content . "')";
        if ($print_query) {
            echo $query . "\n";
        }
        
        $mysqli_wp->query($query);
        $j++;
    }
    $query = "UPDATE " . $database_wordpress . ".{$wptable_prefix}_posts SET comment_count = " . $comments_count . " WHERE ID = " . $wpid;
    if ($print_query) {
        echo $query . "\n";
    }
    $mysqli_wp->query($query);
    
    $i++;
    
}
