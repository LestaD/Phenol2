<?php


class DefaultErrors extends \Core\Phenol2ErrorListener
{
	public function errorControllerLoad($controller)
	{
		?>
		<div style="margin: 25px; background-color: #fcc; padding: 20px; ">
			Error loading controller: <b><?=$controller?></b>
		</div>
		<?
		die();
	}
}



