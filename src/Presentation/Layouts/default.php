<?php


function activeLink(string $link)
{
    $url = $_SERVER['PATH_INFO'];
    if ($url === '/' . strtolower($link)) {
        return "class='nav-link app-nav-link fw-bold'";
    } else if ($url === null && $link === '') {
        return "class='nav-link app-nav-link fw-bold'";
    } else {
        return  "class='nav-link text-secondary'";
    }
}

$title = [
    "" => "Accueil",
    "/clients" => "Gestion des clients",
    "/cars" => "Gestion des voitures",
    "/orders" => "Gestion des achats",
    "/revenues" => "Gestion des recettes",
    "/clients/add" => "Nouveau client",
    "/cars/add" => "Nouvelle voiture",
    "/orders/create" => "Nouvelle vente",
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/styles.css">

    <title> <?= $title[$_SERVER['PATH_INFO']] ?> </title>

</head>


<body class="bg-light container">
    <nav class="row py-3">
        <div class="col-sm-4 col-8 fw-bold fs-4">Gestion de vente.</div>
        <div class="col">
            <ul class="nav d-flex justify-content-end">
                <li class="nav-item "><a href="/" <?= activeLink("") ?> aria-current="page">Acceuil</a></li>
                <li class="nav-item"><a href="/clients" <?= activeLink("Clients") ?>>Clients</a></li>
                <li class="nav-item"><a href="/cars" <?= activeLink("Cars") ?>>Voitures</a></li>
                <li class="nav-item"><a href="/orders" <?= activeLink("Orders") ?>>Achats</a></li>
                <li class="nav-item"><a href="/revenues" <?= activeLink("Revenues") ?>>Recettes</a></li>
            </ul>
        </div>
    </nav>

    {{content}}

    <script src="/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>