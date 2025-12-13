<?php
/**
 * ESC/POS Printer Test Script
 * Test connection to thermal printer on COM3
 * 
 * FilePrintConnector is used for serial ports (COM1, COM2, COM3, etc.)
 * WindowsPrintConnector is for printer queues/shares, NOT for COM ports
 */

require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

echo "=================================\n";
echo "ESC/POS Printer Test (COM3)\n";
echo "=================================\n\n";

echo "NOTE: For COM ports, use FilePrintConnector\n";
echo "      WindowsPrintConnector is for printer queues only\n\n";

// Test both COM3 formats
$portOptions = [
    "COM3",      // Standard format
    "COM3:",     // Alternative format with colon
];

foreach ($portOptions as $index => $port) {
    echo "--- Test #" . ($index + 1) . " ---\n";
    echo "Port format: $port\n\n";
    
    try {
        echo "[1] Attempting to connect to $port...\n";
        
        // Connect to serial port
        $connector = new FilePrintConnector($port);
        echo "[✓] Connected to $port\n\n";
        
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
        $printer->text("Connection: $port\n");
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
        
        // If successful, no need to try other formats
        echo "=================================\n";
        echo "SUCCESS - Using port format: $port\n";
        echo "=================================\n";
        exit(0);
        
    } catch(Exception $e) {
        echo "[✗] Failed with $port: " . $e->getMessage() . "\n\n";
        
        if ($index === count($portOptions) - 1) {
            // Last attempt failed
            echo "=================================\n";
            echo "ALL ATTEMPTS FAILED\n";
            echo "=================================\n\n";
            echo "Troubleshooting steps:\n";
            echo "1. Verify printer is connected to COM3\n";
            echo "   - Check Device Manager > Ports (COM & LPT)\n";
            echo "\n";
            echo "2. Configure COM3 settings in Device Manager:\n";
            echo "   - Right-click COM3 > Properties > Port Settings\n";
            echo "   - Baud rate: 9600\n";
            echo "   - Data bits: 8\n";
            echo "   - Parity: None\n";
            echo "   - Stop bits: 1\n";
            echo "   - Flow control: Hardware (RTS/CTS)\n";
            echo "\n";
            echo "3. Close other programs using COM3\n";
            echo "   - Your Python script\n";
            echo "   - Any terminal programs\n";
            echo "   - Device Manager (if port properties open)\n";
            echo "\n";
            echo "4. Run this script as Administrator\n";
            echo "   - Right-click Command Prompt > Run as Administrator\n";
            echo "   - Then run: php test-printer.php\n";
            echo "\n";
            echo "5. Test with mode command:\n";
            echo "   - Open Command Prompt as Administrator\n";
            echo "   - Run: mode COM3:9600,n,8,1\n";
            echo "   - Then try this script again\n";
            echo "\n";
            exit(1);
        }
        
        echo "Trying next format...\n\n";
    }
}
