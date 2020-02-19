<?php

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$database = "hi2rashi_thengapatndb";

$jtable_prefix = "j25_"; //Joomla Table Prefix

$mysqli = new mysqli($dbhost, $dbusername, $dbpassword, $database);
// Change character set to utf8
$mysqli->set_charset("utf8");

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}



$query = "SELECT DISTINCT object_id FROM {$jtable_prefix}jcomments";
echo $query;
print "
";
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
$i = 0;

while ($i < $num) {
    $pid = mysql_result($pids, $i, "object_id");
    $query = "SELECT created FROM  {$jtable_prefix}content WHERE id = " . $pid;
    echo $query;
    print "
    ";
    $created = mysql_query($query);
    if (!$created) {
        echo mysql_error();
    }
    $ct = mysql_result($created, 0, "created");
    $query = "SELECT ID FROM  wp_posts WHERE  post_date =  '" . $ct . "' AND post_type =  'post'";
    echo $query;
    print "
    ";
    $wpids = mysql_query($query);
    if (!$wpids) {
        echo mysql_error();
    }
    $wpid = mysql_result($wpids, 0, "ID");
    $query = "SELECT * FROM {$jtable_prefix}jcomments WHERE object_id = " . $pid;
    echo $query;
    print "
    ";
    $comments = mysql_query($query);
    $comments_count = mysql_numrows($comments);
    $j = 0;
    while ($j < $comments_count) {
        $author = mysql_result($comments, $j, "name");
        $email = mysql_result($comments, $j, "email");
        $url = mysql_result($comments, $j, "homepage");
        $ip = mysql_result($comments, $j, "ip");
        $cdate = mysql_result($comments, $j, "date");
        $content = mysql_result($comments, $j, "comment");
        $content = mysql_real_escape_string($content);

        $query = "INSERT INTO wp_comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content) VALUES (" . $wpid . ", '" . $author . "', '" . $email . "', '" . $url . "', '" . $ip . "', '" . $cdate . "', '" . $date . "', '" . $content . "')";
        echo $query;
        print "
    ";
        mysql_query($query);
        $j++;
    }
    $query = "UPDATE wp_posts SET comment_count = " . $comments_count . " WHERE ID = " . $wpid;
    echo $query;
    print "
    ";
    mysql_query($query);

    $i++;
}
