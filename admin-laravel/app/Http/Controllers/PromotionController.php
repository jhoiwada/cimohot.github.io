<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        return view('promotions.index');
    }

    public function getAll()
    {
        $promotions = Promotion::orderByDesc('created_at')->get()->map(function ($promo) {
            return [
                'promo_id' => $promo->promo_id,
                'code' => $promo->code,
                'description' => $promo->description ?: '-',
                'discount_type' => $promo->discount_type,
                'discount_value' => (float)$promo->discount_value,
                'discount_formatted' => $promo->formatted_discount,
                'min_order' => (float)$promo->min_order,
                'min_order_formatted' => 'Rp ' . number_format($promo->min_order, 0, ',', '.'),
                'is_active' => (bool)$promo->is_active,
                'is_valid' => $promo->isValid(),
                'expires_at' => $promo->expires_at ? $promo->expires_at->format('Y-m-d') : null,
                'expires_at_formatted' => $promo->expires_at ? $promo->expires_at->format('d M Y') : 'Tanpa Batas',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'promotions' => $promotions,
                'total' => $promotions->count(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ], [
            'code.required' => 'Kode voucher promo wajib diisi.',
            'code.unique' => 'Kode voucher ini sudah digunakan. Harap gunakan kode unik lain.',
            'discount_value.required' => 'Nilai diskon wajib diisi.',
        ]);

        $promo = Promotion::create([
            'code' => strtoupper(trim($request->code)),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order' => $request->min_order ?: 0,
            'is_active' => true,
            'expires_at' => $request->expires_at ? $request->expires_at . ' 23:59:59' : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher promo ' . $promo->code . ' berhasil dibuat!',
            'data' => $promo
        ]);
    }

    public function update(Request $request, $id)
    {
        $promo = Promotion::find($id);
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code,' . $id . ',promo_id',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $promo->code = strtoupper(trim($request->code));
        $promo->description = $request->description;
        $promo->discount_type = $request->discount_type;
        $promo->discount_value = $request->discount_value;
        $promo->min_order = $request->min_order ?: 0;
        $promo->expires_at = $request->expires_at ? $request->expires_at . ' 23:59:59' : null;
        $promo->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher promo ' . $promo->code . ' berhasil diperbarui!',
            'data' => $promo
        ]);
    }

    public function toggleStatus($id)
    {
        $promo = Promotion::find($id);
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo tidak ditemukan.'
            ], 404);
        }

        $promo->is_active = !$promo->is_active;
        $promo->save();

        $statusStr = $promo->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success' => true,
            'message' => "Promo {$promo->code} berhasil {$statusStr}.",
            'is_active' => $promo->is_active
        ]);
    }

    public function destroy($id)
    {
        $promo = Promotion::find($id);
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo tidak ditemukan.'
            ], 404);
        }

        $code = $promo->code;
        $promo->delete();

        return response()->json([
            'success' => true,
            'message' => "Promo {$code} berhasil dihapus."
        ]);
    }
}
