<!DOCTYPE HTML>
<?php 
    include("inc/doenerbank.php");
	$loggedIn = false;
	
	if($_SESSION['loggedin']){
		var_dump("alles paletti");
		$user = new user("","", $_SESSION['loggedIn']);
		$loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
		}
	}
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $user = new user($_POST["username"], $_POST["password"]);
        $loggedIn = $user->checkLogin();
		if($loggedIn){
			$_SESSION['loggedin'] = $user->sessionCrypt();
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
    
    $showAdminHint = false;
    if($view == "admin"){
        //if(!user.isAdmin()){
        if(true){
            $showAdminHint = true;
            $view = "order";
        }
    }
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" charset="utf-8"/>
	<meta name="author" content="Steffen Pfeil - ITFU1" />
	<title>ITFU1 - D&ouml;ner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <link href="css/main.css" rel="stylesheet" type="text/css"/>
    <link href="css/login.css" rel="stylesheet" type="text/css"/>
    <link href="css/nav.css" rel="stylesheet" type="text/css"/>
    <script src="js/checkLoginForm.js"></script>
</head>

<body>
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
          <ul class="nav">
            <li><a href="index.php?view=order">Bestellen</a></li>
            <li><a href="index.php?view=admin">Administration</a></li>
            <li><a>Logout</a></li>
          </ul>  
     
          <!-- Everything you want hidden at 940px or less, place within here -->
          <div class="nav-collapse collapse">
            <!-- .nav, .navbar-search, .navbar-form, etc -->

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
                    <button type="button" id="register_btn" disabled="true" class="login_btn btn btn-secondary">Register</button>              
                </form>
            </div>
    <?php else: ?>        
            <?php if($view == "order"):?>
                
                <h1>Ein Gericht bestellen</h1>
                
            <?php endif ?>
            <?php if($view == "admin"):?>
            
                <h1>Administration</h1>
            
            <?php endif ?>
            
    <?php endif ?>

    </div>

</body>
</html>