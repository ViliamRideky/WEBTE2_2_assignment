<?php
header("Access-Control-Allow-Origin: *");

require_once "./config.php";
$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        $date = $_GET['date'];
        if ($date) {
            header("Content-Type: application/json");
            readMealsByDay($db, $date);
        }
        if (isset($_GET['menu'])) {
            header("Content-Type: application/json");
            readMeals($db);
        }
        break;

    case 'POST':
        $restaurantId = $_GET['id'];
        $inputData = file_get_contents('php://input');
        $data = json_decode($inputData, true);
        if ($restaurantId) {
            insertFood($db, $data, $restaurantId);

        }
        break;

    case 'PUT':
        $inputData = file_get_contents('php://input');
        $data = json_decode($inputData, true);
        $id = $_GET['id'];
        $price = $data['price'];
        updatePrice($db, $id, $price);
        break;

    case 'DELETE':
        $restaurantId = $_GET['id'];
        deleteMealsByRestaurant($db, $restaurantId);
        break;
}

/**
 * GET week menu ORDERED BY date and restaurant
 * @param @db
 * @return void
 */
function readMeals($db)
{
    // aktuálny dátum v tvare 'YYYY-MM-DD'
    $current_date = date('Y-m-d');
    $monday_date = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));

    $stmt = $db->prepare("SELECT name,price,date,restaurant FROM food WHERE date >= '$monday_date' AND date <= DATE_ADD('$monday_date', INTERVAL 6 DAY) ORDER BY restaurant, date");
    $stmt->execute();
    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($menu) {
        http_response_code(200);
        echo json_encode($menu, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Neplatná požiadavka. Žiadne dáta k dispozícií.'));
    }
    exit();
}

/**
 * GET food BY date
 * @param $db
 * @param $date
 * @return void
 */
function readMealsByDay($db, $date)
{

    // Overenie platnosti datumu
    if (!strtotime($date)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Nesprávny formát dátumu'));
        exit();
    }

    // Overenie formatu datumu
    $regex = "/^(\d{4})-(\d{2})-(\d{2})$/";
    if (!preg_match($regex, $date)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Nesprávny formát dátumu'));
        exit();
    }
    $stmt = $db->prepare('SELECT name,price,restaurant,date FROM food WHERE date=:date');
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($meals) {
        http_response_code(200);
        echo json_encode($meals, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Neplatná požiadavka. Žiadne dáta pre daný deň.'));
    }
    exit();
}

/**
 *  INSERT new FOOD into DB
 *  @param $db
 *  @param $data
 *  @param $restaurantId
 *  @return void
 */

function insertFood($db, $data, $restaurantId)
{
    // Check if price is valid
    if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid price format'));
        exit();
    }

    $stmt = $db->prepare('SELECT name FROM source WHERE id = :id');
    $stmt->bindParam(':id', $restaurantId);
    $stmt->execute();
    $restaurantName = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if restaurant exists
    if (!$restaurantName || !isset($restaurantName['name'])) {
        http_response_code(404);
        echo json_encode(array('error' => 'Restaurant not found'));
        exit();
    }

    if ($restaurantName['name']) {
        $current_date = date('Y-m-d');
        $monday_date = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));

        for ($i = 0; $i < 7; $i++) {
            $stmt = $db->prepare('INSERT INTO food (name, price, source_id, restaurant, date ) VALUES (:name, :price, :source_id, :restaurant, :date)');
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':source_id', $restaurantId);
            $stmt->bindParam(':restaurant', $restaurantName['name']);
            $stmt->bindParam(':date', $monday_date);
            $stmt->execute();
            $monday_date = date('Y-m-d', strtotime($monday_date . ' +1 day'));
        }
        http_response_code(200);
        echo json_encode(array('success' => 'Food inserted successfully'));
    }
    exit();
}

/**
 * UPDATE price OF food BY its ID
 * @param $db
 * @param $id
 * @param $price
 * @return void
 */
function updatePrice($db, $id, $price)
{

    // Check if ID is a positive integer
    if (!is_numeric($id) || $id < 1 || strpos($id, '.') !== false) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid ID provided'));
        exit();
    }
    // Check if price is a positive number
    if (!is_numeric($price) || $price <= 0) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid price provided'));
        exit();
    }

    $stmt = $db->prepare('UPDATE food SET price = :price WHERE id = :id');
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    // Check if any row was affected by the update query
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(array('success' => 'Price updated successfully'), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(array('error' => 'Food with provided ID not found or the price is the same'));
    }
    exit();
}

