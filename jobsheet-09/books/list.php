<?php
$page_title = "Book List";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/connection.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM books WHERE title ILIKE :search OR author ILIKE :search");
    $hitung->execute(['search' => "%$search%"]);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM books WHERE title ILIKE :search OR author ILIKE :search ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('search', "%$search%");
    $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM books ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Book List</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Search Book Title</label>
                <input type="text" id="search-input" placeholder="Type book title...">
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Added Date</th>
                        <th>Year</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="5">No book data yet. Please add one via the "Add Book" menu.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['title']; ?></td>
                            <td><?php echo $book['author']; ?></td>
                            <td><?php echo $book['tanggal_ditambahkan']; ?></td>
                            <td><?php echo $book['year']; ?></td>
                            <td><?php echo $book['stock']; ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-delete">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </nav>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
