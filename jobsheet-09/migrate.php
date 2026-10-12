<?php
require __DIR__ . '/includes/connection.php';

$jsonString = file_get_contents(__DIR__ . '/data/books.json');
$booksData = json_decode($jsonString, true);

if (!$booksData) {
    die("File JSON tidak ditemukan atau formatnya salah.");
}

$stmt = $pdo->prepare(
    "INSERT INTO books (title, author, year, isbn, stock, category) 
     VALUES (:title, :author, :year, :isbn, :stock, :category)"
);

$berhasil = 0;
echo "<h3>Proses Migrasi Dimulai...</h3>";

foreach ($booksData as $book) {
    try {
        $stmt->execute([
            'title' => $book['title'],
            'author' => $book['author'],
            'year' => (int) $book['year'],
            'isbn' => '-', 
            'stock' => (int) $book['stock'],
            'category' => $book['category']
        ]);
        echo "Berhasil memindahkan: " . $book['title'] . "<br>";
        $berhasil++;
    } catch (PDOException $e) {
        echo "<span style='color:red;'>Gagal memindahkan " . $book['title'] . ": " . $e->getMessage() . "</span><br>";
    }
}

echo "<strong>Selesai! Total $berhasil buku berhasil dimasukkan ke database.</strong>";
?>