<!doctype html>
<link rel="stylesheet" href="style.css">
<?php
    /**
     * Pievieno navigāciju
     * Q: Vai navigācija ir kāds PHP kods vai tikai HTML?
     * A: Tur ir gan PHP gan HTML
     */
    include "navigation.php";

    //Pārbaude vai ir uzspiesta reset poga
    /**
     * Q: Kā darbojas šis if nosacijums?
     * A: array_key_exists('reset', $_GET) - pārbauda vai reset atslēga ir masīvā $_GET
     * A: $_GET['reset'] == 'true' - salīdzina vai reset vērtība ir 'true'
     */
    if (array_key_exists('reset', $_GET) &&  $_GET['reset'] == 'true') {
        //ir uzspiesta
        resetGame();
        $moves = [];
    }
    else {
        //nav uzspiesta
        $moves = get();
    }

    /**
     * Pārbauda vai ir padoda ID vērtība
     * Q1: Vai šī pārbaude būs vajadzīga?
     * A1: Jā, vajadzīga
     */
    if (array_key_exists('id', $_GET)) {//ir padota
        // pēc skaita nosaka vai jāliek X vai O
        $symbol = count($moves) % 2 == 0 ? 'x' : 'o';

        // Pārbauda vai nav noteikts uzvarētājs
        if (@$moves['winner'] === null) { //nav noteikts uzvarētājs
            // pievieno simbolu json failā
            add($_GET['id'], $symbol);
            checkWinner($symbol);
        }
        else {
            echo "<h2>Winner is '$symbol'!</h2>";
        }
    }
?>



<div class="game_board">
    <?php

    for($i = 1; $i <= 9; $i++) {
        $symbol = array_key_exists($i, $moves) ? $moves[$i] : '';
        // Ievietojam simbolu iekš <a>
        /**
         * Q: Kur ņemam $symbol vērtību?
         */

        echo "<a href='?id=$i'>" . $symbol . "</a> ";
    }
    ?>
</div>
<a href="?reset=true" class="btn">Reset</a>


<?php

function get() {
    // Pārbauda vai neeksistē fails
    if (!file_exists('tic_data.json')) { // Fails neeksistē
        //Pārtraucam funkcijas izpildi izvadot tukšu masīvu
        return [];
    }

    // Paņems JSON formāta visus gājienus no faila un ierakstīs mainīgajā
    $content = file_get_contents('tic_data.json');
    
    // No JSON formāta pārvērš saturu uz massīvu
    $data = json_decode($content, true);
    if (!is_array($data)) {
        $data = [];
    }

    return $data;
}

function add($id, $symbol) {
    // Pieslēdz globālo mainīgo
    global $moves;
    // Vai gājienu masīvā nav ID
    if (!array_key_exists($id, $moves)) { //Šāda ID vel nav
        // Masīvā ieraksta simbolu ar noteiktu ID
        $moves[$id] = $symbol;
        // Gājienu masīvu pārvērš JSON formātā
        $json = json_encode($moves);
        // JSON formātā visus gājienus saglabā failā
        file_put_contents('tic_data.json', $json);     
    }
}

function resetGame() {
    file_put_contents('tic_data.json', '{}');

    header('Location: ?');
}


function checkWinner($symbol) {
    global $moves;

    $win_combinations = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],

        [1, 4, 7],
        [2, 5, 8],
        [3, 6, 9],

        [1, 5, 9],
        [3, 5, 7],
    ];
    foreach ($win_combinations as $combination) {
        if (
            @$moves[$combination[0]] == $symbol &&
            @$moves[$combination[1]] == $symbol &&
            @$moves[$combination[2]] == $symbol
        ) {
            echo "<h2>Winner is '$symbol'!</h2>";
            add('winner', $symbol);
            return;
        }
    }

    /*
    if (
        check(1,2,3) ||
        check(5,4,6) ||
        check(7, 8, 9) ||

        check(1, 4, 7) ||
        check(2, 5, 8) ||
        check(3, 6, 9) ||

        check(1, 5, 9) ||
        check(2, 5, 7)
    ) {
             echo "<h2>Winner is '$symbol'!</h2>";
    }
    */
}

?>