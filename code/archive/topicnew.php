<?php
include 'connect.php';
$userID = $_SESSION['userid'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Untitled Document</title>
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
					<div class="col-sm-3" id="topicbrowser">		  
						<h3>Browse Existing Topics:</h3>
						<div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">

							<?php		

							$db = $conn;

							$topiclistsql = "SELECT * FROM Topic_Combined_View ORDER BY categoryName DESC, date ASC";
							$tagsql = "SELECT * FROM Category";

							$topiclistsqlresult = mysqli_query($db, $topiclistsql);
							$tagsqlresult = mysqli_query($db, $tagsql);

							$topiccontent = array();
							$topictag = array();
							$taglist = array();
							$topicidlist = array();

							if(mysqli_num_rows($topiclistsqlresult) > 0){
								while($row = mysqli_fetch_array($topiclistsqlresult,MYSQLI_ASSOC)) {
									$topiccontent[] = $row["content"];
									$topictag[] = $row["categoryName"];
									$topicidlist[] = $row["ID"];
								}
							}

							if(mysqli_num_rows($tagsqlresult) > 0){
								while($row = mysqli_fetch_array($tagsqlresult,MYSQLI_ASSOC)) {
									$taglist[] = $row["name"];	
								}
							}			

							for ($i = 0; $i < (sizeof($taglist)); $i++)
							{
								// Categories
								echo "<div class='panel panel-default'>";
								echo "<div class='panel-heading' role='tab'>";
								echo "<h4 class='panel-title'>".$taglist[$i]."</a></h4>";
								echo "</div>";
								echo "<ul class='list-group'>";
								for ($j = 0; $j < (sizeof($topiccontent)); $j++)
								{
									if(strcmp($topictag[$j], $taglist[$i]) == 0)
									{
										//tag matches
										// Topics
										echo "<div id='collapseOne1' class='panel-collapse collapse in'>";
										echo "<div class='panel-body'>"."<a href='topic.php?varname=$topicidlist[$j] class='list-group-item list-group-item-action'>".$topiccontent[$j]."</a>"."</div>";
										echo "</div>";					
									}	
								}
								echo "</ul>";
								echo "</div>";
							}
							?>
						</div>
					</div>		

					<div class="col-sm-5" id = "middlecolumn">
						<?php
						// Xheni's code

						?>

					</div>
					<div class="col-sm-4" id="rightbar">
						<div id="usertopicsbar">
							<h3>Your Topics:</h3>
							<ul class="list-group">
								<?php 

								$usertopicsql = "SELECT ID, content FROM Topic WHERE userID = '$userID'";
								$usertopicsresult = mysqli_query($conn, $usertopicsql);

								if(mysqli_num_rows($usertopicsresult) > 0)
								{
									while($row = mysqli_fetch_array($usertopicsresult,MYSQLI_ASSOC)) 
									{
										$topicid = $row["ID"];
										echo "<a href='topic.php?varname=$topicid' class='list-group-item list-group-item-action'>".$row["content"]."</a>";				
									}
								}
								else
									echo "<li class='list-group-item'> You Have Not Posted Any Topics";

								?>
							</ul>
						</div>	

						<div id="favtopicsbar">
							<h3>Your Favorite Topics:</h3>
							<ul class="list-group">	
								<?php

								$favoritetopicsql = "SELECT * FROM Topic_Combined_View WHERE ID in (SELECT Favorite.contentID FROM Favorite WHERE userID = '$userID' && isInstanceTopic = 1) ORDER BY date DESC";
								$favoritetopicsresult = mysqli_query($conn, $favoritetopicsql);

								if(mysqli_num_rows($favoritetopicsresult) > 0)
								{
									while($row = mysqli_fetch_array($favoritetopicsresult,MYSQLI_ASSOC)) 
									{
										$topicid = $row["ID"];
										echo "<a href='topic.php?varname=$topicid' class='list-group-item list-group-item-action'>".$row["content"]."</a>";
									}
								}
								else
									echo "<li class='list-group-item'> You Don't Have Any Favorite Topics";
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>	
		</body>
	</html>