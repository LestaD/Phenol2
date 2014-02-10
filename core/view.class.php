<?php
namespace Core {
	
	include DIR_CORE . "viewbase.class.php";
	
	class View extends ViewBase {
		
		
		/**
		 * array(
		 * 	'name' => 'link',
		 * 	'name' => false
		 * )
		 */
		public function generateBreadcrumbs(array $breadcrumbs)
		{
			$count = count($breadcrumbs);
			if ( $count > 0 )
			{
				$cur = 0;
				$code = '';
				foreach( $breadcrumbs as $name=>$link )
				{
					$code .= $link ? '<a href="'.$link.'">'.$this->locale->translate($name).'</a>' : '<span>'.$this->locale->translate($name).'</span>';
					$cur++;
					if ( $cur != $count )
					{
						$code .= ' &raquo; ';
					}
				}
				$this->set('breadcrumbs', $code);
			}
		}
		
		

		/**
		 * Генерация любого списка по двумерному массиву с данными
		 * 
		 * @param array $array
		 * @param string $template
		 * @return void
		 */
		public function generateListByArray( $template, $array )
		{
			$code = '';
			if ( is_array( $array ) ) {
				if ( count($array) > 0 ) {
					foreach ( $array as $item ) {
						if ( is_array($item) ) {
							$counter = 1;
							$curtpl = $template . '';
							foreach ( $item as $id=>$val ) {
								$curtpl = str_replace('%'.$counter, $val, $curtpl);
								$counter++;
							}
							$code .= $curtpl;
						} else {
							$code .= str_replace('%1', $item, $template);
						}
					}
				}
			}
			return $code;
		}
		
		
		
		
		/**
		 * Вывод шаблона на страницу
		 * 
		 * @return
		 */
		public function render()
		{
			if ( $this->error ) return;
			
			$this->registry->detector->dispatchAfterActionEvents();
			
	        header("Content-type: text/html; charset=" . $this->config->db_encode ?: 'utf-8');
			echo $this->dispatch($this);
		}
	}
	

}