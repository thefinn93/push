<?
session_start();
if(isset($_SESSION['user'])) {
    header("Location: home");
    }
?><html>
<body>
    Like NotifyMyAndroid, but completely free/libre and open source. To begin, <a href="signin">sign in</a>.
</body>
</html>
