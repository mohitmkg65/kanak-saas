<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class GlobalMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Units of Measure
        $units = [
            ['name' => 'Bag', 'abbreviation' => 'Bag', 'description' => 'Standard 50kg cement bag'],
            ['name' => 'Metric Ton', 'abbreviation' => 'MT', 'description' => 'Metric ton (1000 kg)'],
            ['name' => 'Kilogram', 'abbreviation' => 'KG', 'description' => 'Kilogram'],
            ['name' => 'Gram', 'abbreviation' => 'GM', 'description' => 'Gram'],
            ['name' => 'Number/Piece', 'abbreviation' => 'Nos', 'description' => 'Count/piece/number'],
            ['name' => 'Box', 'abbreviation' => 'Box', 'description' => 'Box or carton'],
            ['name' => 'Litre', 'abbreviation' => 'Ltr', 'description' => 'Litre (liquid measure)'],
            ['name' => 'Millilitre', 'abbreviation' => 'ML', 'description' => 'Millilitre'],
            ['name' => 'Square Feet', 'abbreviation' => 'Sqft', 'description' => 'Square feet (area)'],
            ['name' => 'Square Meter', 'abbreviation' => 'Sqm', 'description' => 'Square meter (area)'],
            ['name' => 'Running Feet', 'abbreviation' => 'Rft', 'description' => 'Running feet (linear)'],
            ['name' => 'Meter', 'abbreviation' => 'Mtr', 'description' => 'Meter (linear)'],
            ['name' => 'Bundle', 'abbreviation' => 'Bndl', 'description' => 'Bundle or pack'],
            ['name' => 'Sheet', 'abbreviation' => 'Sht', 'description' => 'Sheet'],
            ['name' => 'Gallon', 'abbreviation' => 'Gal', 'description' => 'Gallon (liquid)'],
            ['name' => 'Cubic Feet', 'abbreviation' => 'Cft', 'description' => 'Cubic feet (volume)'],
            ['name' => 'Cubic Meter', 'abbreviation' => 'Cu.m', 'description' => 'Cubic meter (volume)'],
        ];

        $unitMap = [];
        foreach ($units as $u) {
            $unit = Unit::updateOrCreate(
                ['abbreviation' => $u['abbreviation']],
                [
                    'name' => $u['name'],
                    'description' => $u['description'],
                    'status' => 'active',
                ]
            );
            $unitMap[$u['abbreviation']] = $unit->id;
        }

        // 2. Seed Parent Categories
        $parentCategories = [
            ['name' => 'Cement & Concrete', 'slug' => 'cement-concrete', 'sort_order' => 1, 'description' => 'All types of cement, ready-mix concrete, and concrete accessories'],
            ['name' => 'Steel & Iron', 'slug' => 'steel-iron', 'sort_order' => 2, 'description' => 'TMT bars, steel pipes, iron sheets, and structural steel'],
            ['name' => 'Bricks & Blocks', 'slug' => 'bricks-blocks', 'sort_order' => 3, 'description' => 'Red bricks, fly-ash bricks, AAC blocks, and hollow blocks'],
            ['name' => 'Hardware & Fasteners', 'slug' => 'hardware-fasteners', 'sort_order' => 4, 'description' => 'Nuts, bolts, screws, anchors, and general hardware'],
            ['name' => 'Tiles & Flooring', 'slug' => 'tiles-flooring', 'sort_order' => 5, 'description' => 'Floor tiles, wall tiles, marble, granite, and vitrified tiles'],
            ['name' => 'Sanitaryware & Plumbing', 'slug' => 'sanitaryware-plumbing', 'sort_order' => 6, 'description' => 'Toilets, wash basins, pipes, fittings, and taps'],
            ['name' => 'Paints & Coatings', 'slug' => 'paints-coatings', 'sort_order' => 7, 'description' => 'Interior paints, exterior paints, primers, and waterproofing'],
            ['name' => 'Electrical Materials', 'slug' => 'electrical-materials', 'sort_order' => 8, 'description' => 'Wires, cables, switches, conduits, and electrical accessories'],
            ['name' => 'Wood & Plyboard', 'slug' => 'wood-plyboard', 'sort_order' => 9, 'description' => 'Plywood, MDF, timber, and wood accessories'],
            ['name' => 'Roofing & Waterproofing', 'slug' => 'roofing-waterproofing', 'sort_order' => 10, 'description' => 'Roofing sheets, waterproofing membranes, and terrace solutions'],
            ['name' => 'Adhesives & Chemicals', 'slug' => 'adhesives-chemicals', 'sort_order' => 11, 'description' => 'Tile adhesives, grout, construction chemicals, and sealants'],
            ['name' => 'Glass & Aluminum', 'slug' => 'glass-aluminum', 'sort_order' => 12, 'description' => 'Float glass, toughened glass, aluminum profiles, and sections'],
        ];

        $categoryMap = [];
        foreach ($parentCategories as $cat) {
            $category = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'sort_order' => $cat['sort_order'],
                    'parent_id' => null,
                    'status' => 'active',
                ]
            );
            $categoryMap[$cat['slug']] = $category->id;
        }

        // 3. Seed Subcategories
        $subcategories = [
            // Cement subcategories
            ['name' => 'OPC Cement', 'slug' => 'opc-cement', 'parent' => 'cement-concrete', 'sort_order' => 1],
            ['name' => 'PPC Cement', 'slug' => 'ppc-cement', 'parent' => 'cement-concrete', 'sort_order' => 2],
            ['name' => 'White Cement', 'slug' => 'white-cement', 'parent' => 'cement-concrete', 'sort_order' => 3],
            ['name' => 'Ready Mix Concrete', 'slug' => 'ready-mix-concrete', 'parent' => 'cement-concrete', 'sort_order' => 4],

            // Steel subcategories
            ['name' => 'TMT Bars', 'slug' => 'tmt-bars', 'parent' => 'steel-iron', 'sort_order' => 1],
            ['name' => 'Steel Pipes & Tubes', 'slug' => 'steel-pipes-tubes', 'parent' => 'steel-iron', 'sort_order' => 2],
            ['name' => 'MS Plates & Sheets', 'slug' => 'ms-plates-sheets', 'parent' => 'steel-iron', 'sort_order' => 3],

            // Tiles subcategories
            ['name' => 'Floor Tiles', 'slug' => 'floor-tiles', 'parent' => 'tiles-flooring', 'sort_order' => 1],
            ['name' => 'Wall Tiles', 'slug' => 'wall-tiles', 'parent' => 'tiles-flooring', 'sort_order' => 2],
            ['name' => 'Vitrified Tiles', 'slug' => 'vitrified-tiles', 'parent' => 'tiles-flooring', 'sort_order' => 3],
            ['name' => 'Marble & Granite', 'slug' => 'marble-granite', 'parent' => 'tiles-flooring', 'sort_order' => 4],

            // Paints subcategories
            ['name' => 'Interior Emulsion', 'slug' => 'interior-emulsion', 'parent' => 'paints-coatings', 'sort_order' => 1],
            ['name' => 'Exterior Emulsion', 'slug' => 'exterior-emulsion', 'parent' => 'paints-coatings', 'sort_order' => 2],
            ['name' => 'Primers & Putty', 'slug' => 'primers-putty', 'parent' => 'paints-coatings', 'sort_order' => 3],
        ];

        foreach ($subcategories as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                [
                    'name' => $sub['name'],
                    'parent_id' => $categoryMap[$sub['parent']],
                    'sort_order' => $sub['sort_order'],
                    'status' => 'active',
                ]
            );
        }

        // 4. Map Units to Multiple Categories
        $unitCategoryMapping = [
            'Bag' => ['cement-concrete', 'adhesives-chemicals', 'opc-cement', 'ppc-cement', 'white-cement'],
            'MT' => ['steel-iron', 'cement-concrete', 'tmt-bars'],
            'KG' => ['hardware-fasteners', 'adhesives-chemicals', 'paints-coatings'],
            'GM' => ['hardware-fasteners', 'adhesives-chemicals'],
            'Nos' => ['hardware-fasteners', 'sanitaryware-plumbing', 'electrical-materials', 'bricks-blocks', 'wood-plyboard'],
            'Box' => ['tiles-flooring', 'hardware-fasteners', 'sanitaryware-plumbing', 'electrical-materials'],
            'Ltr' => ['paints-coatings', 'adhesives-chemicals', 'roofing-waterproofing', 'interior-emulsion', 'exterior-emulsion'],
            'ML' => ['paints-coatings', 'adhesives-chemicals'],
            'Sqft' => ['tiles-flooring', 'wood-plyboard', 'glass-aluminum', 'roofing-waterproofing', 'floor-tiles', 'wall-tiles', 'vitrified-tiles'],
            'Sqm' => ['tiles-flooring', 'glass-aluminum', 'roofing-waterproofing'],
            'Rft' => ['steel-iron', 'sanitaryware-plumbing', 'wood-plyboard'],
            'Mtr' => ['electrical-materials', 'sanitaryware-plumbing', 'steel-iron'],
            'Bndl' => ['steel-iron', 'wood-plyboard', 'tmt-bars'],
            'Sht' => ['roofing-waterproofing', 'wood-plyboard', 'glass-aluminum', 'ms-plates-sheets'],
            'Cft' => ['wood-plyboard', 'bricks-blocks'],
        ];

        foreach ($unitCategoryMapping as $unitAbbr => $categorySlugs) {
            $unitId = $unitMap[$unitAbbr] ?? null;
            if ($unitId) {
                $matchedCategoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
                Unit::find($unitId)?->categories()->syncWithoutDetaching($matchedCategoryIds);
            }
        }

        // 5. Seed Sample Products
        $products = [
            [
                'name' => 'UltraTech OPC 53 Grade Cement',
                'slug' => 'ultratech-opc-53-grade-cement',
                'category' => 'opc-cement',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-OPC-53-UT',
                'brand' => 'UltraTech',
                'base_price' => 380.00,
                'description' => 'UltraTech OPC 53 Grade Cement — 50kg bag. Premium quality Portland cement for all construction purposes.',
            ],
            [
                'name' => 'ACC Gold OPC 53 Cement',
                'slug' => 'acc-gold-opc-53-cement',
                'category' => 'opc-cement',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-OPC-53-ACC',
                'brand' => 'ACC',
                'base_price' => 370.00,
                'description' => 'ACC Gold OPC 53 Grade Cement — 50kg bag.',
            ],
            [
                'name' => 'Ambuja PPC Cement',
                'slug' => 'ambuja-ppc-cement',
                'category' => 'ppc-cement',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-PPC-AMB',
                'brand' => 'Ambuja',
                'base_price' => 360.00,
                'description' => 'Ambuja Portland Pozzolana Cement — 50kg bag.',
            ],
            [
                'name' => 'TMT Fe-500 12mm Bars',
                'slug' => 'tmt-fe500-12mm-bars',
                'category' => 'tmt-bars',
                'unit' => 'MT',
                'hsn_code' => '721310',
                'sku' => 'STL-TMT-12-500',
                'brand' => 'TATA Steel',
                'base_price' => 62000.00,
                'description' => 'TATA TMT Fe-500 grade 12mm reinforcement bars — per metric ton.',
            ],
            [
                'name' => 'TMT Fe-500 8mm Bars',
                'slug' => 'tmt-fe500-8mm-bars',
                'category' => 'tmt-bars',
                'unit' => 'MT',
                'hsn_code' => '721310',
                'sku' => 'STL-TMT-8-500',
                'brand' => 'TATA Steel',
                'base_price' => 63000.00,
                'description' => 'TATA TMT Fe-500 grade 8mm reinforcement bars — per metric ton.',
            ],
            [
                'name' => 'Asian Paints Apex Exterior Emulsion (1L)',
                'slug' => 'asian-paints-apex-exterior-1l',
                'category' => 'exterior-emulsion',
                'unit' => 'Ltr',
                'hsn_code' => '320910',
                'sku' => 'PNT-EXT-APEX-1L',
                'brand' => 'Asian Paints',
                'base_price' => 350.00,
                'description' => 'Asian Paints Apex Exterior Emulsion — weather-resistant exterior paint, 1 Litre.',
            ],
            [
                'name' => 'Berger WeatherCoat Exterior Paint (20L)',
                'slug' => 'berger-weathercoat-exterior-20l',
                'category' => 'exterior-emulsion',
                'unit' => 'Ltr',
                'hsn_code' => '320910',
                'sku' => 'PNT-EXT-BERG-20L',
                'brand' => 'Berger',
                'base_price' => 4800.00,
                'description' => 'Berger WeatherCoat exterior emulsion — 20 Litre bucket.',
            ],
            [
                'name' => '600x600 Vitrified Floor Tiles (Polished)',
                'slug' => '600x600-vitrified-floor-tiles-polished',
                'category' => 'vitrified-tiles',
                'unit' => 'Sqft',
                'hsn_code' => '690721',
                'sku' => 'TLE-VIT-600P',
                'brand' => 'Kajaria',
                'base_price' => 65.00,
                'description' => 'Kajaria 600x600mm double charged vitrified floor tiles — polished finish.',
            ],
        ];

        // Refresh category map to include subcategories
        $allCategories = Category::pluck('id', 'slug');

        foreach ($products as $p) {
            $categoryId = $allCategories[$p['category']] ?? null;
            $unitId = $unitMap[$p['unit']] ?? null;

            if ($categoryId && $unitId) {
                Product::updateOrCreate(
                    ['sku' => $p['sku']],
                    [
                        'name' => $p['name'],
                        'slug' => $p['slug'],
                        'description' => $p['description'],
                        'category_id' => $categoryId,
                        'unit_id' => $unitId,
                        'hsn_code' => $p['hsn_code'],
                        'brand' => $p['brand'],
                        'base_price' => $p['base_price'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
