<?php 
/**
 * TinyMCE Image Upload Handler - Movie Review System
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication (using your system's method)
requireAuth();

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF token (if your system uses it)
if (isset($_POST['csrf_token']) && function_exists('verifyCSRFToken')) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

// Define upload directory
$uploadDir = '../../assets/uploads/reviews/content-image/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Get file extension
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validate file type
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)]);
    exit;
}

// Set size limits (5MB for images)
$maxSize = 5 * 1024 * 1024;

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max: 5MB']);
    exit;
}

// Generate unique filename
$newFileName = 'content_tinymce_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
$uploadPath = $uploadDir . $newFileName;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    chmod($uploadPath, 0644);
    
    // Optional: Log to database if you have a media table
    try {
        if (class_exists('Database')) {
            $db = Database::getInstance();
            
            // Check if media table exists
            $tableExists = $db->fetchOne("SHOW TABLES LIKE 'media'");
            if ($tableExists) {
                $mediaData = [
                    'filename' => $newFileName,
                    'original_name' => $file['name'],
                    'file_path' => 'assets/uploads/reviews/content-image/' . $newFileName,
                    'file_size' => $file['size'],
                    'mime_type' => $file['type'],
                    'file_type' => 'image',
                    'uploaded_by' => 'admin', // or get current user ID if available
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $db->insert('media', $mediaData);
            }
        }
    } catch (Exception $e) {
        // Continue even if media logging fails
        error_log('Media logging failed: ' . $e->getMessage());
    }
    
    // Create absolute URL
    $absolutePath = SITE_URL . '/assets/uploads/reviews/content-image/' . $newFileName;
    
    // Verify file exists and is accessible
    if (file_exists($uploadPath)) {
        // Return success response for TinyMCE
        echo json_encode([
            'success' => true, 
            'location' => $absolutePath,
            'file' => $newFileName
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'File uploaded but not accessible']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>