<?php
/**
 * User profile page
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth.php';

requireLogin();

$page_title = 'Profile';
$success = '';

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT username, email, is_admin, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $user = null;
}

include __DIR__ . '/includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>My Profile</h2>
        </div>
        
        <?php if ($user): ?>
            <div class="profile-container">
                <div class="profile-info">
                    <div class="info-item">
                        <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Role:</strong> <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                    </div>
                    <div class="info-item">
                        <strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-primary">View Events</a>
                    <?php if ($user['is_admin']): ?>
                        <a href="<?php echo SITE_URL; ?>/dashboard/index.php" class="btn btn-secondary">Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Failed to load profile information.</p>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

