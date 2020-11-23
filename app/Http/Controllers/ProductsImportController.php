<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Rules\MaxSizeRule;
use Illuminate\Http\Request;

class ProductsImportController extends Controller
{
    public function show()
    {
        return view('import.products');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xls,xlsx', new MaxSizeRule()],
        ]);

        $file = $request->file('file')->store('import');

        $import = new ProductsImport;
        $import->import($file);

        if ($import->failures()->isNotEmpty()) {
            return back()->withFailures($import->failures())->withStatus('Number of successful imported rows : ' . $import->getSuccessfulRowCount());
        }

        return back()->withStatus('Number of successful imported rows : ' . $import->getSuccessfulRowCount());
    }
}
