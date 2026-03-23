<?php
/**
 * View event registrations
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

requireAdmin();

$page_title = 'Event Registrations';
$event_id = intval($_GET['event_id'] ?? 0);

try {
    $db = getDB();
    
    // Get all events for filter
    $stmt = $db->query("SELECT id, title FROM events ORDER BY event_date DESC");
    $events_list = $stmt->fetchAll();
    
    // Build query
    if ($event_id > 0) {
        $stmt = $db->prepare("SELECT er.*, e.title as event_title 
                             FROM event_registrations er 
                             JOIN events e ON er.event_id = e.id 
                             WHERE er.event_id = ? 
                             ORDER BY er.registered_at DESC");
        $stmt->execute([$event_id]);
    } else {
        $stmt = $db->query("SELECT er.*, e.title as event_title 
                           FROM event_registrations er 
                           JOIN events e ON er.event_id = e.id 
                           ORDER BY er.registered_at DESC");
    }
    $registrations = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $registrations = [];
    $events_list = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Event Registrations</h2>
        </div>
        
        <!-- Filter by event -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <select name="event_id" id="event_id" class="filter-select">
                    <option value="0">All Events</option>
                    <?php foreach ($events_list as $event): ?>
                        <option value="<?php echo $event['id']; ?>" <?php echo $event_id == $event['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($event['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        
        <?php if (empty($registrations)): ?>
            <p>No registrations found.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                <td><?php echo htmlspecialchars($reg['name']); ?></td>
                                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($reg['registered_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

