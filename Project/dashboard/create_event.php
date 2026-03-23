<?php
/**
 * Create new event
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

requireAdmin();

$page_title = 'Create Event';
$error = '';
$success = '';
$validation_errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $venue = sanitize($_POST['venue'] ?? '');
    $max_capacity = intval($_POST['max_capacity'] ?? 0);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($venue)) {
        $error = 'All required fields must be filled.';
    } else {
        // Handle file upload
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file = $_FILES['image'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
                $error = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            } elseif ($file['size'] > MAX_FILE_SIZE) {
                $error = 'File size exceeds maximum limit of 5MB.';
            } else {
                $image_name = uniqid('event_') . '.' . $file_ext;
                $upload_path = UPLOAD_DIR . $image_name;
                
                if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $error = 'Failed to upload file.';
                }
            }
        }
        
        if (!$error) {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO events (title, description, event_date, event_time, venue, image, max_capacity, created_by) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $event_date, $event_time, $venue, $image_name, $max_capacity, $_SESSION['user_id']]);
                
                $success = 'Event created successfully!';
                // Clear form
                $title = $description = $event_date = $event_time = $venue = '';
                $max_capacity = 0;
            } catch (PDOException $e) {
                $error = 'Failed to create event. Please try again.';
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Create New Event</h2>
            <a href="<?php echo SITE_URL; ?>/dashboard/index.php" class="back-link">← Back to Dashboard</a>
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
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" required rows="5"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="event_date">Event Date <span class="required">*</span></label>
                    <input type="date" id="event_date" name="event_date" required value="<?php echo htmlspecialchars($event_date); ?>">
                </div>
                
                <div class="form-group">
                    <label for="event_time">Event Time <span class="required">*</span></label>
                    <input type="time" id="event_time" name="event_time" required value="<?php echo htmlspecialchars($event_time); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="venue">Venue <span class="required">*</span></label>
                <input type="text" id="venue" name="venue" required value="<?php echo htmlspecialchars($venue); ?>">
            </div>
            
            <div class="form-group">
                <label for="max_capacity">Max Capacity (optional)</label>
                <input type="number" id="max_capacity" name="max_capacity" min="0" value="<?php echo $max_capacity; ?>">
            </div>
            
            <div class="form-group">
                <label for="image">Event Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Allowed formats: JPG, PNG, GIF (Max 5MB)</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

