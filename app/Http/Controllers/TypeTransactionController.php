<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeTransaction;

class TypeTransactionController extends Controller
{
    public function index()
    {
        $typeTransactions = TypeTransaction::all();
        return response()->json($typeTransactions);
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required',
            ],
            [
                'name.required' => 'Name is required',
            ]);
            $typeTransaction = TypeTransaction::create([
                'name' => $request->name,
            ]);
            return response()->json($typeTransaction);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Type Transaction registration failed! '.$e->getMessage()
            ], 409);
        }
    }

    public function show($id)
    {
        $typeTransaction = TypeTransaction::find($id);
        return response()->json($typeTransaction);
    }

    public function update(Request $request, $id)
    {
        try{
            $request->validate([
                'name' => 'required',
            ],
            [
                'name.required' => 'Name is required',
            ]);
            $typeTransaction = TypeTransaction::find($id);
            $typeTransaction->update([
                'name' => $request->name,
            ]);
            return response()->json($typeTransaction);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Type Transaction update failed! '.$e->getMessage()
            ], 409);
        }
    }

    public function destroy($id)
    {
        try{
            $typeTransaction = TypeTransaction::find($id);
            $typeTransaction->delete();
            return response()->json([
                'message' => 'Type Transaction deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Type Transaction delete failed! '.$e->getMessage()
            ], 409);
        }
    }
    
}
