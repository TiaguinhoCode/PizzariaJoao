<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if($method === "GET") {

    $pedidosQuery = $conn->query("SELECT * FROM pedidos;");

    // Transforma em IDS, Agente quer todos os dados
    $pedidos = $pedidosQuery->fetchAll();

    $pizzas = [];

    // Montando pizza
    foreach($pedidos as $pedido) {

        $pizza = [];

        // Definir um array para a pizza
        $pizza["id"] = $pedido["pizzas_id"];

        // Regastando a pizza
        $pizzaQuery = $conn->prepare
        ("SELECT * FROM pizzas WHERE id = :pizza_id");

        $pizzaQuery->bindParam(":pizza_id", $pizza["id"]);

        $pizzaQuery->execute();

        // Array associativo
        $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);

        // Regastando a borda pizza
        $bordaQuery = $conn->prepare
        ("SELECT * FROM bordas WHERE id = :borda_id");

        $bordaQuery->bindParam
        (":borda_id", $pizzaData["borda_id"]);

        $bordaQuery->execute();

        $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);

        $pizza["borda"] = $borda["tipo"];

        // Resgatando a borda da pizza
        $massaQuery = $conn->prepare
        ("SELECT * FROM massas WHERE id = :massa_id");

        $massaQuery->bindParam
        (":massa_id", $pizzaData["massa_id"]);

        $massaQuery->execute();

        $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);

        $pizza["massa"] = $massa["tipo"];

        // Regastando os sabores da pizza
        $saboresQuery = $conn->prepare
        ("SELECT * FROM pizza_sabor WHERE pizzas_id = :pizzas_id");

        $saboresQuery->bindParam(":pizzas_id", $pizza["id"]);

        $saboresQuery->execute();

        $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);

        // Regastando o nome dos sabores
        $saboresDaPizza = [];

        $saborQuery = $conn->prepare
        ("SELECT * FROM sabores WHERE id = :sabor_id");
    
        foreach($sabores as $sabor) {

            $saborQuery->bindParam(":sabor_id", $sabor["sabores_id"]);

            $saborQuery->execute();

            $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);

            array_push($saboresDaPizza, $saborPizza["nome"]);

        }

        $pizza["sabores"] = $saboresDaPizza;

        // Adicionar o status do pedido
        $pizza["status"] = $pedido["status_id"];

        // Adcionar o array da pizzza, ao array das pizzas
        array_push($pizzas, $pizza); 
        
    }

    // Resgatando os status
    $statusQuery = $conn->query("SELECT * FROM status;");

    $status = $statusQuery->fetchAll();

    } else if($method === "POST") {
        // Verificando tipo de POST 
        $type = $_POST["type"];

        // Deletar pedido
        if($type === "delete") {

        $pizzaId = $_POST["id"];
        // Dinamica
        $deleteQuery = $conn->prepare("DELETE FROM pedidos WHERE pizzas_id = :pizza_id;");

        $deleteQuery->bindParam(":pizza_id", $pizzaId, PDO::PARAM_INT);

        $deleteQuery->execute();

        // Menssagem
        $_SESSION["msg"] = "Pedido removido com sucesso!";
        $_SESSION["status"] = "success";

        // Atualizar o status do pedido
        } else if($type === "update") {

            $pizzaId = $_POST["id"];
            $statusId = $_POST["status"];

            $updateQuery = $conn->prepare("UPDATE pedidos SET status_id = :status_id WHERE pizzas_id = :pizza_id");

            $updateQuery->bindParam(":pizza_id", $pizzaId, PDO::PARAM_INT);
            $updateQuery->bindParam(":status_id", $statusId, PDO::PARAM_INT);
            
            $updateQuery->execute();

            // Menssagem
            $_SESSION["msg"] = "Pedido atualizado com sucesso!";
            $_SESSION["status"] = "success";

        }
        // Retorna o usuário para Dashboard
        header("Location: ../dashboard.php");


    }
 
?>