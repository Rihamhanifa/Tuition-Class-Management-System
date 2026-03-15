<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Pagination and Search
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$whereClause = "";
$params = [];
if (!empty($search)) {
    $whereClause = "WHERE first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

try {
    // Total records for pagination
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM students $whereClause");
    $countStmt->execute($params);
    $total_records = $countStmt->fetchColumn();
    $total_pages = ceil($total_records / $per_page);

    // Fetch students
    $stmt = $pdo->prepare("SELECT * FROM students $whereClause ORDER BY created_at DESC LIMIT $offset, $per_page");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='badge badge-danger'>Error: " . $e->getMessage() . "</div>";
    $students = [];
    $total_pages = 1;
}
?>

<div class="page-header">
    <h1 class="page-title">Students</h1>
    <a href="add.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Student</a>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 10px; width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; height: 38px;">
            <button type="submit" class="btn btn-secondary" style="height: 38px;">Search</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($students)): ?>
                    <tr><td colspan="5" style="text-align: center;">No students found</td></tr>
                <?php else: ?>
                    <?php foreach($students as $student): ?>
                        <tr>
                            <td>#<?= str_pad($student['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td style="font-weight: 500;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($student['phone']) ?></td>
                            <td><?= date('M d, Y', strtotime($student['joined_date'])) ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.85rem;"><i data-lucide="edit" style="width: 14px; height: 14px;"></i></a>
                                    <form action="delete.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 4px 8px; border: none; font-size: 0.85rem; cursor: pointer;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 5px;">
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="btn <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>" style="padding: 6px 12px;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
