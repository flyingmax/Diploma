<?php


include_once ROOT . '/models/simplexlsx.class.php'; // Подключаем класс парсера
include_once ROOT . '/Exporter/PHPExcel.php'; // Подключаем библиотеку PHPExcel

class Analysis
{
    public static $table = null; // распарсенная таблица
    public static $result = null; // результирующая таблица

    public static function parseXls()
    {
        //массив, хранящий по ключу html код
        $outerHTML = array(
            "source" => "", // таблица с исходной матрицей
            "standard" => "", // таблица стандартизированных данных
            "euclid" => "", // матрица евклидовых расстояний
            "result" => "", // таблица с результатами
            "file" => "",  // временное имя excel файлов для экспорта
        );

        // проверяем, загружен ли пользователем файл
        if (isset($_FILES['file'])) {

            $mas = array(); //в него считывается матрица, из excel документа
            $xlsx = new SimpleXLSX($_FILES['file']['tmp_name']); //создаем объект парсера и передаём в него загруженный файл

            $outerHTML['source'] = '<table class="table" border="1" cellpadding="3" style="border-collapse: collapse">'; // начинаем формирование html таблицы

            list($cols,) = $xlsx->dimension(); //вычисляем число столбцов

            //пробегаем по всем строчкам. $r - это массив строк
            foreach ($xlsx->rows() as $k => $r) {

                array_push($mas, $r); //создаём матрицу данных построчно

                //формируем html вывод таблицы
                $outerHTML['source'] .= '<tr>';
                for ($i = 0; $i < $cols; $i++)
                    $outerHTML['source'] .= '<td>' . ((isset($r[$i])) ? $r[$i] : '&nbsp;') . '</td>';
                $outerHTML['source'] .= '</tr>';
            }
            $outerHTML['source'] .= '</table>';

            Analysis::$table = $mas;

            $isValid = self::validation(Analysis::$table);

            if (!$isValid) {
                return null;
            }

            self::$result = Analysis::processing(); //выполнение всех этапов анализа

            // получаем из результата промежуточные данные для формирования их html-вывода
            $standard = self::$result["standard"];   // стандартизированная матрица
            $euclid = self::$result["euclid"];       // матрица евклидовых расстояний
            $result = self::$result["result_table"]; // результирующая таблица

            // Вывод стандартизированной таблицы
            $outerHTML['standard'] = '<table class="table" border="1" cellpadding="3" style="border-collapse: collapse">';

            for ($i = 0; $i < count($standard); $i++) {
                $outerHTML['standard'] .= '<tr>';
                for ($j = 0; $j < count($standard[$i]); $j++) {
                    $outerHTML['standard'] .= '<td>' . ((isset($standard[$i][$j])) ? $standard[$i][$j] : '&nbsp;') . '</td>';
                }
                $outerHTML['standard'] .= '</tr>';
            }

            $outerHTML['standard'] .= '</table>';

            ///

            // Вывод евклидового расстояния
            $outerHTML['euclid'] = '<table class="table" border="1" cellpadding="3" style="border-collapse: collapse"><tr>';

            for ($i = 0; $i < count(Analysis::$table); $i++) {
                $outerHTML['euclid'] .= '<th>' . Analysis::$table[$i][0] .'</th>';
            }

            $outerHTML['euclid'] .= '<th>Гипотетический лидер</th></tr>';

            for ($i = 0; $i < count($euclid); $i++) {
                $outerHTML['euclid'] .= '<tr>';
                if ($i + 1 < count(Analysis::$table)) {
                    $outerHTML['euclid'] .= '<th>' . Analysis::$table[$i + 1][0] .'</th>';
                } else {
                    $outerHTML['euclid'] .= '<th>Гипотетический лидер</th>';
                }

                for ($j = 0; $j < count($euclid[$i]); $j++) {
                    $outerHTML['euclid'] .= '<td>' . ((isset($euclid[$i][$j])) ? $euclid[$i][$j] : '&nbsp;') . '</td>';
                }
                $outerHTML['euclid'] .= '</tr>';
            }

            $outerHTML['euclid'] .= '</table>';

            ///

            // Вывод результата
            $outerHTML['result'] = '<table class="table" border="1" cellpadding="3" style="border-collapse: collapse"><tr>';

            $outerHTML['result'] .= '<th>Объект наблюдения</th>';
            $outerHTML['result'] .= '<th>Евклидово расстояние</th>';
            $outerHTML['result'] .= '<th>Коэффициент техничности</th>';
            $outerHTML['result'] .= '<th>Место по техничности</th></tr>';


            for ($i = 0; $i < count($result); $i++) {
                $outerHTML['result'] .= '<tr>';
                for ($j = 0; $j < count($result[$i]); $j++) {
                    if ($j == 1 || $j == 2) {
                        $outerHTML['result'] .= '<td>' . ((isset($result[$i][$j])) ? round($result[$i][$j], 4) : '&nbsp;') . '</td>';
                    } else {
                        $outerHTML['result'] .= '<td>' . ((isset($result[$i][$j])) ? $result[$i][$j] : '&nbsp;') . '</td>';
                    }

                }
                $outerHTML['result'] .= '</tr>';
            }

            $outerHTML['result'] .= '</table>';

            // Генерируем и сохраняем на сервере xlsx-файл со случайным именем. Имя сохраняем в $outerHTML['file']
            $outerHTML['file'] = Analysis::generateXlsDoc();

            return $outerHTML;
        }
    }

