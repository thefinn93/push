<?
session_start();
if(isset($_SESSION['user'])) {
    if(isset($_SESSION['continue_after_signin'])) {
        header("Location: ".$_SESSION['continue_after_signin']);
        unset($_SESSION['continue_after_signin']);
    } else {
        header("Location: home");
    }
}
?><html>
<head>
<title>sign in</title>
</head>
<body>
Pick your favorite oauth service, I'm too lazy to make a login system just now:<br/>
<!--<a href="signup/google">Google</a> <a href="signup/twitter">Twitter</a> <a href="signup/uw">UW</a>-->
<a href="oauth/reddit">reddit</a>

</body>
</html>
