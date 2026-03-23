<?php
/**
 * Contact page
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$page_title = 'Contact Us';
$message = '';

// Handle form submission (simple contact form - you can extend this)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message_text = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'All fields are required.';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email address.';
    } else {
        $message = 'Thank you for your message! We will get back to you soon.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="main">
    <div class="container">
        <div class="page-header">
            <h2>Contact Us</h2>
            <p>Get in touch with us for any questions or support</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-info">
                <div class="info-box">
                    <h3>📍 Address</h3>
                    <p>123 Event Street<br>City, State 12345<br>United States</p>
                </div>
                
                <div class="info-box">
                    <h3>📞 Phone</h3>
                    <p>Phone: (555) 123-4567<br>Fax: (555) 123-4568</p>
                </div>
                
                <div class="info-box">
                    <h3>✉️ Email</h3>
                    <p>info@eventmanager.com<br>support@eventmanager.com</p>
                </div>
            </div>
            
            <div class="contact-form-container">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php elseif (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