/**
 * 2 implementations, because of the confusing description
 * 1. DELETE food BY source_id
 * @param $db
 * @param $restaurantId
 * @return void
 * 
 * 2. DELETE source BY id with CASCADE DELETE of FOOD with source_id = id
 * @param $db
 * @param $restaurantId
 * @return void
 */
function deleteMealsByRestaurant($db, $restaurantId)
{
    header("Content-Type: application/json");
    if (!isEmpty($restaurantId)) {
        echo json_encode(array('error' => 'Delete failed, incorrect ID provided'));
        http_response_code(400);
        exit();
    } else if (!is_numeric($restaurantId)) {
        echo json_encode(array('error' => 'Delete failed, invalid ID format'));
        http_response_code(400);
        exit();
    } else {
        // Check if restaurant ID exists
        $stmt = $db->prepare('SELECT id FROM source WHERE id = :id');
        $stmt->bindParam(':id', $restaurantId);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!$result) {
            echo json_encode(array('error' => 'Delete failed, restaurant ID does not exist'));
            http_response_code(404);
            exit();
        }
        // Delete restaurant and data
        $stmt = $db->prepare('DELETE FROM source WHERE id = :id');
        $stmt->bindParam(':id', $restaurantId);
        $stmt->execute();
        echo json_encode(array('success' => 'Restaurant and data deleted successfully'));
    }
    exit();
}


