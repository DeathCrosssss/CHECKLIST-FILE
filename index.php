<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student and Course Information</title>
   
    <?php
    
    $changeCSS = "styles/styles1.css"; 
    if (isset($_GET['css_button']) && $_GET['css_button'] === 'styles2') {
        $changeCSS = "styles/styles2.css"; 
    }
    ?>
    <link rel="stylesheet" href="<?php echo $changeCSS; ?>">
    <style>
        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <div class="header-content">
        <div class="university-info">
            <div class="university-image">
                <img src="images/cavsuLogo.png" alt="University Image">
            </div>
            <div>
                <h1 class="university-title">CAVITE STATE UNIVERSITY OF BACOOR</h1>
                <p class="program-title">BACHELOR OF SCIENCE IN COMPUTER SCIENCE</p>
            </div>
        </div>
    </div>
</header>


<div class="container">




<?php
  
    require_once("includes/db_connection.php");

  
    $sql_students = "SELECT * FROM students";
    $result_students = $conn->query($sql_students);

 if ($result_students->num_rows > 0) {
    while ($row = $result_students->fetch_assoc()) {
        echo "<div class='student-info'>";
        echo "<div class='left-info'>";
        echo "<p><strong>Name of Student:</strong> <span class='underline'>" . $row["name"] . "</span></p>";
        echo "<p><strong>Student Number:</strong> <span class='underline'>" . $row["student_id"] . "</span></p>";
        echo "<p><strong>Address:</strong> <span class='underline'>" . $row["address"] . "</span></p>";
        echo "</div>";
        echo "<div class='right-info'>";
        echo "<p><strong>Date of Admission:</strong> <span class='underline'>" . $row["date_of_admission"] . "</p>";
        echo "<p><strong>Contact Number:</strong> <span class='underline'>" . $row["contact_number"] . "</span></p>";
        echo "<p><strong>Adviser:</strong> <span class='underline'>" . $row["adviser"] . "</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='student-table'>";
    echo "<h2>Student Information</h2>";
    echo "0 results";
    echo "</div>";
}


   
    $course_ranges = array(
        array("1st Year 1st Semester", 1, 9),
        array("1st Year 2nd Semester", 10, 17),
        array("2nd Year 1st Semester", 18, 25),
        array("2nd Year 2nd Semester", 26, 33),
        array("3rd Year 1st Semester", 34, 40),
        array("3rd Year 2nd Semester", 41, 47),
        array("Mid Year Courses", 48, 48),
        array("4th Year 1st Semester", 49, 53),
        array("4th Year 2nd Semester", 54, 57)
    );

  
    echo "<div class='course-table'>";
    echo "<div class='search-box'>";
    echo "<label for='course-search'>Search Course:</label>";
    echo "<input type='text' id='course-search' placeholder='Enter course code or title'>";
    echo "</div>";

    echo "<div class='filter-box'>";
    echo "<label for='filter_year'>Year:</label>";
    echo "<select name='filter_year' id='filter_year'>";
    echo "<option value='all'>All</option>";
    echo "<option value='1st'>1st Year</option>";
    echo "<option value='2nd'>2nd Year</option>";
    echo "<option value='3rd'>3rd Year</option>";
    echo "<option value='Mid Year'>Mid Year</option>";
    echo "<option value='4th'>4th Year</option>";
    echo "</select>";
    
    echo "<label for='filter_semester'>Semester:</label>";
    echo "<select name='filter_semester' id='filter_semester'>";
    echo "<option value='all'>All</option>";
    echo "<option value='1st'>1st Semester</option>";
    echo "<option value='2nd'>2nd Semester</option>";
    echo "</select>";
    
    


        
    

    foreach ($course_ranges as $range) {
        list($semester_title, $start, $end) = $range;
        $courses = fetch_courses_by_semester($conn, $semester_title, $start, $end);
        echo "<div class='table-container'>";
        echo "<h2>$semester_title</h2>";
        echo "<table>";
        echo "<tr><th>Course ID</th><th>Course Title</th><th>Credit Units (Lecture)</th>
        <th>Credit Units (Lab)</th><th>Contact Hours (Lecture)</th><th>Contact Hours (Lab)</th>
        <th>Prerequisite</th><th>Grade Year</th><th>Academic Year</th><th>Semester</th>
        <th>Final Grade</th><th>Professor</th></tr>";
        foreach ($courses as $row) {
            echo "<tr>";
            echo "<td>" . ($row["course_id"] ?: "") . "</td>";
            echo "<td>" . $row["course_title"] . "</td>";
            echo "<td>" . $row["credit_unit_lec"] . "</td>";
            echo "<td>" . ($row["credit_unit_lab"] ?: "") . "</td>";
            echo "<td>" . $row["contact_hours_lec"] . "</td>";
            echo "<td>" . ($row["contact_hours_lab"] ?: "") . "</td>";
            echo "<td>" . ($row["pre_requisite"] ?: "") . "</td>";
            echo "<td>" . $row["grade_year"] . "</td>";
            echo "<td>" . $row["academic_year"] . "</td>";
            echo "<td>" . $row["semester"] . "</td>";
            echo "<td>" . (isset($row["grade"]) ? $row["grade"] : "") . "</td>";
            echo "<td>" . (isset($row["professor"]) ? $row["professor"] : "") . "</td>";
            echo "</tr>";
        }
        
        
        $total_credit_unit_lec = array_sum(array_column($courses, 'credit_unit_lec'));
        $total_credit_unit_lab = array_sum(array_column($courses, 'credit_unit_lab'));
        $total_contact_hours_lec = array_sum(array_column($courses, 'contact_hours_lec'));
        $total_contact_hours_lab = array_sum(array_column($courses, 'contact_hours_lab'));

      
        
        echo "<tr>";
        echo "<td colspan='2'>Total</td>";
        echo "<td>$total_credit_unit_lec</td>";
        echo "<td>$total_credit_unit_lab</td>";
        echo "<td>$total_contact_hours_lec</td>";
        echo "<td>$total_contact_hours_lab</td>";
        echo "<td></td>"; 
        echo "<td colspan='5'>Adviser:__________________________________</td>"; 
        echo "</tr>";

        
        echo "</table>";
        echo "</div>";
    }

    echo "</div>";

    $conn->close();


    function fetch_courses_by_semester($conn, $semester_title, $start_id, $end_id) {
        $sql = "SELECT courses.course_id, courses.course_title, courses.credit_unit_lec, 
        courses.credit_unit_lab, courses.contact_hours_lec, courses.contact_hours_lab, 
        courses.pre_requisite, courses.grade_year, courses.academic_year, courses.semester, 
        grades.grade, student_enroll.professor
        FROM courses
        LEFT JOIN grades ON courses.course_id = grades.course_id
        LEFT JOIN student_enroll ON courses.course_id = student_enroll.course_id
        WHERE courses.id BETWEEN $start_id AND $end_id";
        $result = $conn->query($sql);

        if ($result === false) {
            die("Query Failed: " . $conn->error);
        }

        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        return $courses;
    }

?>

<form action="" method="GET" style="display: inline;">
    <button type="submit" name="css_button" value="<?php echo ($changeCSS === "styles/styles1.css") ? "styles2" : "styles1"; ?>">
        Background 
    </button>
</form>

<script src="js/script.js"></script>

</body>
</html>
