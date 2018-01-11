<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Ранжирование</title>

    <!-- Bootstrap core CSS -->
    <link href="/template/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="/template/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/template/css/main.css" rel="stylesheet">
    <link href="/template/css/xls.css" rel="stylesheet">
    <link href="/template/css/fileinput.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class=""><a href="/">Главная</a></li>
                <li class="active"><a href="/upload">Анализ</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>


<div class="container theme-showcase" role="main">

    <div class="row">

        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li><a data-toggle="tab" href="#src">Исходные данные</a></li>
                <li><a data-toggle="tab" href="#middle">Промежуточные результаты</a></li>
                <li class="active"><a data-toggle="tab" href="#result">Результаты анализа</a></li>
            </ul>
        </div>

        <div class="tab-content">
            <div id="src" class="tab-pane fade">
                <div class="col-sm-12">
                    <h3>Результат парсинга Excel-документа</h3>
                </div>
                <br>

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Таблица исходных данных</h3>
                        </div>
                        <div class="panel-body">
                            <div class="scrollable row-sm-5">
                                <?php
                                if (isset($table['source'])) {
                                    echo $table['source'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.col-sm-4 -->

            </div>

            <div id="middle" class="tab-pane fade">
                <div class="col-sm-12">
                    <h3>Промежуточные результаты</h3>
                </div>
                <br>

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Таблица стандартизированных данных</h3>
                        </div>
                        <div class="panel-body">
                            <div class="scrollable row-sm-5">
                                <br>
                                <?php
                                if (isset($table['standard'])) {
                                    echo $table['standard'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.col-sm-4 -->

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Матрица евклидовых расстояний</h3>
                        </div>
                        <div class="panel-body">
                            <div class="scrollable row-sm-5">
                                <br>
                                <?php
                                if (isset($table['euclid'])) {
                                    echo $table['euclid'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.col-sm-4 -->

            </div>

            <div id="result" class="tab-pane fade in active">
                <div class="col-sm-12">
                    <h3>Результаты анализа</h3>
                </div>
                <br>

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <ul class = "list-inline custom-inline">
                                <li><h3 class="panel-title">Итоговая таблица</h3></li>
                                <li class = "pull-right">
                                    <?php
                                    if (isset($table['file'])) {
                                        echo '<a download class=\'btn btn-success\' href=\'to-xls/?file=' . $table['file'] . '\'>Экспорт в файл Excel.</a>';
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <br>
                            <div class="scrollable row-sm-5">
                                <?php
                                if (isset($table['result'])) {
                                    echo $table['result'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.col-sm-4 -->

            </div>


        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="/template/js/bootstrap.min.js"></script>
<script src="/template/js/docs.min.js"></script>

<script src="/template/js/plugins/purify.js"></script>
<script src="/template/js/plugins/sortable.js"></script>
<script src="/template/js/fileinput.js"></script>

</body>
</html>