    public static function validation($table) {

        $rows = count($table);
        $columns = count($table[0]);

        // если шапка пустая
        for ($j = 1; $j < $columns ; $j++) {
            if ($table[0][$j] == "") {
                return false;
            }
        }

        // если кол-во столбцов не везде одинаковое, то ошибка
        for ($i = 1; $i < $rows; $i++) {
            if (count($table[$i]) != $columns) {
                return false;
            }
            // если есть незаполненное имя вуза - ошибка
            if ($table[$i][0] == "") {
                return false;
            }
        }

        // каждая ячейка матрицы (кроме шапки) должна быть числом, иначе - ошибка
        for ($i = 1; $i < $rows; $i++) {
            for ($j = 1; $j < $columns; $j++) {
                if (!is_numeric($table[$i][$j]) or ($table[$i][$j] == "")) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function standardization($table)
    {
        $average = array();
        $average = array_pad($average, count($table[0]) - 1, 0);

        $n = count($table) - 1;

        for ($i = 1; $i < count($table); $i++) {
            for ($j = 1; $j < count($table[$i]); $j++) {
                $average[$j - 1] += $table[$i][$j];
            }
        }

        for ($i = 0; $i < count($average); $i++) {
            $average[$i] = $average[$i] / $n;
        }

        $s = array();
        $s = array_pad($s, count($table[0]) - 1, 0);

        for ($i = 1; $i < count($table); $i++) {
            for ($j = 1; $j < count($table[$i]); $j++) {
                $s[$j - 1] += pow($table[$i][$j] - $average[$j - 1], 2);
            }
        }

        for ($i = 0; $i < count($s); $i++) {
            $s[$i] = sqrt($s[$i] / ($n - 1));
        }

        for ($i = 1; $i < count($table); $i++) {
            for ($j = 1; $j < count($table[$i]); $j++) {
                $table[$i][$j] = round(($table[$i][$j] - $average[$j - 1]) / $s[$j - 1], 3);
            }
        }

        return $table;
    }

    public static function processing()
    {
        // переменные для хранения промежуточных результатов и для конечного результата
        // функция возвращает данную переменную
        $outer = array(
            "standard" => null,
            "euclid" => null,
            "result_table" => null,
        );

        // выполняем стандартизацию таблицы
        $table = self::standardization(Analysis::$table);

        // Поиск гипотетического лидера
        $hl = self::hypotheticalLeader($table);

        // добавили строку для Г.Л. в таблицу
        $table = array_pad($table, count($table) + 1, 0);

        // строка с гл - это массив
        $table[count($table) - 1] = array();

        // увеличили длину для массива г.л.
        $table[count($table) - 1] = array_pad($table[count($table) - 1], count($hl) + 1, 0);
        $table[count($table) - 1][0] = "Гипотетический лидер";
        for ($j = 1; $j < count($table[0]); $j++) {
            $table[count($table) - 1][$j] = round($hl[$j - 1], 3);
        }

        $outer["standard"] = $table;

        // Считаем матрицу евклидовых расстояний
        $euclid = self::euclidDistanceOfTable($table);

        $outer["euclid"] = $euclid["table"];

        // из матрицы евклидовых расстояний берём столбец Г.Л. для дальнейших рассчётов
        $hl = $euclid["hl"];

        // формируем результирующую таблицу и заполняем её итоговыми данными
        $result_table = array();
        $result_table = array_pad($result_table, count($hl) - 1, 0);

        for ($i = 0; $i < count($hl) - 1; $i++) {
            $result_table[$i] = array();
            $result_table[$i] = array_pad($result_table[$i], 4, 0);

            $result_table[$i][0] = $table[$i + 1][0];
            $result_table[$i][1] = $hl[$i];
            $result_table[$i][2] = 1 / $hl[$i];
            $result_table[$i][3] = $i + 1;
        }

        $unsorted = array();

        // Создаём ассоциативный массив, где ключ - имя вуза + "_" + Расстояние
        // Значение - Коэффициент техничности

        for ($i = 0; $i < count($hl) - 1; $i++) {
            $unsorted[$result_table[$i][0] . '_' . $result_table[$i][1]] = $result_table[$i][2];
        }

        // Сортируем по убыванию
        array_multisort($unsorted, SORT_DESC);

        $keys = array_keys($unsorted);

        // Восстанавливаем по ключу имя вуза и расстояние и переприсваиваем результаты.
        $i = 0;
        foreach ($keys as $key) {

            $segments = explode("_", $key);

            $result_table[$i][0] = $segments[0];
            $result_table[$i][1] = $segments[1];
            $result_table[$i][2] = $unsorted[$key];
            $result_table[$i][3] = $i + 1;
            $i++;
        }

        $outer["result_table"] = $result_table;
        return $outer;
    }

    // Евклидово расстояние двух векторов
    public static function euclidDistance($table, $i1, $i2)
    {
        $result = 0;

        for ($j = 1; $j < count($table[$i1]); $j++) {
            $result += pow($table[$i1][$j] - $table[$i2][$j], 2);
        }

        return sqrt($result);
    }

    // Гипотетический лидер в матрице евклидовых расстояний
    public static function euclidDistanceOfTable($table)
    {
        // функция вовращает эту переменную
        $outer = array(
            "table" => null, // вся матрица евклидовых расстояний
            "hl" => null,    // массив Г.Л. из этой матрицы
        );

        $result = array();
        $dimension = count($table) - 1;
        $result = array_pad($result, $dimension, 0);

        for ($i = 0; $i < $dimension; $i++) {
            $result[$i] = array();
            $result[$i] = array_pad($result[$i], $dimension, 0);
        }

        for ($i = 0; $i < $dimension; $i++) {
            for ($j = 0; $j < $dimension; $j++) {
                $result[$i][$j] = round(self::euclidDistance($table, $i + 1, $j + 1), 3);
            }
        }

        // создали пустой массив и "расширили" его до нужной длинны с помощью метода "array_pad@
        $hl = array();
        $hl = array_pad($hl, $dimension, 0);

        for ($i = 0; $i < $dimension; $i++) {
            $hl[$i] = $result[$i][$dimension - 1];
        }

        $outer["table"] = $result;
        $outer["hl"] = $hl;

        return $outer;
    }

    public static function hypotheticalLeader($table)
    {

        $max = array();
        $max = array_pad($max, count($table[0]) - 1, 0);

        for ($i = 1; $i < count($table); $i++) {
            for ($j = 1; $j < count($table[$i]); $j++) {
                if ($table[$i][$j] > $max[$j - 1]) {
                    $max[$j - 1] = $table[$i][$j];
                }
            }
        }
        return $max;
    }

    public static function generateXlsDoc()
    {
        // Создали уникальное имя файла
        $baseName = md5(microtime(true)) . '.xlsx';
        $fileName = ROOT . '/models/' . $baseName; // собрали полное имя файла, включая его расположение на сервере

        $phpexcel = new PHPExcel(); // Создаём объект PHPExcel
        /* Каждый раз делаем активным 1-й лист и получаем его, потом записываем в него данные */
        $page = $phpexcel->setActiveSheetIndex(0); // Делаем активным 1-й лист и получаем его

        // Автоширина
        $phpexcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $phpexcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        // шапка
        $page->setCellValue("A1", "Объект наблюдения");
        $page->setCellValue("B1", "Евклидово расстояние");
        $page->setCellValue("C1", "Коэффициент техничности");
        $page->setCellValue("D1", "Место по техничности");

        $result = self::$result["result_table"];

        for ($i = 0; $i < count($result); $i++) {
            $a = "A" . ($i + 2);
            $b = "B" . ($i + 2);
            $c = "C" . ($i + 2);
            $d = "D" . ($i + 2);

            $page->setCellValue($a, $result[$i][0]);
            $page->setCellValue($b, $result[$i][1]);
            $page->setCellValue($c, $result[$i][2]);
            $page->setCellValue($d, $result[$i][3]);
        }

        // название листа
        $page->setTitle("Отчёт");

        // создаём экземпляр класса-экспортёра в Excel
        $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');

        // генерируем excel и сохраняем в файл
        $objWriter->save($fileName);

        // Возвращаем имя сохранённого файла
        return $baseName;
    }

    public static function exportToXls()
    {
        //скачивание пользователем файла с сервера с именем $_GET['file']
        if (isset($_GET)) {
            $filename = ROOT . '/models/' . $_GET['file'];

            if (ini_get('zlib.output_compression'))
                ini_set('zlib.output_compression', 'Off');

            $file_extension = strtolower(substr(strrchr($filename, "."), 1));

            if ($filename == "") {
                echo "ОШИБКА: не указано имя файла.";
                exit;
            } elseif (!file_exists($filename)) // проверяем существует ли указанный файл
            {
                echo "ОШИБКА: данного файла не существует.";
                exit;
            };
            switch ($file_extension) {
                case "pdf":
                    $ctype = "application/pdf";
                    break;
                case "exe":
                    $ctype = "application/octet-stream";
                    break;
                case "zip":
                    $ctype = "application/zip";
                    break;
                case "doc":
                    $ctype = "application/msword";
                    break;
                case "xls":
                    $ctype = "application/vnd.ms-excel";
                    break;
                    case "xlsx":
                    $ctype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                    break;
                case "ppt":
                    $ctype = "application/vnd.ms-powerpoint";
                    break;
                case "mp3":
                    $ctype = "audio/mp3";
                    break;
                case "gif":
                    $ctype = "image/gif";
                    break;
                case "png":
                    $ctype = "image/png";
                    break;
                case "jpeg":
                case "jpg":
                    $ctype = "image/jpg";
                    break;
                default:
                    $ctype = "application/force-download";
            }
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false); // нужен для некоторых браузеров
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"report.xlsx\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($filename)); // необходимо доделать подсчет размера файла по абсолютному пути
            readfile("$filename");

            //unlink($filename);

            return $filename;
        }
    }
}