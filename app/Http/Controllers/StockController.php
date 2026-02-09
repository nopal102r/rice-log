<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display a listing of the stocks.
     */
    public function index(): View
    {
        $allStocks = Stock::all();
        
        // Grouping logic
        $rawMaterials = $allStocks->filter(function ($stock) {
            return in_array($stock->name, ['gabah', 'beras_giling']);
        });

        $packedProducts = $allStocks->filter(function ($stock) {
            return str_contains($stock->name, 'packed');
        });

        $totalPackedQuantity = $packedProducts->sum('quantity');

        return view('boss.stock.index', compact('rawMaterials', 'packedProducts', 'totalPackedQuantity'));
    }
}
