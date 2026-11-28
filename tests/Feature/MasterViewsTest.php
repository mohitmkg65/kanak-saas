<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\OrganizationProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\SuperAdminSeeder;

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->seed(OrganizationSeeder::class);

    $this->admin = User::where('email', 'admin@kanaksaas.com')->first();
    $this->orgUser = User::where('email', 'owner@kanaksupplies.com')->first();
    $this->organization = Organization::where('slug', 'kanak-building-supplies')->first();
});

test('super admin can view categories list and create page', function () {
    $category = Category::factory()->create(['name' => 'Cement & Concrete']);

    $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));
    $response->assertOk();
    $response->assertSee('Cement &amp; Concrete', false);
    $response->assertSee('Categories &amp; Subcategories', false);
    $response->assertSee('Add Category');
    $response->assertSee('Save Category');

    // Test edit mode on 2-part index page
    $editPageResponse = $this->actingAs($this->admin)->get(route('admin.categories.index', ['edit' => $category->public_id]));
    $editPageResponse->assertOk();
    $editPageResponse->assertSee('Edit Category: Cement &amp; Concrete', false);
    $editPageResponse->assertSee('Update Category');

    // Direct edit route renders 2-part index page at /admin/categories/{category}/edit
    $editRouteResponse = $this->actingAs($this->admin)->get(route('admin.categories.edit', $category));
    $editRouteResponse->assertOk();
    $editRouteResponse->assertSee('Edit Category: Cement &amp; Concrete', false);
    $editRouteResponse->assertSee('Update Category');

    // Updating category redirects back to the category index page
    $updateResponse = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
        'name' => 'Cement & Concrete Updated',
        'slug' => 'cement-concrete-updated',
        'status' => 'active',
    ]);
    $updateResponse->assertRedirect(route('admin.categories.index'));

    $createResponse = $this->actingAs($this->admin)->get(route('admin.categories.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('Add Category');
});

