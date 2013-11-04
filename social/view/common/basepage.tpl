<!doctype html>
<html>
<head>
	<title>{title}</title>
	<?php foreach($stylesheets as $style) {?>
	<link rel="stylesheet" type="text/css" href="/style/<?=$style?>" />
	<?}?>
</head>
<body>
{header}
<div class="content">
{content}
</div>
{footer}
</body>
</html>