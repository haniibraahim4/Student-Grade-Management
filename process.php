
<?php


require_once "functions.php";


$name = trim($_POST["student_name"] ?? "");
$id = trim($_POST["student_id"] ?? "");
$gender = $_POST["gender"] ?? "";
$faculty = $_POST["faculty"] ?? "";

$php = $_POST["php_mark"] ?? "";
$database = $_POST["database_mark"] ?? "";
$networking = $_POST["networking_mark"] ?? "";

$errors = "";


if ($name === "") {
    $errors .= "Student name is required.<br>";
}

if ($id === "") {
    $errors .= "Student ID is required.<br>";
}


if ($gender === "") {
    $errors .= "Please select gender.<br>";
}

if ($faculty === "") {
    $errors .= "Please select faculty.<br>";
}


if ($php === "" || $database === "" || $networking === "") {
    $errors .= "All three subject marks are required.<br>";
} else {


    if (!is_numeric($php) || $php < 0 || $php > 100) {
        $errors .= "PHP mark must be between 0 and 100.<br>";
    }

    if (!is_numeric($database) || $database < 0 || $database > 100) {
        $errors .= "Database mark must be between 0 and 100.<br>";
    }

    if (!is_numeric($networking) || $networking < 0 || $networking > 100) {
        $errors .= "Networking mark must be between 0 and 100.<br>";
    }
}


if ($errors !== "") {

    echo "<h2>Validation Errors</h2>";
    echo $errors;
    echo '<a href="index.html">Go Back</a>';

} else {

  
    $php = (float)$php;
    $database = (float)$database;
    $networking = (float)$networking;

    $total = calculateTotal($php, $database, $networking);
    $average = calculateAverage($total);
    $grade = calculateGrade($average);
    $result = checkResult($average);

 
    $uploadStatus = "Upload failed";
    $certificateName = "None";
    $fileType = "None";
    $fileSize = 0;

    if (isset($_FILES["certificate"]) &&
        $_FILES["certificate"]["error"] === UPLOAD_ERR_OK) {

        $originalName = basename($_FILES["certificate"]["name"]);
        $tmpName = $_FILES["certificate"]["tmp_name"];
        $fileType = $_FILES["certificate"]["type"];
        $fileSize = $_FILES["certificate"]["size"];

       
        if (!is_dir("uploads")) {
            mkdir("uploads", 0755, true);
        }

       
        if ($fileSize > 2 * 1024 * 1024) {

            $uploadStatus = "File exceeds 2 MB";

        } elseif ($originalName === "" ||
                  $originalName === "." ||
                  $originalName === "..") {

            $uploadStatus = "Invalid file name";

        } elseif (file_exists("uploads/" . $originalName)) {

           
            $uploadStatus = "File already exists";

        } elseif (move_uploaded_file(
            $tmpName,
            "uploads/" . $originalName
        )) {

            $uploadStatus = "Upload successful";
            $certificateName = $originalName;

        } else {

            $uploadStatus = "Unable to save file";
        }

    } elseif (isset($_FILES["certificate"]) &&
              $_FILES["certificate"]["error"] === UPLOAD_ERR_NO_FILE) {

        $uploadStatus = "No certificate selected";

    } else {

        $uploadStatus = "File upload error";
    }


    echo "<h1>Student Examination Results</h1>";

    echo "<table border='1' cellpadding='8' cellspacing='0'>";

    echo "<tr><th>Information</th><th>Value</th></tr>";

    echo "<tr><td>Student Full Name</td><td>"
        . htmlspecialchars($name) . "</td></tr>";

    echo "<tr><td>Student ID</td><td>"
        . htmlspecialchars($id) . "</td></tr>";

    echo "<tr><td>Gender</td><td>"
        . htmlspecialchars($gender) . "</td></tr>";

    echo "<tr><td>Faculty</td><td>"
        . htmlspecialchars($faculty) . "</td></tr>";

    echo "<tr><td>PHP Mark</td><td>$php</td></tr>";

    echo "<tr><td>Database Mark</td><td>$database</td></tr>";

    echo "<tr><td>Networking Mark</td><td>$networking</td></tr>";

    echo "<tr><td>Total Marks</td><td>$total</td></tr>";

    echo "<tr><td>Average Marks</td><td>"
        . number_format($average, 2) . "</td></tr>";

    echo "<tr><td>Grade</td><td>$grade</td></tr>";

    echo "<tr><td>Pass or Fail</td><td>$result</td></tr>";

    echo "<tr><td>Certificate Name</td><td>"
        . htmlspecialchars($certificateName) . "</td></tr>";

    echo "<tr><td>File Type</td><td>"
        . htmlspecialchars($fileType) . "</td></tr>";

    echo "<tr><td>File Size</td><td>"
        . number_format($fileSize / 1024, 2) . " KB</td></tr>";

    echo "<tr><td>Upload Status</td><td>"
        . htmlspecialchars($uploadStatus) . "</td></tr>";

    echo "</table>";

    echo '<br><a href="index.html">Back to Form</a>';
}

?>
