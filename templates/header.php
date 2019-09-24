<!DOCTYPE html>
<html>
<head>
	<title>Pet Crud</title>
</head>
<!-- girlfriend was unhappy with the way that the nav looked, so made it more palatable for her. -->
<style type="text/css">
	header, body {
		margin: 0;
	}
	.aPet {
		border: 4px solid purple;
		margin: 10px 200px;
		padding: 10px;
	}
	nav {
		background-color: lavender;
		padding: 20px;
		margin: 0;
	}
	nav ul {
		display: flex;
		list-style-type: none;
		justify-content: space-around;
	}
	nav ul li {
		font-size: 2em;
		border: 2px solid purple;
		padding: 3px 15px;
		border-radius: 45%;
		background-color: #fcedf8;
	}
	li a {
		text-decoration: none;
		color: purple;
	}

	li a:hover {
		color: #b51d8c;
	}

	footer {
		background-color: lavender;
		line-height: 8rem;
	}

</style>
<body>
	<header>
		<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="add.php">Add Pet</a></li>
			</ul>
		</nav>
		<h1>Pet CRUD</h1>
	</header>
