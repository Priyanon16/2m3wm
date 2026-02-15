<?php

function addToCart($p_id){

    global $conn;

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit;
    }

    $uid = intval($_SESSION['user_id']);
    $p_id = intval($p_id);

    $check = mysqli_query($conn,"
        SELECT * FROM cart
        WHERE user_id='$uid'
        AND product_id='$p_id'
    ");

    if(mysqli_num_rows($check)>0){
        mysqli_query($conn,"
            UPDATE cart 
            SET quantity = quantity + 1
            WHERE user_id='$uid'
            AND product_id='$p_id'
        ");
    }else{
        mysqli_query($conn,"
            INSERT INTO cart(user_id,product_id,quantity)
            VALUES('$uid','$p_id',1)
        ");
    }

    header("Location: cart.php");
    exit;
}


function addToFavorite($p_id){

    global $conn;

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit;
    }

    $uid = intval($_SESSION['user_id']);
    $p_id = intval($p_id);

    $check = mysqli_query($conn,"
        SELECT * FROM favorites
        WHERE user_id='$uid'
        AND product_id='$p_id'
    ");

    if(mysqli_num_rows($check)==0){
        mysqli_query($conn,"
            INSERT INTO favorites(user_id,product_id)
            VALUES('$uid','$p_id')
        ");
    }

    header("Location: favorite.php");
    exit;
}
?>
