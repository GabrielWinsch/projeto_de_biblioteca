
<head>
	<title>Ibook.com</title>
	<!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style type="text/css">
    	.brand{
    		background: #cbb09c !important;
    	}
    	.brand-text{
    	color: #cbb09c !important; 
    }
    	form{
    		max-width: 460px;
    		margin:20px auto;
    		padding: 20px;
    	}
    </style>
</head>
<body class="grey lighten-4">
	<nav class="white z-depth-0">
		<div class="container">
			<a href="site.php" class="brand-logo brand-text">Ibook.com</a>
			<ul id="nav-mobile" class="right hide-on-small-and-down"><li>
			<b class="brand-text"> <?php $login_cookie = $_SESSION['login']; 
				echo "Bem Vindo, $login_cookie";?> </b>
				<a href="reservados.php" class="btn brand z-depth-0">MINHAS RESERVAS</a>
				<a href="emprestimo.php" class="btn brand z-depth-0">MEUS EMPRÉSTIMOS</a>
				<a href="../logout.php" class="btn brand z-depth-0">SAIR</a>
			</li>
		</ul>
	</div>
</nav>
</body>
</html>