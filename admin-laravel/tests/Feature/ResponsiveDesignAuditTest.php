<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ResponsiveDesignAuditTest extends TestCase
{
    /**
     * Get or create a standard user (customer)
     */
    protected function getCustomerUser(): User
    {
        $customer = User::where('role', 'user')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Auditor Customer',
                'email' => 'auditor@cimohot.test',
                'phone' => '081299998888',
                'address' => 'Jl. Audit Responsive No. 1',
                'password' => bcrypt('Audit123!'),
                'role' => 'user',
            ]);
        }
        return $customer;
    }

    /**
     * Test viewport meta tags across all key views
     */
    public function test_all_pages_have_mobile_responsive_viewport_meta(): void
    {
        $admin = User::where('role', 'admin')->first();
        $customer = $this->getCustomerUser();

        // 1. Home
        $home = $this->get('/');
        $home->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $home->getContent());
        $this->assertStringContainsString('width=device-width', $home->getContent());

        // 2. Login
        $login = $this->get('/login');
        $login->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $login->getContent());
        $this->assertStringContainsString('width=device-width', $login->getContent());

        // 3. Register
        $register = $this->get('/register');
        $register->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $register->getContent());
        $this->assertStringContainsString('width=device-width', $register->getContent());

        // 4. Cart (as customer)
        $cart = $this->actingAs($customer)
                     ->withSession(['user_id' => $customer->user_id, 'role' => 'user'])
                     ->get('/cart');
        $cart->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $cart->getContent());
        $this->assertStringContainsString('width=device-width', $cart->getContent());

        // 5. Profile
        $profile = $this->actingAs($customer)
                        ->withSession(['user_id' => $customer->user_id, 'role' => 'user'])
                        ->get('/akun');
        $profile->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $profile->getContent());
        $this->assertStringContainsString('width=device-width', $profile->getContent());

        // 6. Admin Dashboard
        $dashboard = $this->actingAs($admin)
                          ->withSession(['user_id' => $admin->user_id, 'role' => 'admin'])
                          ->get('/admin/dashboard');
        $dashboard->assertStatus(200);
        $this->assertStringContainsString('name="viewport"', $dashboard->getContent());
        $this->assertStringContainsString('width=device-width', $dashboard->getContent());
    }

    /**
     * Test that Admin Dashboard layout contains all mobile off-canvas & responsive controls
     */
    public function test_admin_dashboard_has_responsive_mobile_offcanvas_controls(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)
                         ->withSession(['user_id' => $admin->user_id, 'role' => 'admin'])
                         ->get('/admin/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Must have mobile hamburger toggle button
        $this->assertStringContainsString('mobile-menu-toggle', $content);
        $this->assertStringContainsString('id="mobile-menu-toggle"', $content);

        // Must have mobile sidebar backdrop overlay
        $this->assertStringContainsString('id="sidebar-backdrop"', $content);

        // Must have mobile sidebar close button
        $this->assertStringContainsString('id="sidebar-close-btn"', $content);
        $this->assertStringContainsString('sidebar-close-btn', $content);

        // Must have responsive topbar user pill
        $this->assertStringContainsString('topbar-user-pill', $content);
        $this->assertStringContainsString('topbar-user-avatar', $content);
    }

    /**
     * Test that Admin CSS contains all required mobile & tablet responsive media queries
     */
    public function test_admin_css_contains_full_responsive_rules(): void
    {
        $cssPath = public_path('css/admin.css');
        $this->assertFileExists($cssPath);
        $css = file_get_contents($cssPath);

        // Breakpoints
        $this->assertStringContainsString('@media (max-width: 860px)', $css);
        $this->assertStringContainsString('@media (max-width: 640px)', $css);
        $this->assertStringContainsString('@media (max-width: 480px)', $css);

        // Off-canvas sidebar rules
        $this->assertStringContainsString('translateX(-100%)', $css);
        $this->assertStringContainsString('.admin-sidebar.mobile-open', $css);

        // Table horizontal touch scroll
        $this->assertStringContainsString('min-width: 640px', $css);
        $this->assertStringContainsString('-webkit-overflow-scrolling: touch', $css);

        // Prevent body/html horizontal scroll leak
        $this->assertStringContainsString('overflow-x: hidden', $css);
    }

    /**
     * Test that Customer Main Web contains Mobile Bottom Dock and Floating actions
     */
    public function test_customer_web_has_mobile_bottom_dock_and_floating_actions(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Must have mobile bottom dock
        $this->assertStringContainsString('mobile-bottom-dock', $content);
        $this->assertStringContainsString('dock-item', $content);
        $this->assertStringContainsString('id="dockCartLink"', $content);
        $this->assertStringContainsString('dock-cart-badge', $content);

        // Must have floating cart bar and drawer
        $this->assertStringContainsString('id="floatingCartBar"', $content);
        $this->assertStringContainsString('id="cartDrawerOverlay"', $content);

        // Must have floating WhatsApp button
        $this->assertStringContainsString('floating-wa-btn', $content);
    }

    /**
     * Test that Customer CSS contains all responsive stacking and touch media queries
     */
    public function test_customer_css_contains_responsive_mobile_stacking(): void
    {
        $candidates = [
            'C:/laragon/www/cimohot/assets/css/main.css',
            public_path('assets/css/main.css'),
            base_path('../assets/css/main.css'),
            base_path('../cimohot/assets/css/main.css'),
        ];

        $css = null;
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $css = file_get_contents($path);
                break;
            }
        }

        $this->assertNotNull($css, 'Could not locate main.css in any known paths');

        // Mobile dock definition
        $this->assertStringContainsString('.mobile-bottom-dock', $css);
        $this->assertStringContainsString('@media (max-width: 991px)', $css);

        // Safe stacking between dock, cart bar, and WhatsApp button
        $this->assertStringContainsString('.floating-cart-bar', $css);
        $this->assertStringContainsString('.floating-wa-btn', $css);
        $this->assertStringContainsString('body.has-floating-cart .floating-wa-btn', $css);

        // Prevent overflow leak
        $this->assertStringContainsString('overflow-x: hidden', $css);
    }

    /**
     * Test that Profile page has responsive table wrapper and single-column collapse
     */
    public function test_profile_page_has_responsive_layout(): void
    {
        $customer = $this->getCustomerUser();
        $response = $this->actingAs($customer)
                         ->withSession(['user_id' => $customer->user_id, 'role' => 'user'])
                         ->get('/akun');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for responsive wrappers
        $this->assertStringContainsString('orders-table-wrapper', $content);
        $this->assertStringContainsString('orders-table', $content);
        $this->assertStringContainsString('app-container', $content);
        $this->assertStringContainsString('profile-card', $content);
    }

    /**
     * Test that Cart page has responsive layout and stepper
     */
    public function test_cart_page_has_responsive_stepper_and_layout(): void
    {
        $customer = $this->getCustomerUser();
        $response = $this->actingAs($customer)
                         ->withSession(['user_id' => $customer->user_id, 'role' => 'user'])
                         ->get('/cart');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check stepper and layout wrappers
        $this->assertStringContainsString('progress-bar-wrap', $content);
        $this->assertStringContainsString('stepper', $content);
        $this->assertStringContainsString('page-wrap', $content);
    }
}
