<?php
/**
 * ESC/POS Printer Test Script
 * Test connection to thermal printer on COM3
 */

require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

echo "=================================\n";
echo "ESC/POS Printer Test (COM3)\n";
echo "=================================\n\n";

try {
    echo "[1] Attempting to connect to COM3...\n";
    
    // Connect to COM3 serial port
    $connector = new FilePrintConnector("COM3");
    echo "[✓] Connected to COM3\n\n";
    
    // Create printer instance
    $printer = new Printer($connector);
    echo "[2] Printer instance created\n\n";
    
    // Print test content
    echo "[3] Sending test data to printer...\n";
    
    $printer->initialize();
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->text("PRINTER TEST\n");
    $printer->setEmphasis(false);
    $printer->text("================================\n\n");
    
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Connection: COM3\n");
    $printer->text("Status: SUCCESS\n");
    $printer->text("Date: " . date('Y-m-d H:i:s') . "\n");
    $printer->text("\n");
    
    // Test line width (32 chars like your Python config)
    $printer->text("--------------------------------\n");
    $printer->text("Line Width Test (32 chars):\n");
    $printer->text("12345678901234567890123456789012\n");
    $printer->text("--------------------------------\n\n");
    
    // Test formatting
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Test completed successfully!\n\n");
    
    // Cut paper
    $printer->cut();
    
    // Close connection
    $printer->close();
    
    echo "[✓] Print job sent successfully!\n";
    echo "[✓] Check your printer for output\n\n";
    
} catch(Exception $e) {
    echo "[✗] ERROR: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting steps:\n";
    echo "1. Verify printer is connected to COM3\n";
    echo "2. Check COM3 settings in Device Manager:\n";
    echo "   - Baud rate: 9600\n";
    echo "   - Data bits: 8\n";
    echo "   - Parity: None\n";
    echo "   - Stop bits: 1\n";
    echo "   - Flow control: Hardware\n";
    echo "3. Ensure no other program is using COM3\n";
    echo "4. Run this script as Administrator\n\n";
    exit(1);
}

echo "=================================\n";
echo "Test completed\n";
echo "=================================\n";
