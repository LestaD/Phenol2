<!DOCTYPE HTML>
<html>
<head>
	<title>{$title}</title>
	<link rel="stylesheet" href="/resource/style.css" />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css' />
</head>
<body>
	<div class="breadcrumbs">{$breadcrumbs}</div>
	<? if(isset($menu)): ?><div class="menu">
		<? foreach( $menu as $name=>$link ) { ?><a href="<?=$link?>"><?=$this->registry->locale->translate($name)?></a><? } ?>
	</div><? endif; ?>
	<div class="content">
		{$body}
	</div>
</body>
</html>