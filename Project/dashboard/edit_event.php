<?php
/**
 * Edit event
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

requireAdmin();

$page_title = 'Edit Event';
$event_id = intval($_GET['id'] ?? 0);
$error = '';
$success = '';

if (!$event_id) {
    header('Location: ' . SITE_URL . '/dashboard/events.php');
    exit;
}

try {
    $db = getDB();
    
    // Get event
    $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: ' . SITE_URL . '/dashboard/events.php');
        exit;
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $event_date = $_POST['event_date'] ?? '';
        $event_time = $_POST['event_time'] ?? '';
        $venue = sanitize($_POST['venue'] ?? '');
        $max_capacity = intval($_POST['max_capacity'] ?? 0);
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        if (!verifyCSRFToken($csrf_token)) {
            $error = 'Invalid security token.';
        } elseif (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($venue)) {
            $error = 'All required fields must be filled.';
        } else {
            $image_name = $event['image'];
            
            // Handle file upload if new file is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $file = $_FILES['image'];
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
                    $error = 'Invalid file type.';
                } elseif ($file['size'] > MAX_FILE_SIZE) {
                    $error = 'File size exceeds maximum limit.';
                } else {
                    // Delete old image if exists
                    if (!empty($event['image'])) {
                        $old_file = UPLOAD_DIR . $event['image'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    
                    $image_name = uniqid('event_') . '.' . $file_ext;
                    $upload_path = UPLOAD_DIR . $image_name;
                    
                    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $error = 'Failed to upload file.';
                    }
                }
            }
            
            if (!$error) {
                $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, venue = ?, image = ?, max_capacity = ? WHERE id = ?");
                $stmt->execute([$title, $description, $event_date, $event_time, $venue, $image_name, $max_capacity, $event_id]);
                
                $success = 'Event updated successfully!';
                $event = array_merge($event, [
                    'title' => $title,
                    'description' => $description,
                    'event_date' => $event_date,
                    'event_time' => $event_time,
                    'venue' => $venue,
                    'max_capacity' => $max_capacity,
                    'image' => $image_name
                ]);
            }
        }
    }
    
} catch (PDOException $e) {
    $error = 'Failed to load event.';
}

include __DIR__ . '/../includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Edit Event</h2>
            <a href="<?php echo SITE_URL; ?>/dashboard/events.php" class="back-link">← Back to Events</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="event-form">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="title">Event Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($event['title']); ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" required rows="5"><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="event_date">Event Date <span class="required">*</span></label>
                    <input type="date" id="event_date" name="event_date" required value="<?php echo $event['event_date']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="event_time">Event Time <span class="required">*</span></label>
                    <input type="time" id="event_time" name="event_time" required value="<?php echo $event['event_time']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="venue">Venue <span class="required">*</span></label>
                <input type="text" id="venue" name="venue" required value="<?php echo htmlspecialchars($event['venue']); ?>">
            </div>
            
            <div class="form-group">
                <label for="max_capacity">Max Capacity</label>
                <input type="number" id="max_capacity" name="max_capacity" min="0" value="<?php echo $event['max_capacity']; ?>">
            </div>
            
            <?php if (!empty($event['image'])): ?>
                <div class="form-group">
                    <label>Current Image</label>
                    <img src="<?php echo UPLOAD_URL . $event['image']; ?>" alt="Current image" style="max-width: 200px;">
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="image">Change Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Allowed formats: JPG, PNG, GIF (Max 5MB)</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

