<?php

function addToCart($id){
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]++;
    }else{
        $_SESSION['cart'][$id] = 1;
    }
}

function addToFavorite($id){
    if(!in_array($id, $_SESSION['favorite'])){
        $_SESSION['favorite'][] = $id;
    }
}
