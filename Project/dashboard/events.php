<?php
/**
 * List all events for admin management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

requireAdmin();

$page_title = 'Manage Events';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $db = getDB();
        
        // Get event image to delete it
        $stmt = $db->prepare("SELECT image FROM events WHERE id = ?");
        $stmt->execute([$delete_id]);
        $event = $stmt->fetch();
        
        // Delete image if exists
        if ($event && !empty($event['image'])) {
            $image_path = UPLOAD_DIR . $event['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete event (cascade will delete registrations)
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$delete_id]);
        
        $success = 'Event deleted successfully.';
    } catch (PDOException $e) {
        $error = 'Failed to delete event.';
    }
}

try {
    $db = getDB();
    $stmt = $db->query("SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registrations
                       FROM events e 
                       ORDER BY e.event_date ASC, e.event_time ASC");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Manage Events</h2>
            <a href="<?php echo SITE_URL; ?>/dashboard/create_event.php" class="btn btn-primary">Create New Event</a>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($events)): ?>
            <p>No events found. <a href="<?php echo SITE_URL; ?>/dashboard/create_event.php">Create your first event</a></p>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Registrations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($event['image'])): ?>
                                        <img src="<?php echo UPLOAD_URL . $event['image']; ?>" alt="Event image" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span>No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                <td><?php echo $event['registrations']; ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/event_details.php?id=<?php echo $event['id']; ?>" class="btn-small">View</a>
                                    <a href="<?php echo SITE_URL; ?>/dashboard/edit_event.php?id=<?php echo $event['id']; ?>" class="btn-small btn-edit">Edit</a>
                                    <a href="?delete=<?php echo $event['id']; ?>" 
                                       class="btn-small btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

