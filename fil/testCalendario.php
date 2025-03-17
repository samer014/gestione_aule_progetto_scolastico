<!DOCTYPE html>
<html>
<head>
    <title>Example 1</title>
    <style>
        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3>Using PHP's Date Functions</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="month">Enter Month (1-12):</label>
        <input type="number" id="month" name="month" min="1" max="12" required>
        <label for="year">Enter Year:</label>
        <input type="number" id="year" name="year" min="1900" max="2100" required>
        <input type="submit" value="Show Calendar">
    </form>
    <br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $month = $_POST["month"];
        $year = $_POST["year"];
        $timestamp = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date("t", $timestamp);
        $firstDay = date("N", $timestamp);
        echo "<h3>Calendar for " . date("F Y", $timestamp) . "</h3>";
        echo "<table>";
        echo "<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
        $dayCount = 1;
        echo "<tr>";
        for ($i = 1; $i <= 7; $i++) {
            if ($i < $firstDay) {
                echo "<td></td>";
            } else {
                echo "<td>$dayCount</td>";
                $dayCount++;
            }
        }
        echo "</tr>";
        while ($dayCount <= $daysInMonth) {
            echo "<tr>";
            for ($i = 1; $i <= 7 && $dayCount <= $daysInMonth; $i++) {
                echo "<td>$dayCount</td>";
                $dayCount++;
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>