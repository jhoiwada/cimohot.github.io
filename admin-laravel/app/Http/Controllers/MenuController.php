<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        return view('menu.index');
    }

    public function getAll(Request $request)
    {
        $query = Menu::query()->orderBy('category')->orderBy('name');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $menus = $query->get()->map(function ($menu) {
            return [
                'menu_id' => $menu->menu_id,
                'name' => $menu->name,
                'description' => $menu->description ?: '',
                'price' => (float)$menu->price,
                'price_formatted' => 'Rp ' . number_format($menu->price, 0, ',', '.'),
                'category' => $menu->category,
                'image' => $menu->image,
                'image_url' => $menu->image_url,
                'rating' => (float)$menu->rating,
                'stock' => (int)$menu->stock,
            ];
        });

        // Kategori unik untuk dropdown filter
        $categories = Menu::select('category')->distinct()->pluck('category');

        return response()->json([
            'success' => true,
            'data' => [
                'menus' => $menus,
                'categories' => $categories,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'category.required' => 'Kategori menu wajib dipilih.',
            'image.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'image.max' => 'Ukuran gambar maksimal 3MB.',
        ]);

        $imagePath = 'assets/images/default-food.png';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('menu-images', $filename, 'public');
            $imagePath = 'storage/' . $path;
        }

        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'stock' => $request->stock,
            'image' => $imagePath,
            'rating' => 5.0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu baru "' . $menu->name . '" berhasil ditambahkan!',
            'data' => $menu
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->category = $request->category;
        $menu->stock = $request->stock;
        $menu->description = $request->description;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('menu-images', $filename, 'public');
            $menu->image = 'storage/' . $path;
        }

        $menu->save();

        return response()->json([
            'success' => true,
            'message' => 'Menu "' . $menu->name . '" berhasil diperbarui!',
            'data' => $menu
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }

        $menuName = $menu->name;
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu "' . $menuName . '" berhasil dihapus.'
        ]);
    }
}
