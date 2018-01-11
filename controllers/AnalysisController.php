<?php

include_once ROOT. '/models/Analysis.php';

class AnalysisController
{
    public function actionUploadPage()
    {
        //выводим на экран html страницу с загрузкой файла excel
        require_once(ROOT . '/views/analysis/upload.php');
        return true;
    }

    public function actionUploadXLS()
    {
        //производится загрузка и парсинг excel, выполняется анализ
        $table = Analysis::parseXls();

        // Если ошибок при проверке excel не обнаружено - выводим результаты, иначе - сообщение об ошибке
        if (!$table == null) {
            require_once(ROOT . '/views/analysis/view.php'); //выводим страницу с отображением результатов
        } else {
            require_once(ROOT . '/views/analysis/xls-error.php'); //выводим страницу с ошибкой
        }

        return true;
    }

    public function actionExport() //скачивание пользователем документа excel
    {
        Analysis::exportToXls();
        return true;
    }

}