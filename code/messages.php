<?php
	include 'connect.php';
	$userID = $_SESSION['userid'];
	$rec_id = "";
	$to = "";

	//Check if user is logged in, if not go to home
	if($userID == ""){
		echo "<script>alert('You have to be logged in in order to be able to user the messaging. Please Log In and try again later!')</script>";
		header("Location:home.php");
		exit;
	}
	
	$connected = FALSE;
	if($username != "" or $email != ""){
		$connected = TRUE;
	}

	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	//a function to check whether two users have blocked each other or not
	//returns true if blocked
	function is_blocked($user1_id, $user2_id, $db){
		//user_1 should be logged in
		if($user1_id != ""){
			$sql = "SELECT * from UserBlock where ( (blockerID = '$user1_id') and (blockedID = '$user2_id') ) or ( (blockerID = '$user2_id') and (blockedID = '$user1_id') )";
			$result = mysqli_query($db, $sql);

			if( mysqli_num_rows($result) > 0 ){
				return true;
			}
			else{
				return false;
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Messages</title>
	<!-- Bootstrap -->
	<link href="css/bootstrap.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

	</head>
	<body style="padding-top: 70px">
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
		<script src="js/jquery-1.11.3.min.js"></script>

		<!-- Include all compiled plugins (below), or include individual files as needed --> 
		<script src="js/bootstrap.js"></script>

		<div class="container-fluid">
			<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topFixedNavbar1" aria-expanded="false"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
          <a class="navbar-brand" href="home.php">Servo</a></div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="topFixedNavbar1">
          <form class="navbar-form navbar-left" role="search" method="post">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search" name="searchInput">
            </div>
            <button type="submit" class="btn btn-default" name="searchButton">Submit</button>
          </form>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="home.php" title="Home Page Link">Home</a></li>
            <li><a href="messages.php">Messages</a></li>
            <li><a href="settingsadmin.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
        <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
    </nav>
				<div class="row">					
					<div class="col-sm-3" id="friendlist">
						<h3>Following List</h3>		  
						<!-- Show the list of available users -->
						<ul class="list-group">						
							<?php
								//construct a query for the users list and call it
								$user_list_sql = "SELECT * FROM User Where 1 ORDER BY username DESC";
								$user_list_sql_result = mysqli_query($conn, $user_list_sql);

								//display available users to interact
								if(mysqli_num_rows($user_list_sql_result) > 0){
									while($row = mysqli_fetch_array($user_list_sql_result, MYSQLI_ASSOC)){
										$rec_id = $row["ID"];
										$rec_name = $row["username"];
										//print the list of users that are not blocked
										if(!is_blocked($userID, $rec_id, $conn) and ($rec_id != $userID) and !empty($rec_name))
										{
											echo "<li class='list-group-item' onclick=\"window.location.href='messages.php?recipient=$rec_name'\" >".$rec_name."</li>";
										}
									}
								}
							?>
						</ul>
					</div>		

					<div class="col-sm-5" id = "middlecolumn">					
						<!-- Show the messages -->
						<?php
							if(isset($_GET['recipient']) && !empty($_GET['recipient'])){
							$to = ($_GET['recipient']);
							}

							if($to != ""){

								$user_message_sql = "SELECT * FROM User Where username = '$to'";
								$user_message_result = mysqli_query($conn, $user_message_sql);

								//check if there is more than one user with the same name
								if(mysqli_num_rows($user_message_result) > 1){
									echo "<script>alert('Something went wrong! Please try again.')</script>";
									header("Location:messages.php");
									exit;
								}

								//get that row
								$row = mysqli_fetch_array($user_message_result,MYSQLI_ASSOC);

								// if(mysqli_num_rows($user_message_result) > 0){
								if($row){
									$rec_id = $row["ID"];
									//check whether the two users have blocked each other
									if(is_blocked($userID, $rec_id, $conn) or ($rec_id == $userID)){
										echo "<script>alert('You are not allowed to interact with this user!')</script>";
										header("Location:messages.php");
										exit;
									}
									else{
										$msg_sql = "SELECT * from UserMessage where ( (senderID = '$userID') and (receiverID = '$rec_id') ) or ( (senderID = '$rec_id') and (receiverID = '$userID') ) ORDER BY messageDate ASC";
										$msg_result = mysqli_query($conn, $msg_sql);

										while($row = mysqli_fetch_array($msg_result,MYSQLI_ASSOC)){
											if($userID == $row["senderID"]){
												echo "<p> You: ".$row["messageContent"]."</p>";
											}
											else{
												echo "<p>".$to.": ".$row["messageContent"]."</p>";
											}
										}
										
										//generate the form
										?>
										<h3>Message</h3>
										<div>
										<form name="send_msg" method="post">
											<textarea name="message_field" rows="3" style="width:100%;"></textarea>
											<button type="submit" class="btn btn-primary">Send</button>
										</form>
										</div>

									<?php
										//Upon post generated by the form, declared above
										if($_SERVER["REQUEST_METHOD"] == "POST"){
											//if the message is not empty
											if(!empty($_POST["message_field"])){
												$msg = $_POST["message_field"];
												$timestamp = date('Y-m-d G:i:s');
												$sql = "INSERT INTO `UserMessage` (`messageContent`, `messageDate`, `senderID`, `receiverID`) VALUES ('$msg', '$timestamp', '$userID', '$rec_id')";
												if(mysqli_query($conn, $sql)){
													header("Refresh:0");
													exit;
												}
												else{
													echo "<script>alert('Something went wrong! Please try again.')</script>";
													header("Location:messages.php");
													exit;
												}
											}
										}
									}
								}
								else{
									echo "<script>alert('The user that you want to message does not exist')</script>";
									header("Location:messages.php");
									exit;
								}
							}	
						?>
					</div>
					<div class="col-sm-4" id="rightbar">						

						
					</div>
				</div>
			</div>	
		</body>
	</html>