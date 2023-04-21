<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
require_once "./canteens/delikanti.php";
require_once "./canteens/eat.php";
require_once "./canteens/freefood.php";



?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zadanie 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Zadanie 2</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                    aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php"> Jedálny lístok</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="description.php">Popis vytvorených metód API</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="verification.php">Overenie vytvorených metód API</a>
                        </li>


                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="content">

        <div class="container">
            <h1>Jedálne</h1>
            <div class="row">
                <div class="col-12 pick-day">
                    <label for="pickDay">Výber dňa:</label>
                    <select name="pickDay" id="pickDay">
                        <option value="all">Celý týždeň</option>
                        <option value="pon">Pondelok</option>
                        <option value="uto">Utorok</option>
                        <option value="str">Streda</option>
                        <option value="štv">Štvrtok</option>
                        <option value="pia">Piatok</option>
                    </select>
                </div>
            </div>
            <div class="row" id="menu">
                <div class="col-12 col-lg-4">
                    <!-- <h3>Delikanti - FEI STU</h3> -->
                    <h3>
                        <?php echo $name_del ?>
                    </h3>
                    <a href="https://www.delikanti.sk/prevadzky/1-jedalen-fei-stu/" target="_blank">
                        <img src="./images/delikanti.png" alt="delikanti-logo" class="images" width="300" height="70">
                    </a>
                    <?php
                    for ($i = 0; $i < sizeof($foods_delikanti) - 2; $i++) { ?>
                        <div data-day="<?php echo $i ?>" class="dayos">
                            <h5>
                                <?php echo $foods_delikanti[$i]["day"] . " - " . $foods_delikanti[$i]["date"] ?>
                            </h5>
                            <ol class="list-group">
                                <?php

                                foreach ($foods_delikanti[$i]["menu"] as $item) {
                                    echo "<li class=list-group-item>$item</li>";
                                }

                                ?>
                            </ol>
                        </div>
                    <?php } ?>
                    <!-- <h4 class="dayos">Cez víkend je jedáleň
                        <?php echo $name_del; ?> zatvorená.
                    </h4> -->
                </div>

                <div class="col-12 col-lg-4">
                    <!-- <h3>Eat & Meet</h3> -->
                    <h3>
                        <?php echo $name_eat ?>
                    </h3>
                    <a href="http://eatandmeet.sk/tyzdenne-menu" target="_blank">
                        <img src="./images/eat.png" alt="eat-logo" class="images" width="300" height="70">
                    </a>
                    <?php
                    for ($i = 0; $i < sizeof($foods_eat) - 2; $i++) { ?>
                        <div data-day="<?php echo $i ?>" class="dayos">
                            <h5>
                                <?php echo $foods_eat[$i]["day"] . " - " . $foods_eat[$i]["date"] ?>
                            </h5>
                            <ol class="list-group">
                                <?php

                                foreach ($foods_eat[$i]["menu"] as $item) {
                                    echo "<li class=list-group-item>$item</li>";
                                }

                                ?>
                            </ol>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-12 col-lg-4">
                    <!-- <h3>FreeFood - FIITFOOD</h3> -->
                    <h3>
                        <?php echo $name_free ?>
                    </h3>
                    <a href="http://www.freefood.sk/menu/#fiit-food" target="_blank">
                        <img src="./images/freefood.png" alt="freefood-logo" class="images" width="105">
                    </a>
                    <?php
                    for ($i = 0; $i < sizeof($foods_freefood); $i++) { ?>
                        <div data-day="<?php echo $i ?>" class="dayos">
                            <h5>
                                <?php echo $foods_freefood[$i]["day"] . " - " . $foods_freefood[$i]["date"] ?>
                            </h5>
                            <ol class="list-group">
                                <?php

                                foreach ($foods_freefood[$i]["menu"] as $item) {
                                    $item = substr_replace($item, "&nbsp;&nbsp;&nbsp;", -7, 0); // add 3 spaces before the last 3 characters of the string
                                    echo "<li class=list-group-item>$item</li>";
                                }


                                ?>
                            </ol>
                        </div>
                    <?php } ?>
                    <!-- <h4 class="dayos">Cez víkend je jedáleň
                        <?php echo $name_free; ?> zatvorená.
                    </h4> -->
                </div>

            </div>

        </div>
    </main>
    <footer>
        <div class="container">Viliam Rideky &copy; 2023</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script>
        var daySelect = document.getElementById("pickDay");
        daySelect.value = "all";
        var monday = document.querySelectorAll('div[data-day="0"]')
        var tuesday = document.querySelectorAll('div[data-day="1"]')
        var wednesday = document.querySelectorAll('div[data-day="2"]')
        var thursday = document.querySelectorAll('div[data-day="3"]')
        var friday = document.querySelectorAll('div[data-day="4"]'),
            i = 0,
            l = monday.length;

        daySelect.addEventListener("change", function () {
            if (daySelect.value === "all") {
                console.log("all")
                for (i; i < l; i++) {
                    monday[i].style.display = 'block';
                    tuesday[i].style.display = "block";
                    wednesday[i].style.display = "block";
                    thursday[i].style.display = "block";
                    friday[i].style.display = "block";
                }
                i = 0
            }
            if (daySelect.value === "pon") {
                console.log("pon")
                for (i; i < l; i++) {
                    monday[i].style.display = 'block';
                    tuesday[i].style.display = "none";
                    wednesday[i].style.display = "none";
                    thursday[i].style.display = "none";
                    friday[i].style.display = "none";
                }
                i = 0
            }
            if (daySelect.value === "uto") {
                console.log("uto")
                for (i; i < l; i++) {
                    monday[i].style.display = 'none';
                    tuesday[i].style.display = "block";
                    wednesday[i].style.display = "none";
                    thursday[i].style.display = "none";
                    friday[i].style.display = "none";
                }
                i = 0
            }
            if (daySelect.value === "str") {
                console.log("str")
                for (i; i < l; i++) {
                    monday[i].style.display = 'none';
                    tuesday[i].style.display = "none";
                    wednesday[i].style.display = "block";
                    thursday[i].style.display = "none";
                    friday[i].style.display = "none";
                }
                i = 0
            }
            if (daySelect.value === "štv") {
                console.log("štv")
                for (i; i < l; i++) {
                    monday[i].style.display = 'none';
                    tuesday[i].style.display = "none";
                    wednesday[i].style.display = "none";
                    thursday[i].style.display = "block";
                    friday[i].style.display = "none";
                }
                i = 0
            }
            if (daySelect.value === "pia") {
                console.log("pia")
                for (i; i < l; i++) {
                    monday[i].style.display = 'none';
                    tuesday[i].style.display = "none";
                    wednesday[i].style.display = "none";
                    thursday[i].style.display = "none";
                    friday[i].style.display = "block";
                }
                i = 0
            }
        });
    </script>

</body>

</html>