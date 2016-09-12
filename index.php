<?php

include_once 'function.php';

  /*******************************************************
   * Only these origins will be allowed to upload images *
   ******************************************************/
  $accepted_origins = array("http://localhost", "http://192.168.0.117", "http://example.com");

  /*********************************************
   * Change this line to set the upload folder *
   *********************************************/
  $imageFolder = "img/";

  reset ($_FILES);
  $temp = current($_FILES);
  if (is_uploaded_file($temp['tmp_name'])){
    if (isset($_SERVER['HTTP_ORIGIN'])) {
      // same-origin requests won't set an origin. If the origin is set, it must be valid.
      if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      } else {
        header("HTTP/1.0 403 Origin Denied");
        return;
      }
    }

    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.0 500 Invalid file name.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
        header("HTTP/1.0 500 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder . $temp['name'];
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $filetowrite));
  } else {
    // Notify editor that the upload failed
    header("HTTP/1.0 500 Server Error");
  }


//Umleitungen
$actual_link = "$_SERVER[REQUEST_URI]";
if(strpos($actual_link, "?search=")) {
	$actual_link = str_replace("?search=", "", $actual_link);
	header("Location: ".$actual_link);
}
if(strpos($actual_link, "?")) {
	$actual_link = str_replace("?", "", $actual_link);
	header("Location: ".$actual_link);
}

// Read URL Options
$KB_ID = $_GET["ID"];
$KB_SearchString = $_GET["sitesearch"];

//Namenszusatz hinzufügen
$SITE_DESTIANATION = checkSiteDestination($_SERVER[REQUEST_URI]);
$TL_SiteName = $TL_SiteName . " - " . $SITE_DESTIANATION;

if(isset($_POST['SubmitButton'])){ //check if form was submitted
	$KB_UPDATE_TEXT = $_POST['KB_Text']; //get input text
	$KB_UPDATE_TITLE = $_POST['KB_Title']; //get input text
	$KB_UPDATE_TEXT = htmlspecialchars($KB_UPDATE_TEXT);
	$KB_UPDATE_TITLE = htmlspecialchars($KB_UPDATE_TITLE);
	$KB_UPDATE_TEXT = $db->real_escape_string($KB_UPDATE_TEXT);
	$KB_UPDATE_TITLE = $db->real_escape_string($KB_UPDATE_TITLE);
	echo $result;
	$result = sqlQuery("INSERT INTO KB_ARTICLE_" . $KB_ID . " (KB_ARTICLE_ID, KB_ARTICLE_TEXT_DE, KB_ARTICLE_TITLE_DE) VALUES('" . $KB_ID . "', '".$KB_UPDATE_TEXT."','".$KB_UPDATE_TITLE."');");
}

// Last Update
$result = sqlQuery("SELECT * FROM `KnowledgeBase` ORDER BY KB_LAST_UPDATE_TIME DESC LIMIT 4");
while($row = $result->fetch_assoc()){
	$KB_EDIT_ARTICLE = $KB_EDIT_ARTICLE . "<li><a href=/kb/" . htmlspecialchars_decode($row['KB_ID']) . ">" . htmlspecialchars_decode($row['KB_TITLE']) . "</a></li>";
}

for ($i = 4; $i >= 1; $i--) {
	$sqlQueryTest = "SELECT * FROM KB_ARTICLE_".$i." WHERE KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) AS KB_ARTICLE_SEARCH_MAX_ID FROM KB_ARTICLE_".$i." WHERE KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$i."));";
	$result = sqlQuery($sqlQueryTest);
	while($row = $result->fetch_assoc()){
		$KB_NEW_ARTICLE = $KB_NEW_ARTICLE . "<li><a href=/kb/" . htmlspecialchars_decode($row['KB_ARTICLE_ID']) . ">" . htmlspecialchars_decode($row['KB_ARTICLE_TITLE_DE']) . "</a></li>";
	}
}


