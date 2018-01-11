<?php

include_once ROOT. '/models/Main.php';

class MainController
{

		public function actionMain()
		{
		    // подгружаем и выводим главную страницу сайта
			require_once(ROOT . '/views/main/main.php');
			return true;
		}

}