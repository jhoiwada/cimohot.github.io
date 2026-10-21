<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_customer_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Cimohot');
    }

    public function test_customer_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
    }

    public function test_admin_login_redirects_to_unified_login(): void
    {
        $response = $this->get('/admin/login');
        $response->assertRedirect('/login?role=admin');

        $followed = $this->followingRedirects()->get('/admin/login');
        $followed->assertStatus(200);
        $followed->assertSee('Cimohot');
        $followed->assertSee('Administrator');
    }

    public function test_unauthenticated_admin_redirected_to_unified_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login?role=admin');
    }

    public function test_unauthenticated_customer_redirected_to_login(): void
    {
        $response = $this->get('/akun');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_and_views(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Admin user should exist in database');

        $this->actingAs($admin);

        // Dashboard
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Command Center');

        // Orders
        $response = $this->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pesanan');

        // Menu
        $response = $this->get('/admin/menu');
        $response->assertStatus(200);
        $response->assertSee('Katalog & Stok Menu');

        // Customers
        $response = $this->get('/admin/customers');
        $response->assertStatus(200);
        $response->assertSee('Data Pelanggan');

        // Reports
        $response = $this->get('/admin/reports');
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');

        // Promotions
        $response = $this->get('/admin/promotions');
        $response->assertStatus(200);
        $response->assertSee('Voucher & Promo');
    }

    public function test_admin_api_endpoints_return_valid_json(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Stats
        $response = $this->getJson('/admin/api/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'revenue_today',
                'orders_today',
                'customers_today',
                'top_product',
                'pending_count',
                'processing_count',
                'completed_count',
                'low_stock_menus',
                'payment_breakdown',
            ]
        ]);

        // Sales Chart
        $response = $this->getJson('/admin/api/sales-chart?days=7');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'line_chart' => ['labels', 'revenue', 'orders'],
                'category_chart' => ['labels', 'data'],
            ]
        ]);

        // Orders
        $response = $this->getJson('/admin/api/orders');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Menu
        $response = $this->getJson('/admin/api/menu');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Customers
        $response = $this->getJson('/admin/api/customers');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Promotions
        $response = $this->getJson('/admin/api/promotions');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Notifications
        $response = $this->getJson('/admin/api/notifications');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_admin_can_quick_restock_menu(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $menu = \App\Models\Menu::first();
        $this->assertNotNull($menu);

        $response = $this->patchJson('/admin/api/menu/' . $menu->menu_id . '/stock', [
            'stock' => 25
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(25, $menu->fresh()->stock);
    }

    public function test_customer_can_access_profile_when_authenticated(): void
    {
        $customer = User::where('role', 'pelanggan')->first() ?? User::first();
        $this->actingAs($customer);

        $response = $this->get('/akun');
        $response->assertStatus(200);
        $response->assertSee('Akun Saya');
    }

    public function test_legacy_order_confirmation_redirects_to_clean_route(): void
    {
        $response = $this->get('/order_confirmation.php');
        $response->assertRedirect('/cart/confirmation');

        $response2 = $this->get('/cart/order_confirmation.php');
        $response2->assertRedirect('/cart/confirmation');
    }

    public function test_legacy_cart_redirects_to_clean_route(): void
    {
        $response = $this->get('/cart.php');
        $response->assertRedirect('/cart');

        $response2 = $this->get('/cart/cart.php');
        $response2->assertRedirect('/cart');
    }

    public function test_customer_can_login_via_api_and_add_to_cart_seamlessly(): void
    {
        $customer = User::where('role', 'user')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Cikooo',
                'email' => 'Ciko@gmail.com',
                'phone' => '08123456789',
                'password' => bcrypt('Ciko123'),
                'role' => 'user',
            ]);
        }

        $menu = Menu::first();

        // 1. Act as authenticated customer via Auth
        $response = $this->actingAs($customer)->post('/api.php', [
            'action' => 'add_to_cart',
            'menu_id' => $menu->menu_id,
            'quantity' => 1,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        // 2. Check session endpoint
        $sessionResponse = $this->actingAs($customer)->get('/api.php?action=check_session');
        $sessionResponse->assertOk();
        $sessionResponse->assertJson([
            'success' => true,
            'data' => [
                'loggedIn' => true,
                'user' => [
                    'id' => $customer->user_id,
                    'name' => $customer->name,
                ],
            ],
        ]);
    }

    public function test_admin_cannot_add_to_cart_or_order(): void
    {
        $admin = User::where('role', 'admin')->first();
        $menu = Menu::first();

        $response = $this->actingAs($admin)->post('/api.php', [
            'action' => 'add_to_cart',
            'menu_id' => $menu->menu_id,
            'quantity' => 1,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_unauthenticated_add_to_cart_returns_401(): void
    {
        $menu = Menu::first();

        $response = $this->post('/api.php', [
            'action' => 'add_to_cart',
            'menu_id' => $menu->menu_id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Akses ditolak. Silakan login.',
        ]);
    }

    public function test_order_flow_cart_api_and_drawer_endpoints(): void
    {
        $customer = User::where('role', 'user')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Cikooo',
                'email' => 'Ciko@gmail.com',
                'phone' => '08123456789',
                'password' => bcrypt('Ciko123'),
                'role' => 'user',
            ]);
        }

        // Clean user's cart first
        \Illuminate\Support\Facades\DB::table('cart')->where('user_id', $customer->user_id)->delete();

        $menu = Menu::first();

        // 1. Add item to cart
        $addResponse = $this->actingAs($customer)->post('/api.php', [
            'action' => 'add_to_cart',
            'menu_id' => $menu->menu_id,
            'quantity' => 1,
        ]);

        $addResponse->assertOk();
        $addResponse->assertJsonStructure([
            'success',
            'data' => [
                'new_cart_count',
                'cart_count',
                'cart_subtotal',
                'cart_subtotal_formatted',
                'cart_items',
            ],
        ]);

        // 2. Fetch cart contents for interactive drawer
        $getCartResponse = $this->actingAs($customer)->get('/api.php?action=get_cart');
        $getCartResponse->assertOk();
        $getCartResponse->assertJson([
            'success' => true,
            'data' => [
                'count' => 1,
            ],
        ]);

        // 3. Update quantity inline in drawer
        $updateResponse = $this->actingAs($customer)->post('/api.php', [
            'action' => 'update_cart',
            'menu_id' => $menu->menu_id,
            'quantity' => 3,
        ]);
        $updateResponse->assertOk();
        $updateResponse->assertJson([
            'success' => true,
            'data' => [
                'cart_count' => 3,
            ],
        ]);

        // 4. Remove item from drawer
        $removeResponse = $this->actingAs($customer)->post('/api.php', [
            'action' => 'remove_from_cart',
            'menu_id' => $menu->menu_id,
        ]);
        $removeResponse->assertOk();
        $removeResponse->assertJson([
            'success' => true,
            'data' => [
                'cart_count' => 0,
            ],
        ]);
    }

    public function test_checkout_handles_promo_removal_correctly(): void
    {
        $customer = User::where('role', 'user')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Cikooo',
                'email' => 'Ciko@gmail.com',
                'phone' => '08123456789',
                'address' => 'Jl. Test No. 123',
                'password' => bcrypt('Ciko123'),
                'role' => 'user',
            ]);
        } else {
            $customer->address = $customer->address ?: 'Jl. Test No. 123';
            $customer->phone = $customer->phone ?: '08123456789';
            $customer->save();
        }

        $menu = Menu::first();
        $items = [
            [
                'id' => $menu->menu_id,
                'name' => $menu->name,
                'price_raw' => (float)$menu->price,
                'quantity' => 4,
            ]
        ];
        $expectedSubtotal = (float)$menu->price * 4;

        // 1. Validate promo code CIMOHOT10
        $validateResponse = $this->actingAs($customer)->post('/api.php', [
            'action' => 'validate_promo',
            'code' => 'CIMOHOT10',
            'total' => $expectedSubtotal,
        ]);
        $validateResponse->assertOk();
        $validateResponse->assertJson(['success' => true]);

        // 2. Checkout WITH promo code
        $checkoutWithPromo = $this->actingAs($customer)->post('/api.php', [
            'action' => 'checkout',
            'items' => json_encode($items),
            'payment_method' => 'cod',
            'delivery_type' => 'delivery',
            'notes' => 'Promo applied',
            'promo_code' => 'CIMOHOT10',
        ]);
        $checkoutWithPromo->assertOk();
        $orderIdWithPromo = $checkoutWithPromo->json('data.order_id');
        $orderWithPromo = \Illuminate\Support\Facades\DB::table('orders')->where('order_id', $orderIdWithPromo)->first();
        $this->assertNotNull($orderWithPromo);
        $this->assertGreaterThan(0, (float)$orderWithPromo->discount_amount);
        $this->assertEquals('CIMOHOT10', $orderWithPromo->promo_code);

        // 3. Checkout WITHOUT promo code (when user deletes / clears the discount code)
        $checkoutNoPromo = $this->actingAs($customer)->post('/api.php', [
            'action' => 'checkout',
            'items' => json_encode($items),
            'payment_method' => 'cod',
            'delivery_type' => 'delivery',
            'notes' => 'No promo applied after deletion',
            'promo_code' => '',
        ]);

        $checkoutNoPromo->assertOk();
        $checkoutNoPromo->assertJson(['success' => true]);
        $orderId = $checkoutNoPromo->json('data.order_id');

        $order = \Illuminate\Support\Facades\DB::table('orders')->where('order_id', $orderId)->first();
        $this->assertNotNull($order);
        $this->assertEquals(0, (float)$order->discount_amount);
        $this->assertNull($order->promo_code);
        $this->assertEquals($expectedSubtotal, (float)$order->total_amount);
    }
}




