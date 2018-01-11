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
            <form action="/xls-parsing" method=post enctype=multipart/form-data>
                <label class="control-label">Загрузить файл (документ MS Office Excel)</label>
                <input id="file-l" class="file" name=file type="file"
                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                <br>

            </form>
        </div>
    </div>

    <div class="row">
        <div class="btn-group col-sm-12">
            <button type="button" class="btn btn-default dropdown-toggle col-sm-12" data-toggle="dropdown">
                Образец загружаемого файла
                <span class="caret"></span>
            </button>
            <br>
            <br>
            <div class="dropdown-menu col-sm-12" role="menu">
                <br>
                <center>
                    <p class="h3">
                        Для выполнения анализа загрузите документ, заполненный по образцу.
                    </p>
                </center>
                <br>
                <ul class="custom-list">
                    <li class="green-tick">
                        Названия наблюдений, выделенные на изображении зелёным цветом, должны находиться в столбце A, начиная с ячейки A2
                    </li>
                    <li class="red-tick">
                        Названия переменных, выделенных на изображении красным цветом, должны находиться в первой строке, начиная с ячейки B2
                    </li>
                </ul>

                <br>
                <center>
                    <img src="template/images/xls.png">
                </center>
                <br>

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