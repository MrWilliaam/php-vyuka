

<!DOCTYPE html>
<html lang="cs">
 <head>
	<meta charset="utf-8"/>
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<title>
	 HCV Fanshop
	</title>
	<link href="assets/css/main.css" rel="stylesheet"/>
 </head>
 <body>
	<header class="site-header">
	 <div class="container header-inner">
		<a aria-label="Domů" class="brand" href="index.html">
		 <span aria-hidden="true" class="brand-badge">
		 </span>
		 <span>
			HC Vítkovice Ridera Fanshop
		 </span>
		</a>
		<form action="vyhledavani.html" class="searchbar" method="get" role="search">
		 <input aria-label="Hledat produkty" name="q" placeholder="Hledat produkty…" type="search"/>
		</form>
		<nav aria-label="Hlavní menu" class="nav">
		 <a href="index.php">
			Domov
		 </a>
		 <a href="kategorie.php">
			Kategorie
		 </a>
		 <a href="produkty.php">
			Produkty
		 </a>
		 <a href="o-nas.php">
			O nás
		 </a>
		 <a href="kontakt.php">
			Kontakt
		 </a>
		</nav>
		<div class="header-actions">
		 <a aria-label="Košík" class="badge" href="kosik-krok1.html">
			🛒 <?= $cartItemCount ?? 0 ?>
		 </a>
		 <button aria-controls="mobilePanel" aria-expanded="false" class="icon-btn mobile-toggle" data-mobile-toggle="" type="button">
			☰
		 </button>
		</div>
	 </div>
	 <div class="container mobile-panel" data-mobile-panel="" data-open="false" id="mobilePanel" style="display:none">
		<nav aria-label="Mobilní menu" class="stack">
		 <a href="index.html">
			Home
		 </a>
		 <a href="kategorie.html">
			Kategorie
		 </a>
		 <a href="produkty.php">
			Produkty
		 </a>
		 <a href="o-nas.php">
			O nás
		 </a>
		 <a href="kontakt.html">
			Kontakt
		 </a>
		 <a href="kosik-krok1.html">
			Košík
		 </a>
		</nav>
	 </div>
	</header>