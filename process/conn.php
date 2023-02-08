<?php
    /* Exibir menssagem de erro */
    session_start();

    /* Variavel para conexão com BD */
    $user = "root";
    $pass = "";
    $db = "pizzaria";
    $host = "localhost";

    /* Executar ação ou da erro */
    try {

    $conn = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);
    /* PDO habilitar erro */
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    } catch (PDOException $e) {

    print("Erro: " . $e->getMessage() . "<br/>");
    die();  

    }
?>