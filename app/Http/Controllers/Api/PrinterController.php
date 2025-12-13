<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Exception;

class PrinterController extends Controller
{
    public function print(string $id)
    {
        try {
            $order = Order::with('orderItems')->findOrFail($id);

            // Connect to COM3 Printer
            // Note: This requires the printer to be shared or accessible as a Windows printer,
            // OR if strictly using Serial COM port, we might need FilePrintConnector("COM3").
            // The user example python code used Serial("COM3"). In PHP on Windows,
            // direct COM port access can be tricky.
            // Often FilePrintConnector("COM3") works if permissions allow.
            // Let's try FilePrintConnector since the user specifically mentioned COM3 serial usage.
            // If that fails, WindowsPrintConnector with a share name is the alternative.
            // Given the Python example usage of `Serial(devfile="COM3")`, FilePrintConnector("COM3") is the closest PHP equivalent.

            $connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector('COM3');
            $printer = new Printer($connector);

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("RESTAURANT POS\n");
            $printer->text("Jl. Contoh No. 123\n");
            $printer->text("================================\n");

            // Order Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Order No : ' . $order->id . "\n");
            $printer->text('Date     : ' . $order->order_date->format('Y-m-d H:i') . "\n");
            $printer->text("--------------------------------\n");

            // Items
            foreach ($order->orderItems as $item) {
                $line = sprintf("%-18.18s %3dx %8s\n", $item->menu_name, $item->quantity, number_format($item->subtotal, 0, ',', '.'));
                $printer->text($line);
            }

            $printer->text("--------------------------------\n");

            // Totals
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text('Subtotal: ' . number_format($order->subtotal, 0, ',', '.') . "\n");
            $printer->text('Tax (10%): ' . number_format($order->tax_amount, 0, ',', '.') . "\n");
            $printer->setEmphasis(true);
            $printer->text('TOTAL: ' . number_format($order->total_amount, 0, ',', '.') . "\n");
            $printer->setEmphasis(false);

            // Footer
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for your visit!\n");
            $printer->cut();

            $printer->close();

            return response()->json(['message' => 'Printed successfully']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Printing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
