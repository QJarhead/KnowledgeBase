<?php


$POST = '

<?php
$TL_SiteName = "'.$_REQUEST["sitename"].'";
$TL_SiteSubName = "'.$_REQUEST["sitesubname"].'";
$TL_KnowledgebaseName = "'.$_REQUEST["knowledgebasename"].'";
$db = new mysqli("'.$_REQUEST["host"].'","'.$_REQUEST["user"].'","'.$_REQUEST["password"].'","'.$_REQUEST["database"].'");
$KB_DATABASE = new mysqli("'.$_REQUEST["host"].'","'.$_REQUEST["user"].'","'.$_REQUEST["password"].'","'.$_REQUEST["database"].'");
$TL_COPYRIGHT = "'.$_REQUEST["copyright"].'";
?>


';

/*file_put_contents ( "config/config.php" , $POST ); */

include_once 'function.php';
include_once 'config/config.php';

if(!checkDatabase($KB_DATABASE)) {

echo "Keine Verbindung möglich";

}

/*$actual_link = "$_SERVER[HTTP_HOST]";
header("Location: http://".$actual_link);
exit();
*/

?>

<!DOCTYPE html>
<html>
<body>

<p>Click the button to display a confirm box.</p>

<script>
            myfunction();
</script>

<p id="demo"></p>

<script>
function myFunction() {
    var txt;
    if (<?php echo !checkDatabase($KB_DATABASE) ?>) {
	confirm("Es kann keine Verbindung mit MySQL hergestellt werden, dennoch konfigurieren?");
    } else {
        txt = "You pressed Cancel!";
    }
}
window.onload = myFunction;
</script>

</body>
</html>