function isEmpty($param)
{
    if (empty($param)) {
        $isOk = false;
    } else {
        $isOk = true;
    }
    return $isOk;
}
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
                <div class="col-sm-12">
                    <button type="button" class="btn btn-success" id="down-button">Stiahni</button>
                    <button type=" button" class="btn btn-secondary" id="par-button">Rozparsuj</button>
                    <button type="button" class="btn btn-danger" id="del-button">Vymaž</button>
                </div>
            </div>

            <div class="col-sm-12 ver-meth">
                <h3>Test API metód</h3>
                <button type="button" class="btn btn-success"
                    onclick="window.location.replace('https://site201.webte.fei.stuba.sk/jedalne/verification.php?menu')">GET
                    - week menu</button>
                <button type="button" class="btn btn-success" onclick="hide('getByDate')">GET - by date</button>
                <button type="button" class="btn btn-info" onclick="hide('post')">POST</button>
                <button type="button" class="btn btn-primary" onclick="hide('put')">PUT</button>
                <button type="button" class="btn btn-danger" onclick="hide('delete')">DELETE</button>
            </div>
            <div class="row d-none" id="getByDate">
                <div class="col-sm w-50 mx-auto">
                    <h3>GET - by date</h3>
                    <form action="testApi.php?date=" method="get" id="getForm">
                        <label for="fname">Dátum dňa ktorého jedlá chcete vypísať:</label>
                        <input type="date" class="form-control" id="getDate">
                        <button type="submit" class="btn btn-secondary hide my-2" id="getBtn">Odoslať</button>
                    </form>
                </div>
            </div>

            <div class="row d-none" id="post">
                <div class="col-sm w-50 mx-auto">
                    <h3>POST</h3>
                    <form action="" method="post">
                        <label for="fname">ID jedálne ktorej bude jedlo priradené:</label>
                        <input type="number" class="form-control" name="myId">
                        <label for="fname">Názov jedla:</label>
                        <input type="text" class="form-control" name="myName"
                            onkeydown="return /[a-z]/i.test(event.key)">
                        <label for="fname">Cena jedla:</label>
                        <input type="number" step="0.01" class=" form-control" name="myPrice">
                        <button type="submit" class="btn btn-secondary hide my-2" name="post1"
                            id="postBtn">Odoslať</button>
                    </form>
                </div>
            </div>

            <div class=" row d-none" id="put">
                <div class="col-sm w-50 mx-auto">
                    <h3>PUT</h3>
                    <form action="" method="post" id="putForm">
                        <label for="fname">ID jedla ktorého cenu chcete zmeniť:</label>
                        <input type="number" class="form-control" name="putItem_id">
                        <label for="fname">Nová cena:</label>
                        <input type="number" step="0.01" class="form-control" name="new_price">
                        <button type="submit" class="btn btn-secondary hide my-2" name="put1"
                            id="putBtn">Odoslať</button>
                    </form>
                </div>
            </div>

            <div class="row d-none" id="delete">
                <div class="col-sm w-50 mx-auto">
                    <h3>DELETE</h3>
                    <form action="" method="post" id="delForm">
                        <label for="item_id">ID jedálne, ktorej ponuku chcete vymazať:</label>
                        <input type="number" class="form-control" name="item_id" id="item_id">
                        <button type="submit" class="btn btn-secondary hide my-2" id="delBtn"
                            name="delete1">Odoslať</button>
                    </form>
                </div>
            </div>

        </div>

        </div>
    </main>
    <footer>
        <div class="container">Viliam Rideky &copy; 2023</div>
    </footer>

    <?php
    if (isset($_POST['delete1'])) {
        // Retrieve item ID from form input
        $item_id = $_POST['item_id'];
        // Delete item from database using SQL statement
        $sql1 = "SELECT name FROM source WHERE id = :id";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bindParam(':id', $item_id, PDO::PARAM_INT);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $restaurant = $result['name'];

        if ($restaurant) {
            $sql = "DELETE FROM source WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $item_id, PDO::PARAM_STR);
            $stmt->execute();
            http_response_code(200);
            echo "<script>alert('Reštaurácia s dátami bola úspešné vymazaná');</script>";
        } else {
            http_response_code(404);
            echo "<script>alert('Reštaurácia so zadaným ID nebola nájdena');</script>";
        }
        header("Refresh:0");

    } elseif (isset($_POST['put1'])) {
        $item_id = $_POST['putItem_id'];
        $new_price = $_POST['new_price'];
        $sql = "UPDATE food SET price = :price WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_STR);
        $stmt->bindParam(':price', $new_price, PDO::PARAM_STR);
        $stmt->execute();
        header("Refresh:0");

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo "<script>alert('Cena bola uspéšne aktualizovaná');</script>";
        } else {
            http_response_code(404);
            echo "<script>alert('Jedlo so zadaným ID neexistuje alebo ste zadali rovnáku cenu');</script>";
        }
    } elseif (isset($_POST['post1'])) {
        $current_date = date('Y-m-d');
        $monday_date = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));

        $id = $_POST['myId'];
        $name = $_POST['myName'];
        $price = $_POST['myPrice'];
        $sql1 = "SELECT name FROM source WHERE id = :id";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $restaurant = $result['name'];

        if ($restaurant) {
            for ($i = 0; $i < 7; $i++) {
                $sql = "INSERT INTO food ( name, price, source_id, date, restaurant) VALUES ( :name, :price, :source_id, :date, :restaurant)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':price', $price, PDO::PARAM_STR);
                $stmt->bindParam(':source_id', $id, PDO::PARAM_STR);
                $stmt->bindParam(':date', $monday_date, PDO::PARAM_STR);
                $stmt->bindParam(':restaurant', $restaurant, PDO::PARAM_STR);
                $stmt->execute();
                $monday_date = date('Y-m-d', strtotime($monday_date . ' +1 day'));
            }
            http_response_code(200);
            echo "<script>alert('Jedlo bolo úspešne pridané na každý deň');</script>";
        } else {
            http_response_code(404);
            echo "<script>alert('Reštaurácia so zadaným ID nebola nájdena');</script>";
        }
        header("Refresh:0");
    }

    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>

        function hide(elementId) {
            $('#' + elementId).toggleClass('d-none d-block');
        };

        function send(myform, myinput, mybutton) {
            const form = $('#' + myform);
            const input = form.find('#' + myinput);
            const button = form.find('#' + mybutton);

            form.on('submit', (event) => {
                event.preventDefault();
                const date = input.val();
                const url = `verification.php?date=${date}`;
                form.attr('action', url);
                window.location.href = url;
            });
        }

        send('getForm', 'getDate', 'getBtn');

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

        $("#par-button").click(function () {
            $.ajax({
                url: "./parse.php",
                type: "POST",
                success: function () {
                    alert("Dáta boli úspešne rozparsované");
                }
            })
        })



    </script>
</body>

</html>