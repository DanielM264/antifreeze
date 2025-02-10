<?php
// Define allowed file extensions and maximum file size (in bytes)
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', "wav", "mp3", "mp4", "avi", "flv", "mov", "wmv", "webm", "mkv", "zip", "rar", "7z", "tar", "gz", "xz"];
$maxFileSize = 100 * 1024 * 1024; // 100 MB

$uploadDir = 'uploads/';

// Ensure the uploads directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

    // Get file details
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName    = $_FILES['file']['name'];
    $fileSize    = $_FILES['file']['size'];
    $fileType    = $_FILES['file']['type'];

    // Check file size limit
    if ($fileSize > $maxFileSize) {
        echo json_encode(['error' => 'File size exceeds the maximum allowed size of 2MB.']);
        exit;
    }

    // Extract file extension and convert it to lowercase
    $fileNameCmps = explode(".", $fileName);
    if(count($fileNameCmps) < 2){
        echo json_encode(['error' => 'Invalid file name.']);
        exit;
    }
    $fileExtension = strtolower(end($fileNameCmps));

    // Check if file extension is allowed
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['error' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)]);
        exit;
    }

    // Optionally, check MIME type for additional security (you can maintain an array of allowed MIME types)
    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf'
    ];
    if (!in_array($fileType, $allowedMimeTypes)) {
        echo json_encode(['error' => 'MIME type not allowed.']);
        exit;
    }

    // Generate a new unique file name to avoid overwriting existing files
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Construct the public URL of the uploaded file.
        // Adjust this URL to match your domain and file path.
        $fileLink = 'https://yourdomain.com/' . $destPath;
        echo json_encode(['fileLink' => $fileLink]);
    } else {
        echo json_encode(['error' => 'Error moving the uploaded file.']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded or an upload error occurred.']);
}
?>
