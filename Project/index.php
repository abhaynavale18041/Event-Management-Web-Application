<?php
/**
 * Home page - List upcoming events with search and pagination
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth.php';

$page_title = 'Home';
$search = sanitize($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 6;
$offset = ($page - 1) * $per_page;

try {
    $db = getDB();
    
    // Build query
    $where = "event_date >= CURDATE()"; // Only upcoming events
    
    if (!empty($search)) {
        $where .= " AND (title LIKE :search OR description LIKE :search OR venue LIKE :search)";
    }
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM events WHERE $where";
    $count_stmt = $db->prepare($count_sql);
    if (!empty($search)) {
        $count_stmt->bindValue(':search', '%' . $search . '%');
    }
    $count_stmt->execute();
    $total_events = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_events / $per_page);
    
    // Get events
    $sql = "SELECT * FROM events WHERE $where ORDER BY event_date ASC, event_time ASC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $events = [];
    $total_pages = 0;
    $error_message = 'Failed to load events.';
}

include __DIR__ . '/includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="hero">
            <h2>Upcoming Events</h2>
            <p>Discover and register for exciting events happening near you</p>
        </div>
        
        <!-- Search Form -->
        <div class="search-box">
            <form method="GET" action="<?php echo SITE_URL; ?>/index.php" class="search-form">
                <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Events Grid -->
        <div class="events-grid">
            <?php if (empty($events)): ?>
                <div class="no-events">
                    <p>No upcoming events found. <?php echo !empty($search) ? 'Try a different search term.' : 'Check back later!'; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <?php if (!empty($event['image'])): ?>
                            <div class="event-image">
                                <img src="<?php echo UPLOAD_URL . $event['image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="event-content">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-description"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</p>
                            <div class="event-meta">
                                <span class="event-date">📅 <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                <span class="event-time">🕐 <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                            </div>
                            <p class="event-venue">📍 <?php echo htmlspecialchars($event['venue']); ?></p>
                            <a href="<?php echo SITE_URL; ?>/event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                       class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

