<?php
/*
Teste de Programação
---
The Comic test
---
Desenvolvedor: Guilherme Reschke
E-mail: greschke@gmail.com
Concluído em: 29/11/2019
*/
$Ts = date("U");
$PublicKey = "cedc9f594332c9f58ee5f6d2cf4dd88c";
$PrivateKey = "4498301f39e3d0a3d63768221b16cc3af8859e2b";
$Hash = hash('md5', $Ts.$PrivateKey.$PublicKey);
$Offset = 0;
$Limit = 100;
$charactersFilePath = $_SERVER['DOCUMENT_ROOT']."/marvel/files/";
$charactersFileName = "allCharacter.dat";

$_character = $_GET['_character'];
if ($_character == '') $_character = $_POST['_character'];
$_story = $_GET['_story'];
if ($_story == '') $_story = $_POST['_story'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="https://getbootstrap.com/docs/3.3/favicon.ico">
		<link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/theme/">

		<title>The Comic test - MARVEL</title>

		<!-- Bootstrap core CSS -->
		<link href="https://getbootstrap.com/docs/3.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Bootstrap theme -->
		<link href="https://getbootstrap.com/docs/3.3/dist/css/bootstrap-theme.min.css" rel="stylesheet">
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<link href="https://getbootstrap.com/docs/3.3/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="css/theme.css" rel="stylesheet">

		<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
		<!--[if lt IE 9]><script src="https://getbootstrap.com/docs/3.3/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
		<script src="https://getbootstrap.com/docs/3.3/assets/js/ie-emulation-modes-warning.js"></script>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
	
		<div class="container-fluid" role="main">
		
			<!-- Fixed navbar -->
			<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand text-danger" href="<?php echo $_SERVER['PHP_SELF']; ?>"  style='color:#e62429 !important;'>Teste de Programação</a>
						</div>
						<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<li><a href="mailto:greschke@gmail.com" class='text-primary'>&copy; Guilherme Reschke</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</nav>

<?php
if ($_character == '')
{
	$Acabou = false;
	$DataArray = Array();
	$CharactersArray = Array();
	echo "
	<div class='page-header'>
		<h1>Personagens MARVEL</h1>
	</div>
	<p>
		Escolha abaixo um personagem para ver suas estórias
	</p>
	";
	while (!$Acabou)
	{
		$URLCharacters = "http://gateway.marvel.com/v1/public/characters?limit=".$Limit."&offset=".$Offset."&ts=".$Ts."&apikey=".$PublicKey."&hash=".$Hash."";

		$cr = curl_init();
		curl_setopt($cr, CURLOPT_URL, $URLCharacters);
		curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
		$retorno = curl_exec($cr);
		curl_close($cr);
		$RetornoDecoded = json_decode($retorno, true);
		$DataArray[] = $RetornoDecoded['data'];
		$Results = $RetornoDecoded['data']['results'];
		if (count($Results) <= 0)
		{
			$Acabou = true;
		}
		else
		{
			$Offset += $Limit + 1;
			$TextoAtribuiaoMarvel['txt'] = $RetornoDecoded['attributionText'];
			$TextoAtribuiaoMarvel['html'] = $RetornoDecoded['attributionHTML'];
			foreach ($Results as $Result)
			{
				$Personagem = Array();
				$Personagem['id'] = $Result['id'];
				$Personagem['name'] = $Result['name'];
				$Personagem['img'] = $Result['thumbnail']['path'].".".$Result['thumbnail']['extension'];
				$CharactersArray[] = $Personagem;
			}
		}
	}
	file_put_contents($charactersFilePath.$charactersFileName, serialize($CharactersArray));
	if (count($CharactersArray) > 0)
	{
		echo "
		<div class='row'>
		";
		$Ctrl = 1;
		foreach ($CharactersArray as $Caracter)
		{
			echo "
			<div class='col-xs-12 col-sm-2'>
				<a href='".$_SERVER['PHP_SELF']."?_character=".$Caracter['id']."' class='thumbnail'>
					<img src='".$Caracter['img']."' alt='".$Caracter['name']."' style='width:100%;height:auto;'>
					<div class='caption text-center'>
						<h4 class='text-danger'>".$Caracter['name']."</h4>
					</div>
				</a>
			</div>
			";
			if ($Ctrl < 6)
			{
				$Ctrl++;
			}
			else
			{
				$Ctrl = 1;
				echo "
				</div>
				<div class='row'>
				";
			}
		}
		echo "
		</div>
		";
	}
}
else //Selecionou personagem
{
	//--> Pega os dados do personagem
	$URLCharacters = "http://gateway.marvel.com/v1/public/characters/".$_character."?limit=1&offset=0&ts=".$Ts."&apikey=".$PublicKey."&hash=".$Hash."";

	$cr = curl_init();
	curl_setopt($cr, CURLOPT_URL, $URLCharacters);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
	$retorno = curl_exec($cr);
	curl_close($cr);
	$PersonagemDecoded = json_decode($retorno, true);
	$TextoAtribuiaoMarvel['txt'] = $PersonagemDecoded['attributionText'];
	$TextoAtribuiaoMarvel['html'] = $PersonagemDecoded['attributionHTML'];
	$DadosDoPersoangem['id'] = $PersonagemDecoded['data']['results'][0]['id'];
	$DadosDoPersoangem['name'] = $PersonagemDecoded['data']['results'][0]['name'];
	$DadosDoPersoangem['description'] = $PersonagemDecoded['data']['results'][0]['description'];
	$DadosDoPersoangem['img'] = $PersonagemDecoded['data']['results'][0]['thumbnail']['path'].".".$PersonagemDecoded['data']['results'][0]['thumbnail']['extension'];
	echo "
	<div class='page-header'>
		<h1>
			<div class='row'>
				<div class='col-xs-4 col-sm-2'>
					<img src='".$DadosDoPersoangem['img']."' style='heigth:auto;width:90%;'>
				</div>
				<div class='col-xs-8 col-sm-10 text-right'>
					".$DadosDoPersoangem['name']."
					<small><p class='list-group-item-text'>".$DadosDoPersoangem['description']."</p></small>
				</div>
			</div>
		</h1>
	</div>
	";
	//--> FIM: Pega os dados do personagem
	
	//--> Restaura os dados de todos os personagens
	$TodosOsPersonagensFileArray = unserialize(file_get_contents($charactersFilePath.$charactersFileName));
	$TodosOsPersonagens = Array();
	foreach ($TodosOsPersonagensFileArray as $Pers)
	{
		$TodosOsPersonagens[$Pers['id']] = $Pers;
	}
	//--> FIM: Restaura os dados de todos os personagens
	
	$HistoriasComPersonagemEscolhido = Array();
	$DataArray = Array();
	$StoriesArray = Array();
	$Acabou = false;
	while (!$Acabou)
	{
		$URLStories = "http://gateway.marvel.com/v1/public/characters/".$_character."/stories?limit=".$Limit."&offset=".$Offset."&ts=".$Ts."&apikey=".$PublicKey."&hash=".$Hash."";

		$cr = curl_init();
		curl_setopt($cr, CURLOPT_URL, $URLStories);
		curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
		$retorno = curl_exec($cr);
		curl_close($cr);
		$RetornoDecoded = json_decode($retorno, true);
		$DataArray[] = $RetornoDecoded['data'];
		$Results = $RetornoDecoded['data']['results'];
		if (count($Results) <= 0)
		{
			$Acabou = true;
		}
		else
		{
			$Offset += $Limit + 1;
			$Story = Array();
			foreach ($Results as $Result)
			{
				$Story['id'] = $Result['id'];
				$Story['title'] = $Result['title'];
				$Story['description'] = $Result['description'];
				$StoryCharacters = Array();
				foreach ($Result['characters']['items'] as $StryPers)
				{
					$PersonagemDestaHistoria = Array();
					$resourceURIArray = explode("/", str_replace("http://", "", $StryPers['resourceURI']));
					$StryPersId = $resourceURIArray[4];
					$DadosDoPersonagem = $TodosOsPersonagens[$StryPersId];
					
					$PersonagemDestaHistoria['id'] = $DadosDoPersonagem['id'];
					$PersonagemDestaHistoria['name'] = $DadosDoPersonagem['name'];
					$PersonagemDestaHistoria['img'] = $DadosDoPersonagem['img'];
					$StoryCharacters[$StryPersId] = $PersonagemDestaHistoria;
				}
				$Story['characters'] = $StoryCharacters;
				$StoriesArray[] = $Story;
			}
		}
	}
	
	if (count($StoriesArray) > 0)
	{
		if ($_story == '')
		{
			//--> Seleciona um número aleatório que esteja entre os índices do array
			$MtRandMin = 0;
			$MtRandMax = count($StoriesArray) - 1;
			$IdxSorteado = mt_rand($MtRandMin, $MtRandMax);
			$StorySorteada = $StoriesArray[$IdxSorteado];
			//--> FIM: Seleciona um número aleatório que esteja entre os índices do array
		}
		else
		{
			$StorySorteada = $StoriesArray[$_story];
		}
		echo "
		<h2>
			<small>Título:</small><br>".$StorySorteada['title']." 
			<a class='btn btn-sm btn-info' role='button' data-toggle='collapse' href='#SelecionarOutraStory' aria-expanded='false' aria-controls='SelecionarOutraStory'>Quer ver outras opções?</a>
		</h2>
		<div class='collapse' id='SelecionarOutraStory'>
			<div class='well'>
				<div class='form-group'>
					<label>Selecione abaixo:</label>
					<select class='form-control' onchange=\"window.location='".$_SERVER['PHP_SELF']."?_character=".$_character."&_story='+this.options[this.selectedIndex].value;\">
		";
		$Idx = 0;
		foreach ($StoriesArray as $StrySelect)
		{
			if ($StorySorteada['id'] == $StrySelect['id']) $StrySelected = "selected";
			else $StrySelected = "";
			echo "
			<option value='".$Idx."' ".$StrySelected.">".$StrySelect['title']." ("; if (count($StrySelect['characters']) > 0) { if (count($StrySelect['characters']) > 1) { echo "".count($StrySelect['characters'])." personagens"; } else { echo "".count($StrySelect['characters'])." personagem"; } } else { echo "nenhum personagem"; } echo ")</option>
			";
			$Idx++;
		}
		echo "
					</select>
				</div>
				<div class='form-group'>
					<a class='btn btn-success' href='".$_SERVER['PHP_SELF']."?_character=".$_character."'>Ou tente outra aleatoriamente, clicando aqui!</a>
				</div>
			</div>
		</div>
		<p>".$StorySorteada['description']."</p>
		<h3>Characters</h3>
		";
		if (count($StorySorteada['characters']) > 0)
		{
			echo "
			<div class='row'>
			";
			$Ctrl = 1;
			foreach ($StorySorteada['characters'] as $Caracter)
			{
				echo "
				<div class='col-xs-12 col-sm-2'>
					<a href='".$_SERVER['PHP_SELF']."?_character=".$Caracter['id']."' class='thumbnail'>
						<img src='".$Caracter['img']."' alt='".$Caracter['name']."' style='width:100%;height:auto;'>
						<div class='caption text-center'>
							<h4>".$Caracter['name']."</h4>
						</div>
					</a>
				</div>
				";
				if ($Ctrl < 6)
				{
					$Ctrl++;
				}
				else
				{
					$Ctrl = 1;
					echo "
					</div><hr><div class='row'>
					";
				}
			}
			echo "
			</div>
			";
		}
	}
	else
	{
		echo "
		<h2 class='text-center text-muted'><span class='glyphicon glyphicon-info-sign'></span></h2>
		<p class='list-group-item-text text-center text-muted'>Nenhuma estória encontrada para \"".$DadosDoPersoangem['name']."\"</p>
		<p class='list-group-item-text text-center text-muted'>Selecione abaixo outro personagem para consulta.</p>
		<p>&nbsp;</p>
		";
		if (count($TodosOsPersonagens) > 0)
		{
			echo "
			<div class='row'>
			";
			$Ctrl = 1;
			foreach ($TodosOsPersonagens as $Caracter)
			{
				echo "
				<div class='col-xs-12 col-sm-2'>
					<a href='".$_SERVER['PHP_SELF']."?_character=".$Caracter['id']."' class='thumbnail'>
						<img src='".$Caracter['img']."' alt='".$Caracter['name']."' style='width:100%;height:auto;'>
						<div class='caption text-center'>
							<h4 class='text-danger'>".$Caracter['name']."</h4>
						</div>
					</a>
				</div>
				";
				if ($Ctrl < 6)
				{
					$Ctrl++;
				}
				else
				{
					$Ctrl = 1;
					echo "
					</div>
					<div class='row'>
					";
				}
			}
			echo "
			</div>
			";
		}
	}
}
?>
			<p class='text-center'><a href="source.zip" class='btn btn-danger'><span class='glyphicon glyphicon-download-alt'></span><br>Fazer o download do projeto!<br>(ZIP)</a></p>
			<hr>
			<footer class='text-center'>
				<p>&copy; <?php echo date("Y"); ?> - <a href="mailto:greschke@gmail.com" class='text-primary'>Guilherme Reschke</a></p>
				<p><a href="http://marvel.com/" target='_blank' style='color:#e62429 !important;'><?php echo $TextoAtribuiaoMarvel['txt']; ?></a></p>
			</footer>

		</div> <!-- /container -->


		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/3.3/assets/js/vendor/jquery.min.js"><\/script>')</script>
		<script src="https://getbootstrap.com/docs/3.3/dist/js/bootstrap.min.js"></script>
		<script src="https://getbootstrap.com/docs/3.3/assets/js/docs.min.js"></script>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="https://getbootstrap.com/docs/3.3/assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>