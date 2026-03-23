<?php
/**
 * Admin Dashboard - Overview
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

requireAdmin();

$page_title = 'Dashboard';

try {
    $db = getDB();
    
    // Get statistics
    $stmt = $db->query("SELECT COUNT(*) as total FROM events");
    $total_events = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()");
    $upcoming_events = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM event_registrations");
    $total_registrations = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    // Get recent events
    $stmt = $db->query("SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registrations
                       FROM events e 
                       ORDER BY e.created_at DESC 
                       LIMIT 5");
    $recent_events = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_events = 0;
    $upcoming_events = 0;
    $total_registrations = 0;
    $total_users = 0;
    $recent_events = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $total_events; ?></h3>
                <p>Total Events</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $upcoming_events; ?></h3>
                <p>Upcoming Events</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_registrations; ?></h3>
                <p>Total Registrations</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_users; ?></h3>
                <p>Registered Users</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="<?php echo SITE_URL; ?>/dashboard/create_event.php" class="btn btn-primary">Create New Event</a>
                <a href="<?php echo SITE_URL; ?>/dashboard/events.php" class="btn btn-secondary">Manage Events</a>
                <a href="<?php echo SITE_URL; ?>/dashboard/registrations.php" class="btn btn-secondary">View Registrations</a>
            </div>
        </div>
        
        <!-- Recent Events -->
        <?php if (!empty($recent_events)): ?>
            <div class="dashboard-section">
                <h3>Recent Events</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo $event['registrations']; ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/event_details.php?id=<?php echo $event['id']; ?>" class="btn-link">View</a>
                                        <a href="<?php echo SITE_URL; ?>/dashboard/edit_event.php?id=<?php echo $event['id']; ?>" class="btn-link">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

