<!DOCTYPE HTML>
<?php 
    include("inc/doenerbank.php");
	$loggedIn = false;
	
	if(isset($_SESSION['user_id'])){
		$user = new user($_SESSION['user_id']);
		$loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
                        $bestellung = new bestellung($_SESSION['best_id']);
		}
	}
	
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $user = new user();
        $loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
			$_SESSION['user_id'] = $user->getID();
                        $bestellung = new bestellung();
		}
    }

	
	
    if(isset($_GET["view"])){
        $view = $_GET["view"];
        switch($view){
            case "order": $view = "order"; break;
            case "admin": $view = "admin"; break;
            default: $view = "order";
        }
    }else{
        $view = "order";
    }
    if($loggedIn && $user !== NULL){
    $showAdminHint = false;
		if($view == "admin"){
			if(!$user->isAdmin()){
				$showAdminHint = true;
				$view = "order";
			}
		}
	} else {
		$showAdminHint = true;
	}
?>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="author" content="Steffen Pfeil - ITFU1" />
	<title>ITFU1 - Döner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
		  
          <!-- Everything you want hidden at 940px or less, place within here -->
          <div class="nav-collapse collapse">
            <!-- .nav, .navbar-search, .navbar-form, etc -->
		<ul class="nav">
            <li><a href="index.php?view=order">Bestellen</a></li>
            <li><a href="index.php?view=admin">Administration</a></li>
            <li>  
                                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                Logout <span class="caret"></span>
                          </button>
                                <ul class="dropdown-menu" role="menu">
                                        <li>Wirklich ausloggen?</li>
                                        <li><a href="logout.php">Ja</a></li>
                                        <li><a href="#" class="onclick_false">Nein</a></li>
                                </ul>
                        </li>
          </ul>  
		<span class="pull-right clearfix text-right text-success"><?php if($loggedIn){echo "Willkommen ".$user->getName();} ?></span>
                  <div class="pull-right clearfix warenkorb-container">
                      <span id="warenkorb-icon-container" class="warenkorb-icon-container"></span>
                      <div id="warenkorb-vorschau" class="warenkorb-vorschau"></div>
                  </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
    <?php if($showAdminHint):?>
        <div class="alert alert-error">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Achtung!</strong> Sie sind nicht als Administrator angemeldet.
        </div>
    <?php endif?>
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
                    <button id="register_btn" disabled="true" class="register_btn login_btn btn btn-secondary">Register</button>              
                </form>
            </div>
    <?php else: ?>        
            <?php if($view == "order"):?>
                <h1>Ein Gericht bestellen</h1>
                <div class="container-fluid" id="artikelliste"></div>
                <script>Artikel.render($('#artikelliste'));</script>
            <?php endif ?>
            <?php if($view == "admin"):?>
                <h1>Administration</h1>
                <div class="container-fluid" id="administration">
                    <div class="row control">
                        <div class="btn-group" role="group">
                            <button id="submit" class="btn-info">Speichern</button>
                            <button id="clear" class="btn-info"> Löschen </button>
                            <button id="print" class="btn-info">Speichern und Drucken</button>
                        </div>
                    </div>
                    <div class="row" id="aktive_user"></div>
                    <div class="row" id="bestellungen" class="hidden"></div>
                </div>
            <?php endif ?>
            <?php if($view == "uebersicht"):?>
            
                <h1>Übersicht</h1>
                
            <?php endif ?>
            
    <?php endif ?>

    </div>

</body>
</html>
