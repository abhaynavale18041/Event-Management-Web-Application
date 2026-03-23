<?php
/**
 * Event details page with registration form
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth.php';

$event_id = intval($_GET['id'] ?? 0);
$error = '';
$success = '';
$page_title = 'Event Details';

if (!$event_id) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

try {
    $db = getDB();
    
    // Get event details
    $stmt = $db->prepare("SELECT e.*, u.username as created_by_name FROM events e 
                         JOIN users u ON e.created_by = u.id 
                         WHERE e.id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
    
    // Get registration count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $registration_count = $stmt->fetch()['count'];
    
    // Handle registration form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_event'])) {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!verifyCSRFToken($csrf_token)) {
            $error = 'Invalid security token. Please try again.';
        } elseif (empty($name) || empty($email) || empty($phone)) {
            $error = 'All fields are required.';
        } elseif (!validateEmail($email)) {
            $error = 'Invalid email address.';
        } else {
            // Check if already registered (simple check by email for same event)
            $stmt = $db->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND email = ?");
            $stmt->execute([$event_id, $email]);
            
            if ($stmt->fetch()) {
                $error = 'You have already registered for this event.';
            } else {
                // Insert registration
                $stmt = $db->prepare("INSERT INTO event_registrations (event_id, name, email, phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$event_id, $name, $email, $phone]);
                $success = 'Registration successful! We look forward to seeing you at the event.';
            }
        }
    }
    
} catch (PDOException $e) {
    $error = 'Failed to load event details.';
}

$page_title = htmlspecialchars($event['title']);

include __DIR__ . '/includes/header.php';
?>

<main class="main">
    <div class="container">
        <a href="<?php echo SITE_URL; ?>/index.php" class="back-link">← Back to Events</a>
        
        <div class="event-details">
            <?php if (!empty($event['image'])): ?>
                <div class="event-detail-image">
                    <img src="<?php echo UPLOAD_URL . $event['image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="event-detail-content">
                <h1><?php echo htmlspecialchars($event['title']); ?></h1>
                
                <div class="event-info">
                    <div class="info-item">
                        <strong>📅 Date:</strong> <?php echo date('l, F d, Y', strtotime($event['event_date'])); ?>
                    </div>
                    <div class="info-item">
                        <strong>🕐 Time:</strong> <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                    </div>
                    <div class="info-item">
                        <strong>📍 Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                    </div>
                    <?php if ($event['max_capacity'] > 0): ?>
                        <div class="info-item">
                            <strong>👥 Capacity:</strong> <?php echo $registration_count; ?>/<?php echo $event['max_capacity']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="event-description-full">
                    <h3>About This Event</h3>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>
                
                <div class="registration-form-section">
                    <h3>Register for this Event</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($event['max_capacity'] > 0 && $registration_count >= $event['max_capacity']): ?>
                        <div class="alert alert-warning">This event is now full.</div>
                    <?php else: ?>
                        <form method="POST" class="registration-form">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            
                            <button type="submit" name="register_event" class="btn btn-primary">Register Now</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