switch ($SITE_DESTIANATION) {
    case "KB":
		If(!$KB_ID == "") {		
			//Current
			$result = sqlQuery("
					SELECT 
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_ID,
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TITLE_DE,
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TIMESTAMP,
					KB_USER.KB_LAST_NAME,
					KB_USER.KB_FIRST_NAME,
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_DE,
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_EN
					FROM KB_ARTICLE_".$KB_ID."
					INNER JOIN
					KB_USER ON KB_USER_ID = KB_ARTICLE_".$KB_ID.".KB_ARTICLE_UPDATE_AUTHOR 
					WHERE KB_ARTICLE_".$KB_ID.".KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$KB_ID.");"
				);
			while($row = $result->fetch_assoc()){
				$KB_TITLE = htmlspecialchars_decode($row['KB_ARTICLE_TITLE_DE']);
				$KB_TEXT =  htmlspecialchars_decode($row['KB_ARTICLE_TEXT_DE']);
				$KB_TEXT_EN =  htmlspecialchars_decode($row['KB_ARTICLE_TEXT_EN']);
				$KB_LAST_UPDATE_TIME =  htmlspecialchars_decode($row['KB_ARTICLE_TIMESTAMP']);
				$KB_CREATION_TIME =  htmlspecialchars_decode($row['KB_ARTICLE_TIMESTAMP']);
				$KB_UPDATE_AUTHOR = htmlspecialchars_decode($row['KB_LAST_NAME']).", ".htmlspecialchars_decode($row['KB_FIRST_NAME']);
			}
			$result = sqlQuery("
					SELECT 
					KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TIMESTAMP,
					KB_USER.KB_LAST_NAME,
					KB_USER.KB_FIRST_NAME
					FROM KB_ARTICLE_".$KB_ID."
					INNER JOIN
					KB_USER ON KB_USER_ID = KB_ARTICLE_".$KB_ID.".KB_ARTICLE_UPDATE_AUTHOR 
					WHERE KB_ARTICLE_".$KB_ID.".KB_ARTICLE_CHILD_ID=1;"
				);
			while($row = $result->fetch_assoc()){
				$KB_CREATION_TIME =  htmlspecialchars_decode($row['KB_ARTICLE_TIMESTAMP']);
				$KB_AUTHOR = htmlspecialchars_decode($row['KB_LAST_NAME']).", ".htmlspecialchars_decode($row['KB_FIRST_NAME']);
			}
			$result->free();
			$AUTHOR_INFORMATION = "
				<table>
					<tr>
						<td>Author:</td>
						<td>".$KB_AUTHOR."</td>
					</tr>
					<tr>
						<td>Creation Time:</td>
						<td>".$KB_CREATION_TIME."</td>
					</tr>					<tr>
					<td>Last edit by:</td>
						<td>".$KB_UPDATE_AUTHOR."</td>
					</tr>
					<tr>
						<td>Last update:</td>
						<td>".$KB_LAST_UPDATE_TIME."</td>
					</tr>
				</table>
				";
			$SITE_ARTICLE = $AUTHOR_INFORMATION . 
			"
			<p>
				<form method='get' action=/kb_edit/".$KB_ID." value='Bearbeiten'>
					<input type='submit' name='' class='button' value='Bearbeiten';'/>
				</form>		
			</p>
			<article>
				<h1>".$KB_TITLE."</h1>
				<p>
					".$KB_TEXT."
				</p>
			</article>";
		}else{
		
			$result = sqlQuery("SELECT COUNT(TABLE_Name) AS KB_ARTICLE_COUNT FROM information_schema.tables WHERE table_schema = 'KnowledgeBase' AND TABLE_NAME LIKE 'KB_ARTICLE_%';");
		
			while($row = $result->fetch_assoc()){
				$KB_ARTICLE_COUNT = htmlspecialchars_decode($row['KB_ARTICLE_COUNT']);
			}
			
			for ($i = 1; $i <= $KB_ARTICLE_COUNT; $i++) {
				$sqlQueryTest = "SELECT * FROM KB_ARTICLE_".$i." WHERE KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) AS KB_ARTICLE_SEARCH_MAX_ID FROM KB_ARTICLE_".$i." WHERE KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$i."));";
				$result = sqlQuery($sqlQueryTest);
				while($row = $result->fetch_assoc()){
					$KB_Found = $KB_Found."<br /><a href='/kb/".htmlspecialchars_decode($row['KB_ARTICLE_ID'])."'>Artikel ".$i.": ".htmlspecialchars_decode($row['KB_ARTICLE_TITLE_DE'])."</a><br />";
				}
			}
		
			$SITE_ARTICLE = "
				<form method='get' action=/kb_new/".$KB_ID." value='Neuer Artikel'>
					<input type='submit' name='' class='button' value='Neuer Artikel';'/>
				</form>
				".$KB_Found."
			";
		}
			break;
    case "KB - Editor":
			$sqlQueryTest = 
			"
				SELECT 
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_ID,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TITLE_DE,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TIMESTAMP,
				KB_USER.KB_LAST_NAME,
				KB_USER.KB_FIRST_NAME,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_DE,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_EN
				FROM KB_ARTICLE_".$KB_ID."
				INNER JOIN
				KB_USER ON KB_USER_ID = KB_ARTICLE_".$KB_ID.".KB_ARTICLE_UPDATE_AUTHOR 
				WHERE KB_ARTICLE_".$KB_ID.".KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$KB_ID.");"
				;
			$result = sqlQuery("
				SELECT 
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_ID,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TITLE_DE,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TIMESTAMP,
				KB_USER.KB_LAST_NAME,
				KB_USER.KB_FIRST_NAME,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_DE,
				KB_ARTICLE_".$KB_ID.".KB_ARTICLE_TEXT_EN
				FROM KB_ARTICLE_".$KB_ID."
				INNER JOIN
				KB_USER ON KB_USER_ID = KB_ARTICLE_".$KB_ID.".KB_ARTICLE_UPDATE_AUTHOR 
				WHERE KB_ARTICLE_".$KB_ID.".KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$KB_ID.");"
			);
			while($row = $result->fetch_assoc()){
				$KB_TITLE = htmlspecialchars_decode($row['KB_ARTICLE_TITLE_DE']);
				$KB_TEXT =  htmlspecialchars_decode($row['KB_ARTICLE_TEXT_DE']);
				$KB_TEXT_EN =  htmlspecialchars_decode($row['KB_ARTICLE_TEXT_EN']);
				$KB_LAST_UPDATE_TIME =  htmlspecialchars_decode($row['KB_ARTICLE_TIMESTAMP']);
				$KB_CREATION_TIME =  htmlspecialchars_decode($row['KB_ARTICLE_TIMESTAMP']);
				$KB_UPDATE_AUTHOR = htmlspecialchars_decode($row['KB_LAST_NAME']).", ".htmlspecialchars_decode($row['KB_FIRST_NAME']);
			}
		$SITE_ARTICLE = 
			"<p>
				<form method='get' action=/kb/".$KB_ID.">
					<input type='submit' name='' class='button' value='Zurück zum Artikel';'/>
				</form>
			</p>
            <article>
			
			
		
			
			
			<form action='' method='post'>
			<input type='submit' name='SubmitButton' class='button' value='Save';'/>
			<input type='text' id='KB_Title' name='KB_Title' maxlength='50' value='".$KB_TITLE."' style='text-weight:bold;'>
			<p>
			<textarea id='KB_Textarea' name='KB_Text'>".$KB_TEXT."</textarea>
			</p>
			</form>";
        break;
	case "KB - Search":
		$sitesearch = $_GET["sitesearch"];
		$sitesearch = "%".str_replace(' ', ',', $sitesearch)."%";
		
		$result = sqlQuery("SELECT COUNT(TABLE_Name) AS KB_ARTICLE_COUNT FROM information_schema.tables WHERE table_schema = 'KnowledgeBase' AND TABLE_NAME LIKE 'KB_ARTICLE_%';");
		
		while($row = $result->fetch_assoc()){
			$KB_ARTICLE_COUNT = htmlspecialchars_decode($row['KB_ARTICLE_COUNT']);
		}
		
		for ($i = 1; $i <= $KB_ARTICLE_COUNT; $i++) {
			$sqlQueryTest = "SELECT * FROM KB_ARTICLE_".$i." WHERE KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) AS KB_ARTICLE_SEARCH_MAX_ID FROM KB_ARTICLE_".$i." WHERE MATCH (KB_ARTICLE_TITLE_DE,KB_ARTICLE_TEXT_DE) AGAINST ('".$sitesearch."' IN NATURAL LANGUAGE MODE) AND KB_ARTICLE_CHILD_ID=(SELECT MAX(KB_ARTICLE_CHILD_ID) FROM KB_ARTICLE_".$i."));";
			$result = sqlQuery($sqlQueryTest);
			while($row = $result->fetch_assoc()){
				$KB_Found = $KB_Found."<li><a href='/kb/".htmlspecialchars_decode($row['KB_ARTICLE_ID'])."'>".htmlspecialchars_decode($row['KB_ARTICLE_TITLE_DE'])."</a></li>";
			}
		}
		
		//$result = sqlQuery("SELECT * FROM KB_ARTICLE_1 WHERE MATCH (KB_ARTICLE_TITLE_DE,KB_ARTICLE_TEXT_DE) AGAINST ('".$sitesearch."' IN NATURAL LANGUAGE MODE)");
		$SITE_ARTICLE = $KB_Found;
		break;
	case "KB - New":
		$result = sqlQuery("SELECT MAX(KB_ARTICLE_ID) AS MAX_ID FROM KB_ARTICLE");
		while($row = $result->fetch_assoc()){
			$NEXT_ID = htmlspecialchars_decode($row['MAX_ID']) +1;
		}
		$SITE_ARTICLE = $MAX_ID;
		$result = sqlQuery("
			INSERT INTO  KB_ARTICLE (KB_ARTICLE_ID) VALUES ('".$NEXT_ID."');
		");
		$result = sqlQuery("
			CREATE TABLE KB_ARTICLE_". $NEXT_ID ." (
				KB_ARTICLE_ID MEDIUMINT NOT NULL,
				KB_ARTICLE_CHILD_ID MEDIUMINT NOT NULL AUTO_INCREMENT,
				KB_ARTICLE_TITLE_DE char(50),
				KB_ARTICLE_TITLE_EN char(50),
				KB_ARTICLE_TEXT_EN longtext,
				KB_ARTICLE_TEXT_DE longtext,
				KB_ARTICLE_UPDATE_AUTHOR mediumint(9) DEFAULT 1,
				KB_ARTICLE_CHANGE_COMMENT text,
				KB_ARTICLE_TIMESTAMP timestamp default current_timestamp,
				FULLTEXT KB_ARTICLE_FULLTEXT_DE (KB_ARTICLE_TITLE_DE,KB_ARTICLE_TEXT_DE),
				FULLTEXT KB_ARTICLE_FULLTEXT_EN (KB_ARTICLE_TITLE_EN,KB_ARTICLE_TEXT_EN),
				PRIMARY KEY (KB_ARTICLE_CHILD_ID),
				FOREIGN KEY (KB_ARTICLE_ID) REFERENCES KB_ARTICLE(KB_ARTICLE_ID),
				FOREIGN KEY (KB_ARTICLE_UPDATE_AUTHOR) REFERENCES KB_USER(KB_USER_ID) ON UPDATE CASCADE,
				FOREIGN KEY (KB_ARTICLE_ID) REFERENCES KB_ARTICLE(KB_ARTICLE_ID)
			);
		");
		header("Location: /kb/". $NEXT_ID ."");
	default:
        break;
}

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Knowledge Base</title>
        <link rel="stylesheet" href="/style/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="/style/css/style.css" type="text/css" media="screen" />
        <link href="/modules/lightbox/css/lightbox.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans|Baumans' rel='stylesheet' type='text/css'>
        <script src="/modules/js/vendor/modernizr.min.js"></script>
        <script src="/modules/js/vendor/respond.min.js"></script>
        <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script type="text/javascript">window.jQuery || document.write('<script type="text/javascript" src="js\/vendor\/1.7.2.jquery.min.js"><\/script>')</script>
        <script src="/modules/lightbox/js/lightbox.js"></script>
        <script src="/modules/js/vendor/prefixfree.min.js"></script>
        <script src="/modules/js/vendor/jquery.slides.min.js"></script>
        <script src="/modules/js/script.js"></script>
		
		
		
		<script src="/modules/tinymce/tinymce.dev.js"></script>
		<link rel="stylesheet" type="text/css" href="/modules/tinymce/plugins/codesample/css/prism.css">
		<script><?php echo $LOAD_DIRTYSTATE; ?></script>
		<script><?php echo $INIT_TINYMCE; ?>;</script>
		<script><?php echo $LOAD_JS_SLIDER; ?></script>
		<script type='text/javascript'>
		var data = <?php echo json_encode($input); ?>;
		if(data!=null){
			window.location.replace(window.location.protocol + "//" + window.location.host + "" + window.location.pathname);
		}
		</script>
	</head>
	<body>
		<header>
            <div class="toggleMobile">
                <span class="menu1"></span>
                <span class="menu2"></span>
                <span class="menu3"></span>
            </div>
            <div id="mobileMenu">
                <ul>
                    <li><a href="javascript:void(0)">Home</a></li>
                    <li><a href="javascript:void(0)">Porfolio</a></li>
                    <li><a href="javascript:void(0)">About</a></li>
                </ul>
            </div>           
            <h1><?php echo $TL_SiteName; ?></h1>
            <p><?php echo $TL_SiteSubName; ?></p>
			<br />
            <nav>
            	<h2 class="hidden">Our navigation</h2>
                <ul>
                    <li><a href="/">Startseite</a></li>
					<li><a href="/kb/"><?php echo $TL_KnowledgebaseName; ?></a></li>
                    <li><a href="/filemanager/dialog.php">Dateimanager</a></li>
                </ul>
            </nav>
        </header>
        <section id="spacer">  
        	<h2 class="hidden">Suche</h2>          
            <p>Was habe ich schon wieder vergessen?</p>
            <div class="search">
                <form action="/kb_search/">
                    <input type="text" name="search" value=""/>
                    <input type="submit" name="" class="button"  value="Suchen"/>
                </form>
            </div>            
        </section>
        <section id="boxcontent">
        	<h2 class="hidden">Artikel</h2>
			<?php echo $SITE_ARTICLE;?>
            <br class="clear"/>
        </section>
        <footer>
        	<h2 class="hidden">Our footer</h2>
            <section id="copyright">
            	<h3 class="hidden">Copyright notice</h3>
                <div class="wrapper">
                    <div class="social">
                        <a href="https://de-de.facebook.com/timchristoph.lid"><img src="/img/icons/Facebook.png" alt="Facebook - Profil" width="25"/></a>
                        <a href="https://plus.google.com/u/0/+TimChristophLid/"><img src="/img/icons/GooglePlus.png" alt="Google+ Profil" width="25"/></a>
                    </div>
					<?php echo $TL_COPYRIGHT; ?>
                    </div>
            </section>
            <section class="wrapper">
            	<h3 class="hidden">Footer content</h3>
                <article class="column">
                    <h4>Über die Wissensdatenbank</h4>
					Dies hier ist die Wissendatenbank von <br />Tim C. Lid. Sie dient der einfachen und schnellen Verwaltung von Wissen.
                </article>
                <article class="column midlist">
                    <h4>Neue Artikel</h4>
                    <ul>
						<?php echo $KB_NEW_ARTICLE; ?>
                    </ul>
                </article>
        </footer>
	</body>
</html>
