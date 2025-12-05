<?php
// Connect to database
function connectDB() {
    $conn = mysqli_connect("localhost", "root", "", "bibia");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Get all categories
function getCategories($conn) {
    $query = "SELECT * FROM categorie_livre";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

// Get books optionally filtered by category
function getBooks($conn, $category_id = null) {
    if ($category_id) {
        $query = "SELECT * FROM livre WHERE id_categorie = '" . mysqli_real_escape_string($conn, $category_id) . "'";
    } else {
        $query = "SELECT * FROM livre";
    }

    $result = mysqli_query($conn, $query);
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}
?>
