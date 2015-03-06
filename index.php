<!DOCTYPE HTML>
<?php 
    include("inc/doenerbank.php");
	$loggedIn = false;
	$bestellung = null;
	if(isset($_SESSION['user_id'])){
		$user = new user($_SESSION['user_id']);
		$loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
                        $bestellung = new bestellung();
                        $_SESSION['best_id'] = $bestellung->getBestId();
                        varDump($_SESSION);
		}
	}
	
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $user = new user();
        $loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
			$_SESSION['user_id'] = $user->getID();
                        $bestellung = new bestellung();
                        $_SESSION['best_id'] = $bestellung->getBestId();
                        varDump($_SESSION);
		}
    }

	
	
    if(isset($_GET["view"])){
        $view = $_GET["view"];
        switch($view){
            case "order": $view = "order"; break;
            case "admin": $view = "admin"; break;
            case "uebersicht": $view = "uebersicht"; break;
            default: $view = "order";
        }
    }else{
        $view = "order";
    }
    $adminHint = "";
    if($loggedIn && $user !== NULL){
		if($view == "admin"){
			if(!$user->isAdmin()){
                            $adminHint = '
        <div class="alert alert-error">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Achtung!</strong> Sie sind nicht als Administrator angemeldet.
        </div>';
				$view = "order";
			}
		}
	}
?>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="author" content="Steffen Pfeil - ITFU1" />
	<title>Dönerbank - Fast Food Bestellsystem</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.png" type="image/png" />
    <?php echo $header ?>
</head>

<body>
    
    <div id="triggerling" class="hidden">&nbsp;</div>
    <div class="html-mobile-background"></div>
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
           
          <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
					
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
		
     
          <!-- Be sure to leave the brand out there if you want it shown -->
          <a class="brand" href="index.php?view=order">D&ouml;nerbank</a>
		  <div class="warenkorb-container visible-phone">
		  <span id="warenkorb-icon-container" class="warenkorb-icon-container"></span>
		  <div id="warenkorb-vorschau" class="warenkorb-vorschau"></div>
		</div>
          <!-- Everything you want hidden at 940px or less, place within here -->
          <div class="nav-collapse collapse">
            <!-- .nav, .navbar-search, .navbar-form, etc -->
		<ul class="nav">
            <li><a href="index.php?view=order">Bestellen</a></li>
            <li><a href="index.php?view=admin">Administration</a></li>
            <li> <button class="btn btn-danger" id="logout">Logout</button></li>
          </ul> 
            <div class="span3 pull-right clearfix">
				<div class="row-fluid">
					<div class="greeting span10 visible-desktop">
						<p> <?php if($loggedIn){echo "Willkommen ".$user->getName();} ?></p>
					</div>
                     
					<div class="warenkorb-container span2 visible-desktop">
						  <span id="warenkorb-icon-container" class="warenkorb-icon-container"></span>
						  <div id="warenkorb-vorschau" class="warenkorb-vorschau"></div>
					</div>
				</div>
            </div>
          </div>
        </div>
      </div>
    </div>
	<div class="alert alert-error" id="bestellung_geschlossen" style="display: none;">
		<p class="text-center"><strong>Achtung!</strong> Die Bestellung ist schon geschlossen. Entweder ist schon bestellt, oder deine Bestellung wurde schon gespeichert.</p>
        </div>
    <div class="container maincontent">
	<?php echo $adminHint; ?>
    <?php if($loggedIn == false):?>
            <div id="login_window">
                <form method="post">
                    <label>Benutzername</label>
                    <div class="input-prepend">
                      <span class="add-on"><i class="icon-user"></i></span>
                      <input class="span2" id="username" type="text" placeholder="Username" name="username">
                    </div>
                    <label>Passwort</label>
                    <div class="input-prepend">
                      <span class="add-on"><i class="icon-lock"></i></span>
                      <input class="span2" id="password" type="password" placeholder="Passwort" name="password">
                    </div>    
                    <button type="submit" id="login_btn" disabled="true" class="login_btn btn btn-primary">Login</button>              
                    <button id="register_btn"  class="register_btn btn btn-secondary">Register</button>              
                </form>
            </div>
        <script>LOGIN();</script>
    <?php else: ?>        
            <?php if($view == "order"):?>
                <h1>Ein Gericht bestellen</h1>
                <div class="container" id="artikelliste"></div>
                <script>Artikel.render($('#artikelliste'));</script>
            <?php endif ?>
            <?php if($view == "admin"):?>
                <h1>Administration</h1>
                
                <div class="container-fluid" id="administration">
                <h3>Benutzer</h3>
					<div id="administration_nutzer" class="container-fluid"></div>
                    <h3>Bestellungen</h3>
					<div id="administration_bestellungen" class="container-fluid"></div>
                        <div class="btn-group adminbuttons" role="group">
                            <button id="admin_bestellung_submit" class="btn-info btn">Speichern</button>
                            <button id="admin_bestellung_clear" class="btn-info btn"> Löschen </button>
                            <button id="admin_bestellung_print" class="btn-info btn">Speichern und Drucken</button>
                        </div>
                    <div class="row" id="user"></div>
                    
                    
                    <div class="row" id="bestellungen" class="hidden"></div>
                    <script>administration.init();</script>
                </div>
            <?php endif ?>
            <?php if($view == "uebersicht"):?>
                <h1>Übersicht</h1>
                <div id="bestelluebersicht" class="container-fluid bestelluebersicht"></div>
				<div class='row-fluid' id='uebersicht_preis'>
                    <p class='span3 offset7 gesamtpreis_uebersicht'> <span id='gesamtpreis_uebersicht'>0.00</span>€ </p>
                </div>
                
                <div class='row-fluid text-right' id='uebersicht_kontrolle'>
                    <button class='btn btn-inverse' id='uebersicht_liste_leeren'>Abbrechen</button>
                    <button class='btn btn-primary' id='uebersicht_bestaetigen'>Bestätigen</button>
                </div>
                <script>uebersicht.init();</script>
            <?php endif ?>
                <script>MAIN();</script>
    <?php endif ?>

    </div>
</body>
</html>
