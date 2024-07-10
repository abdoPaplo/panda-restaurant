<?php
date_default_timezone_set('Africa/Cairo');
// Include PhpSpreadsheet library
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Include database connection
include 'db_connection.php';

// Default type is current_month if not specified
$type = isset($_GET['type']) ? $_GET['type'] : 'current_month';

// Get the current year
$currentYear = date('Y');

// Set initial SQL query and period label
$sql = "";
$period = "";

// Check if a specific month is selected from the dropdown
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $selectedMonth = $_GET['month'];
    $sql = "SELECT * FROM order_items 
            WHERE MONTH(order_date) = '$selectedMonth' 
            AND YEAR(order_date) = $currentYear 
            ORDER BY order_id";
    $period = date("F", mktime(0, 0, 0, $selectedMonth, 10)); // Get month name
} else {
    switch ($type) {
        case 'current_year':
            // Calculate entire current year's expenses
            $sql = "SELECT * FROM order_items 
                    WHERE YEAR(order_date) = YEAR(CURRENT_DATE) 
                    ORDER BY order_id";
            $period = "Current Year";
            break;
        case 'last_year':
            // Calculate entire last year's expenses
            $sql = "SELECT * FROM order_items 
                    WHERE YEAR(order_date) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR) 
                    ORDER BY order_id";
            $period = "Last Year";
            break;
        case 'current_month':
        default:
            // Calculate current month's expenses
            $sql = "SELECT * FROM order_items 
                    WHERE MONTH(order_date) = MONTH(CURRENT_DATE) 
                    AND YEAR(order_date) = YEAR(CURRENT_DATE) 
                    ORDER BY order_id";
            $period = "Current Month";
            break;
    }
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();

    // Set document properties
    $spreadsheet->getProperties()->setCreator("Your Company")
                                 ->setLastModifiedBy("Your Company")
                                 ->setTitle("Expenses Report")
                                 ->setSubject("Expenses Report")
                                 ->setDescription("Report of order expenses for $period.")
                                 ->setKeywords("office PhpSpreadsheet php")
                                 ->setCategory("Report");

    // Add headers to the spreadsheet
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Order ID')
          ->setCellValue('B1', 'Item Name')
          ->setCellValue('C1', 'Item Price')
          ->setCellValue('D1', 'Quantity')
          ->setCellValue('E1', 'Total Price')
          ->setCellValue('F1', 'Order Date')
          ->setCellValue('G1', 'Ready Time')
          ->setCellValue('H1', 'Served Time')
          ->setCellValue('I1', 'Status');

    // Populate data from the database
    $rowNumber = 2;
    $totalExpenses = 0;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValueExplicit('A' . $rowNumber, $row["order_id"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
              ->setCellValueExplicit('B' . $rowNumber, $row["item_name"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
              ->setCellValueExplicit('C' . $rowNumber, $row["item_price"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
              ->setCellValueExplicit('D' . $rowNumber, $row["quantity"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
              ->setCellValueExplicit('E' . $rowNumber, $row["total_price"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
              ->setCellValueExplicit('F' . $rowNumber, $row["order_date"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
              ->setCellValueExplicit('G' . $rowNumber, $row["ready_time"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
              ->setCellValueExplicit('H' . $rowNumber, $row["served_time"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
              ->setCellValueExplicit('I' . $rowNumber, $row["status"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $totalExpenses += $row["total_price"];
        $rowNumber++;
    }

    // Add total expenses at the bottom
    $sheet->setCellValue('D' . $rowNumber, 'Total Expenses')
          ->setCellValue('E' . $rowNumber, $totalExpenses);

    // Rename worksheet
    $sheet->setTitle('Expenses');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="expenses_report_' . strtolower(str_replace(' ', '_', $period)) . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1'); // If you're serving to IE 9, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} else {
    echo "No orders found for $period.";
}

$conn->close();
?>
