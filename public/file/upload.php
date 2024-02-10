<?php
$uploadDirectory = 'C:/Data/applications/opsadmin/public/assets/img/'; // Replace with your desired destination folder on Server B

// check old image
$files = scandir($uploadDirectory);

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        if ($file == $_FILES['create_img_profile']['name']) {
            // rename file
            $oldPath = $uploadDirectory . $directory . $file;
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $newFilename = pathinfo($file, PATHINFO_FILENAME) . '-old-' . date('YmdHis') . '.' . $ext;
            $newPath = $uploadDirectory . $directory . $newFilename;

            // use rename for change filename
            if (!rename($oldPath, $newPath)) {
                echo 'Failed rename file ' . $file . " | " . 'Error: ' . error_get_last()['message'] . "\n";
                die();
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['create_img_profile']) && $_FILES['create_img_profile']['error'] === UPLOAD_ERR_OK) {
        $tempPath = $_FILES['create_img_profile']['tmp_name']; // Path sementara file yang diunggah
        $destinationPath = $uploadDirectory . $_FILES['create_img_profile']['name']; // Path file tujuan

        // Pindahkan file yang diunggah ke direktori tujuan
        if (move_uploaded_file($tempPath, $destinationPath)) {
            chmod($destinationPath, 0777); // set permissions
            // echo 'File berhasil diunggah dan disimpan di server B.';
            header('Location: https://127.0.0.1:8002/user');
            die();
        } else {
            echo 'Gagal menyimpan file di server B.';
            die();
        }
    }
    // else {
    //     echo 'Terjadi kesalahan saat mengunggah file.';
    //     die();
    // }
}
