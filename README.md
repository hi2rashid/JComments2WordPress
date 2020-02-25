JComments2WordPress v2
===================

Migrate JComments from Joomla! to WordPress.

How to use this

Download and move the file `jcomments2wp.php` to your Joomla server (or wordpress server)
Fill in the following DB config values (line 3 to line 13)

```
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$database_joomla = "myjoomla25db";
$jtable_prefix = "j25_"; //Joomla Table Prefix
```

If you are using different Database Host and user name, make sure to fill below also
```
$database_wordpress = "wordpress";
$wptable_prefix = "wp_"; //Wordpress Table Prefix
$mysqli_wp = new mysqli($dbhost, $dbusername, $dbpassword, $database_wordpress); //Connection for Wordpress DB
```

Joomla DB Prefix should be changed as per your DB in this variable `$jtable_prefix`
Wordpress DB Prefix should be changed as per your DB in this variable `$wptable_prefix`


If you have utf8 characters in the Joomla comments just leave following lines as it is
```
// Change character set to utf8
$mysqli->set_charset("utf8");
$mysqli_wp->set_charset("utf8");
```