test('super admin can view units list and create page', function () {
    $unit = Unit::factory()->create(['name' => 'Metric Ton Test', 'abbreviation' => 'MTT']);

    $response = $this->actingAs($this->admin)->get(route('admin.units.index'));
    $response->assertOk();
    $response->assertSee('Metric Ton Test');
    $response->assertSee('MTT');
    $response->assertSee('Add Unit of Measure');
    $response->assertSee('Save Unit');
    $response->assertSee('Applicable Categories');

    // Test edit mode on 2-part index page
    $editPageResponse = $this->actingAs($this->admin)->get(route('admin.units.index', ['edit' => $unit->public_id]));
    $editPageResponse->assertOk();
    $editPageResponse->assertSee('Edit Unit: Metric Ton Test');
    $editPageResponse->assertSee('Update Unit');

    // Direct edit route renders 2-part index page at /admin/units/{unit}/edit
    $editRouteResponse = $this->actingAs($this->admin)->get(route('admin.units.edit', $unit));
    $editRouteResponse->assertOk();
    $editRouteResponse->assertSee('Edit Unit: Metric Ton Test');
    $editRouteResponse->assertSee('Update Unit');

    $createResponse = $this->actingAs($this->admin)->get(route('admin.units.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('Add Unit of Measure');
    $createResponse->assertSee('Applicable Material Categories');
});

test('super admin can create unit with multiple categories', function () {
    $cat1 = Category::factory()->create(['name' => 'Cement Department']);
    $cat2 = Category::factory()->create(['name' => 'Steel Department']);

    $response = $this->actingAs($this->admin)->post(route('admin.units.store'), [
        'name' => 'Kilogram Standard',
        'abbreviation' => 'KGSTD',
        'description' => 'Weight measurement',
        'status' => 'active',
        'category_ids' => [$cat1->id, $cat2->id],
    ]);

    $response->assertRedirect(route('admin.units.index'));
    $unit = Unit::where('abbreviation', 'KGSTD')->first();
    expect($unit)->not->toBeNull();
    expect($unit->categories)->toHaveCount(2);
    expect($unit->categories->pluck('id')->toArray())->toContain($cat1->id, $cat2->id);
    expect($cat1->fresh()->units->pluck('id')->toArray())->toContain($unit->id);
});

test('super admin can update unit categories', function () {
    $cat1 = Category::factory()->create(['name' => 'Paints Department']);
    $cat2 = Category::factory()->create(['name' => 'Chemicals Department']);
    $unit = Unit::factory()->create(['name' => 'Litre Standard', 'abbreviation' => 'LTRSTD']);
    $unit->categories()->sync([$cat1->id]);

    $response = $this->actingAs($this->admin)->put(route('admin.units.update', $unit), [
        'name' => 'Litre Standard Updated',
        'abbreviation' => 'LTRSTD',
        'description' => 'Liquid volume',
        'status' => 'active',
        'category_ids' => [$cat1->id, $cat2->id],
    ]);

    $response->assertRedirect(route('admin.units.index'));
    expect($unit->fresh()->categories)->toHaveCount(2);
    expect($unit->fresh()->categories->pluck('id')->toArray())->toContain($cat2->id);
});

test('super admin can view brands list and create page', function () {
    $brand = Brand::factory()->create(['name' => 'Tata Tiscon Test', 'code' => 'TISCONTEST']);

    $response = $this->actingAs($this->admin)->get(route('admin.brands.index'));
    $response->assertOk();
    $response->assertSee('Tata Tiscon Test');
    $response->assertSee('TISCONTEST');
    $response->assertSee('Add Brand');
    $response->assertSee('Save Brand');
    $response->assertSee('Applicable Categories');

    // Test edit mode on 2-part index page
    $editPageResponse = $this->actingAs($this->admin)->get(route('admin.brands.index', ['edit' => $brand->public_id]));
    $editPageResponse->assertOk();
    $editPageResponse->assertSee('Edit Brand: Tata Tiscon Test');
    $editPageResponse->assertSee('Update Brand');

    // Direct edit route renders 2-part index page at /admin/brands/{brand}/edit
    $editRouteResponse = $this->actingAs($this->admin)->get(route('admin.brands.edit', $brand));
    $editRouteResponse->assertOk();
    $editRouteResponse->assertSee('Edit Brand: Tata Tiscon Test');
    $editRouteResponse->assertSee('Update Brand');

    $createResponse = $this->actingAs($this->admin)->get(route('admin.brands.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('Add Brand');
    $createResponse->assertSee('Applicable Material Categories');
});

test('super admin can create brand with multiple categories', function () {
    $cat1 = Category::factory()->create(['name' => 'Ceramics Dept']);
    $cat2 = Category::factory()->create(['name' => 'Bathware Dept']);

    $response = $this->actingAs($this->admin)->post(route('admin.brands.store'), [
        'name' => 'Somany Ceramics Test',
        'code' => 'SOMANYTEST',
        'website' => 'https://somanyceramics.com',
        'description' => 'Floor and wall tile specialist',
        'status' => 'active',
        'category_ids' => [$cat1->id, $cat2->id],
    ]);

    $response->assertRedirect(route('admin.brands.index'));
    $brand = Brand::where('name', 'Somany Ceramics Test')->first();
    expect($brand)->not->toBeNull();
    expect($brand->categories)->toHaveCount(2);
    expect($brand->categories->pluck('id')->toArray())->toContain($cat1->id, $cat2->id);
    expect($cat1->fresh()->brands->pluck('id')->toArray())->toContain($brand->id);
});

test('super admin can update brand and its categories', function () {
    $cat1 = Category::factory()->create(['name' => 'Paints Test Dept']);
    $cat2 = Category::factory()->create(['name' => 'Waterproofing Test Dept']);
    $brand = Brand::factory()->create(['name' => 'Dr Fixit Test', 'code' => 'DRFIXTEST']);
    $brand->categories()->sync([$cat1->id]);

    $response = $this->actingAs($this->admin)->put(route('admin.brands.update', $brand), [
        'name' => 'Dr Fixit Test Updated',
        'code' => 'DRFIXTEST2',
        'website' => 'https://drfixit.co.in',
        'description' => 'Waterproofing solutions',
        'status' => 'active',
        'category_ids' => [$cat1->id, $cat2->id],
    ]);

    $response->assertRedirect(route('admin.brands.index'));
    expect($brand->fresh()->name)->toBe('Dr Fixit Test Updated');
    expect($brand->fresh()->categories)->toHaveCount(2);
    expect($brand->fresh()->categories->pluck('id')->toArray())->toContain($cat2->id);
});

test('super admin can view global products list and details', function () {
    $product = Product::factory()->create(['name' => 'UltraTech 53 Grade', 'brand' => 'UltraTech']);

    $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
    $response->assertOk();
    $response->assertSee('UltraTech 53 Grade');

    $showResponse = $this->actingAs($this->admin)->get(route('admin.products.show', $product));
    $showResponse->assertOk();
    $showResponse->assertSee('UltraTech 53 Grade');
});

test('super admin can view customers directory', function () {
    $customer = Customer::factory()->create([
        'organization_id' => $this->organization->id,
        'name' => 'Balaji Builders',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.customers.index'));
    $response->assertOk();
    $response->assertSee('Balaji Builders');
    $response->assertSee($this->organization->name);
});

test('organization user can view my products and global catalog', function () {
    Product::factory()->create(['name' => 'ACC Suraksha Cement']);

    OrganizationProduct::factory()->create([
        'organization_id' => $this->organization->id,
        'name' => 'Custom Mortar Mix',
        'selling_price' => 450.00,
    ]);

    $session = ['active_organization_id' => $this->organization->id];

    // Org Products list
    $response = $this->actingAs($this->orgUser)->withSession($session)->get(route('organization.products.index'));
    $response->assertOk();
    $response->assertSee('Custom Mortar Mix');
    $response->assertSee('450.00');

    // Global Catalog browser
    $catalogResponse = $this->actingAs($this->orgUser)->withSession($session)->get(route('organization.products.catalog'));
    $catalogResponse->assertOk();
    $catalogResponse->assertSee('ACC Suraksha Cement');
    $catalogResponse->assertSee('+ Add to Store');
});

test('organization user can view and manage customers', function () {
    $customer = Customer::factory()->create([
        'organization_id' => $this->organization->id,
        'name' => 'Shree Construction',
        'phone' => '+91 9988776655',
    ]);

    $session = ['active_organization_id' => $this->organization->id];

    $response = $this->actingAs($this->orgUser)->withSession($session)->get(route('organization.customers.index'));
    $response->assertOk();
    $response->assertSee('Shree Construction');

    $showResponse = $this->actingAs($this->orgUser)->withSession($session)->get(route('organization.customers.show', $customer));
    $showResponse->assertOk();
    $showResponse->assertSee('Shree Construction');
});
