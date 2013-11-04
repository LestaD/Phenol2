<?php


class CCommonDefault extends Controller
{
	
	public function ActionDefault()
	{
		
		// Проверка на валидный кэш
		if ( ($data = $this->cache->Quick('example-action')) === FALSE )
		{
			// Получение реальных данных в случае отсутствия валидного кэша
			$data = 'Default:Common/Default->Default ex=>'.date('H:i:s');
			
			// Запись данных в кэш
			$this->cache->Quick('example-action', $data);
		}
		
		$this->view->link = "/common/default/ext";
		$this->view->link_text = "Extension";
		$this->view->tr("Title", "_Default");
		
		// Отправка данных в вид
		$this->view->body = $data;
		$this->view->myarray = array(
			"me"=>"LestaD",
			"at"=>"@",
			"bk"=>"bk.ru"
		);
		
		
		// Вывод вида
		$this->view->template = "room/base";
		$this->view->childs(array(
			"header"	=> "room/header",
			"footer"	=> "room/footer"
		));
		$this->view->child("footer2", "room/footer2");
		$this->view->render();
	}
	
	
	
	public function ActionExt()
	{
		$this->view->link = "/common/default";
		$this->view->link_text = "Back";
		
		
		// Отправка данных в вид
		$this->view->body = "Extension";
		$this->view->myarray = array(
			"Exa"=>"mpl"
		);
		$this->view->tr("Title", "_Extension");
		
		// Вывод вида
		$this->view->template = "room/base";
		$this->view->childs(array(
			"header"	=> "room/header",
			"footer"	=> "room/footer"
		));
		$this->view->child("footer2", "room/footer2");
		$this->view->render();
	}
}