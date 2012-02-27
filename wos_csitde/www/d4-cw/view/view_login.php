<?
	$userName = $_REQUEST['uname'];
	$passWord = $_REQUEST['pword'];
	
	
	// login
	if( $mode == "login")
	{
		//echo $userName . $passWord;
		$loginObj = MyActiveRecord::FindBySql('login', 'SELECT * FROM login WHERE referred_as="'.$userName.'" AND password="'.$passWord. '"');
		//echo print_r($loginObj);
		
		if ($loginObj == false)
		{
			// user not found
			//echo "<p>Login is not valid</p>";
			?>
			<script type="text/javascript">
				alert( "Login not valid.");
			</script>
			<?	
		}
		else
		{
			// store username in session and reload page
			$_SESSION['user'] = $userName; 
			header("location:index.php");
		}
		//printf $login;
	}

	// logout
	if( $mode == "logout")
	{
		session_destroy();
		header("location:index.php");
	}
	
echo "<h3>Login System</h3>";

if( isset($_SESSION['user']))
{
	?>
	<form name="login" method="post" action="index.php?mode=logout">
		<p> Hello <? echo $_SESSION['user'];?>.</p>
		<table border ="0">
			<tr><td></td><td><input type="submit" value="logout" onclick="reloadPage()"></td></tr>
		</table>
	</form>
<?
}
else
{
?>
	<form name="login" method="post" action="index.php?mode=login">
		<table border ="0">
			<tr><td>Username:</td><td> <input type="text" name="uname"></td></tr>
			<tr><td>Password:</td><td> <input type="password" name="pword"></td></tr>
			<tr><td></td><td><input type="submit" value="login" onclick="reloadPage()"></td></tr>
		</table>
	</form>
<?
}
	
