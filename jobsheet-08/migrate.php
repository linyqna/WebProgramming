<?php
require __DIR__ . '/includes/connection.php';

$jsonData = file_get_contents(__DIR__ . '/data/book.json');
$books = json_decode($jsonData, true);

if (!$books) {
    die("Data JSON tidak ditemukan atau kosong.");
}

$stmt = $pdo->prepare("
    INSERT INTO book (title, author, year, isbn, stok, kategori) 
    VALUES (:title, :author, :year, :isbn, :stok, :kategori)
");

$count = 0;
foreach ($books as $book) {
    try {
        $stmt->execute([
            'title' => $book['title'],
            'author' => $book['author'],
            'year' => (int)$book['year'],
            'isbn' => $book['isbn'],
            'stok' => (int)$book['stok'],
            'kategori' => $book['kategori']
        ]);
        $count++;
    } catch (PDOException $e) {
        echo "Gagal memigrasi buku '{$book['title']}': " . $e->getMessage() . "<br>";
    }
}

echo "Migrasi selesai! Total $count buku berhasil dipindahkan ke database.";
?>