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
                            <a class="nav-link" aria-current="page" href="index.php"> Jedálny lístok</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="description.php">Popis vytvorených metód API</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="verification.php">Overenie vytvorených metód API</a>
                        </li>


                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="content">

        <div class="container">
            <h1>Jedálne</h1>
            <h3>Overenie vytvorených metód API</h3>
            <div class="row buttons">
                <div class="col-sm">
                    <button type="button" class="btn btn-success" id="down-button">Stiahni</button>
                    <button type=" button" class="btn btn-warning">Rozparsuj</button>
                    <button type="button" class="btn btn-danger" id="del-button">Vymaž</button>
                </div>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $("#down-button").click(function () {
            $.ajax({
                url: "./download.php",
                type: "POST",
                success: function () {
                    alert("Stránky boli úspešne stiahnuté a uložené do databázy");
                }
            })
        })

        $("#del-button").click(function () {
            $.ajax({
                url: "./delete.php",
                type: "POST",
                success: function (response) {
                    if (response === "success") {
                        alert("Dáta boli úspešne vymazené z databázy");
                    } else {
                        alert(response);
                    }
                }
            });
        });

    </script>
</body>

</html>