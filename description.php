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
                            <a class="nav-link active" href="description.php">Popis vytvorených metód API</a>
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
            <h3>Popis vytvorených metód API</h3>
            <div class="row">

                <div class="col-12 col-lg-6  shadow-lg rounded box">
                    <h3>Podporované HTTP metódy</h3>
                    <div class="row description-meth">
                        <div class="col-2 methods">
                            <button type="button" class="btn btn-success">GET</button>
                            <button type="button" class="btn btn-info">POST</button>
                            <button type="button" class="btn btn-primary">PUT</button>
                            <button type="button" class="btn btn-danger">DELETE</button>
                        </div>
                        <div class=" col-10 desc">
                            <p>Používa sa na získanie informácií zo servera.</p>
                            <p>Používa sa na odoslanie dát na server na vytvorenie alebo aktualizáciu zdroja.</p>
                            <p>Používa sa na aktualizáciu existujúceho zdroja na serveri.</p>
                            <p>Používa sa na odstránenie zdroja zo servera.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 shadow-lg rounded box">
                    <h6>Popis</h6>
                    <p>Vracia zoznam jedál spolu s dostupnými cenami pre aktuálny týždeň a reštaurácie</p>
                    <h6>Endpoint</h6>
                    <button type="button" class="btn btn-success">GET /verification?menu</button>
                    <h6>Response</h6>
                    <pre class="bg-dark text-white w-75 mx-auto rounded-3"><code>
    [
        {
            "name": "Tatarák",
            "price": 7.99,
            "date": "2023-03-30",
            "restaurant": "Machnáč"
        },
        ....
    ]
                    </code>
                    </pre>
                    <table class="table table-striped table-hover w-75 mx-auto">
                        <thead>
                            <tr>
                                <th scope="col">Kód</th>
                                <th scope="col">Význam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>200</td>
                                <td>Úspešná odpoveď.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Neplatná požiadavka. Žiadne dáta pre daný deň.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="row ">
                <div class="col-12 col-lg-6 shadow-lg rounded box">
                    <h6>Popis</h6>
                    <p>Na základe zadaného dňa vracia json s detailnými informáciami o jedlách, ktoré sú
                        vtedy podávané (aj cena, miesto);</p>
                    <h6>Endpoint</h6>
                    <button type="button" class="btn btn-success">GET /verification?date=YYYY-MM-DD</button>
                    <h6>Response</h6>
                    <pre class="bg-dark text-white w-75 mx-auto rounded-3"><code>
    [
        {
            "name": "Tatarák",
            "price": 7.99,
            "date": "2023-03-30",
            "restaurant": "Machnáč"
        },
        ....
    ]
                    </code>
                    </pre>
                    <table class="table table-striped table-hover w-75 mx-auto">
                        <thead>
                            <tr>
                                <th scope="col">Kód</th>
                                <th scope="col">Význam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>200</td>
                                <td>Úspešná odpoveď.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Neplatná požiadavka. Neplatný dátum, zlý formát dátumu alebo žiadne dáta pre daný
                                    dátum.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-lg-6 shadow-lg rounded box">
                    <h6>Popis</h6>
                    <p>Do databázy pridá nové jedlo so zadaným názvom a cenou, toto jedlo priradí ku jedálni s
                        poskytnutým id na celý aktuálny týždeň.</p>
                    <h6>Endpoint</h6>
                    <button type="button" class="btn btn-info">POST /verification?id={id}</button>
                    <h6>Parametre</h6>
                    <p>"name", "price"</p>
                    <h6>Response</h6>
                    <pre class="bg-dark text-white w-75 mx-auto rounded-3"><code>
    {
        "success": "Food inserted successfully"
    }
                </code></pre>
                    <table class="table table-striped table-hover w-75 mx-auto">
                        <thead>
                            <tr>
                                <th scope="col">Kód</th>
                                <th scope="col">Význam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>200</td>
                                <td>Úspešná odpoveď. Jedlo bolo vložené do databázy.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Neplatná požiadavka. Neplatná cena.</td>
                            </tr>
                            <tr>
                                <td>404</td>
                                <td>Nenájdený. Zadaná jedáleň sa nenašla v databáze.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="row">
                <div class="col-12 col-lg-6 shadow-lg rounded box">
                    <h6>Popis</h6>
                    <p>Modifikuje/aktualizuje cenu jedla s daným ID.</p>
                    <h6>Endpoint</h6>
                    <button type="button" class="btn btn-primary">PUT /verification?id={id}</button>
                    <h6>Parametre</h6>
                    <p>"price"</p>
                    <h6>Response</h6>
                    <pre class="bg-dark text-white w-75 mx-auto rounded-3"><code>
    {
        "success": "Price updated successfully"
    }
                </code></pre>
                    <table class="table table-striped table-hover w-75 mx-auto">
                        <thead>
                            <tr>
                                <th scope="col">Kód</th>
                                <th scope="col">Význam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>200</td>
                                <td>Úspešná odpoveď. Cena jedla bola upravená.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Neplatná požiadavka. Neplatná ID alebo cena.</td>
                            </tr>
                            <tr>
                                <td>404</td>
                                <td>Nenájdený. Jedlo so zadaným ID sa nenašlo v databáze alebo zadaná cena je identická
                                    ako aktuálna.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-lg-6 shadow-lg rounded box">
                    <h6>Popis</h6>
                    <p>Vymaže ponuku vybranej jedálne z databázy spolu so všetkými údajmi, ktoré
                        k nej prináležia.</p>
                    <h6>Endpoint</h6>
                    <button type="button" class="btn btn-danger">DELETE /verification?id={id}</button>
                    <h6>Response</h6>
                    <pre class="bg-dark text-white w-75 mx-auto rounded-3"><code>
    {
        "success": "Food items deleted successfully"
    }
                </code></pre>
                    <table class="table table-striped table-hover w-75 mx-auto">
                        <thead>
                            <tr>
                                <th scope="col">Kód</th>
                                <th scope="col">Význam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>200</td>
                                <td>Úspešná odpoveď. Dáta boli úspešne vymazané.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Neplatná požiadavka. Žiadne alebo zle zadané ID jedálne.</td>
                            </tr>
                            <tr>
                                <td>404</td>
                                <td>Nenájdený. Jedáleň so zadaným ID sa nenašla v databáze.</td>
                            </tr>
                        </tbody>
                    </table>
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
    <script>
</body >

</html >