<?php

namespace Database\Seeders;

use App\Models\Brand;
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
            ['name' => 'Bag', 'abbreviation' => 'Bag', 'description' => 'Standard packaging bag (e.g., 50kg cement bag)'],
            ['name' => 'Metric Ton', 'abbreviation' => 'MT', 'description' => '1000 Kilograms (e.g., steel TMT bars)'],
            ['name' => 'Kilogram', 'abbreviation' => 'KG', 'description' => 'Weight measurement in kilograms'],
            ['name' => 'Gram', 'abbreviation' => 'GM', 'description' => 'Weight measurement in grams'],
            ['name' => 'Number/Piece', 'abbreviation' => 'Nos', 'description' => 'Individual items, fittings, or units'],
            ['name' => 'Box', 'abbreviation' => 'Box', 'description' => 'Box packaging (e.g., tiles, screws, fasteners)'],
            ['name' => 'Litre', 'abbreviation' => 'Ltr', 'description' => 'Volume measurement in litres (e.g., paints, primers)'],
            ['name' => 'Millilitre', 'abbreviation' => 'ML', 'description' => 'Volume measurement in millilitres'],
            ['name' => 'Square Feet', 'abbreviation' => 'Sqft', 'description' => 'Surface area (e.g., tiles, granite, glass)'],
            ['name' => 'Square Meter', 'abbreviation' => 'Sqm', 'description' => 'Metric surface area'],
            ['name' => 'Running Feet', 'abbreviation' => 'Rft', 'description' => 'Linear length in feet (e.g., pipes, sections)'],
            ['name' => 'Meter', 'abbreviation' => 'Mtr', 'description' => 'Linear length in meters (e.g., wires, conduits)'],
            ['name' => 'Bundle', 'abbreviation' => 'Bndl', 'description' => 'Bundle of materials (e.g., TMT bars, timber)'],
            ['name' => 'Sheet', 'abbreviation' => 'Sht', 'description' => 'Flat sheet (e.g., roofing sheets, plyboard)'],
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

        // 2. Seed Categories
        $categories = [
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
        foreach ($categories as $cat) {
            $category = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'sort_order' => $cat['sort_order'],
                    'status' => 'active',
                ]
            );
            $categoryMap[$cat['slug']] = $category->id;
        }

        // 3. Map Units to Categories
        $unitCategoryMapping = [
            'Bag' => ['cement-concrete', 'adhesives-chemicals'],
            'MT' => ['steel-iron', 'cement-concrete'],
            'KG' => ['hardware-fasteners', 'adhesives-chemicals', 'paints-coatings'],
            'GM' => ['hardware-fasteners', 'adhesives-chemicals'],
            'Nos' => ['hardware-fasteners', 'sanitaryware-plumbing', 'electrical-materials', 'bricks-blocks', 'wood-plyboard'],
            'Box' => ['tiles-flooring', 'hardware-fasteners', 'sanitaryware-plumbing', 'electrical-materials'],
            'Ltr' => ['paints-coatings', 'adhesives-chemicals', 'roofing-waterproofing'],
            'ML' => ['paints-coatings', 'adhesives-chemicals'],
            'Sqft' => ['tiles-flooring', 'wood-plyboard', 'glass-aluminum', 'roofing-waterproofing'],
            'Sqm' => ['tiles-flooring', 'glass-aluminum', 'roofing-waterproofing'],
            'Rft' => ['steel-iron', 'sanitaryware-plumbing', 'wood-plyboard'],
            'Mtr' => ['electrical-materials', 'sanitaryware-plumbing', 'steel-iron'],
            'Bndl' => ['steel-iron', 'wood-plyboard'],
            'Sht' => ['roofing-waterproofing', 'wood-plyboard', 'glass-aluminum'],
            'Cft' => ['wood-plyboard', 'bricks-blocks'],
        ];

        foreach ($unitCategoryMapping as $unitAbbr => $categorySlugs) {
            $unitId = $unitMap[$unitAbbr] ?? null;
            if ($unitId) {
                $matchedCategoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
                Unit::find($unitId)?->categories()->syncWithoutDetaching($matchedCategoryIds);
            }
        }

        // 4. Seed Leading Material Brands
        $brands = [
            [
                'name' => 'UltraTech Cement',
                'code' => 'ULTRATECH',
                'slug' => 'ultratech-cement',
                'website' => 'https://www.ultratechcement.com',
                'description' => "India's largest manufacturer of grey cement and ready-mix concrete.",
                'categories' => ['cement-concrete'],
            ],
            [
                'name' => 'ACC Limited',
                'code' => 'ACC',
                'slug' => 'acc-limited',
                'website' => 'https://www.acclimited.com',
                'description' => 'Leading manufacturer of cement and ready-mix concrete.',
                'categories' => ['cement-concrete'],
            ],
            [
                'name' => 'Ambuja Cements',
                'code' => 'AMBUJA',
                'slug' => 'ambuja-cements',
                'website' => 'https://www.ambujacement.com',
                'description' => 'Major cement manufacturer known for high strength and sustainability.',
                'categories' => ['cement-concrete'],
            ],
            [
                'name' => 'TATA Steel',
                'code' => 'TATA',
                'slug' => 'tata-steel',
                'website' => 'https://www.tatatiscon.co.in',
                'description' => 'India’s leading brand of superior quality TMT re-bars.',
                'categories' => ['steel-iron'],
            ],
            [
                'name' => 'Jindal Panther',
                'code' => 'JINDAL',
                'slug' => 'jindal-panther',
                'website' => 'https://www.jindalpanther.com',
                'description' => 'High-strength TMT re-bars and structural steel products.',
                'categories' => ['steel-iron'],
            ],
            [
                'name' => 'Asian Paints',
                'code' => 'ASIAN',
                'slug' => 'asian-paints',
                'website' => 'https://www.asianpaints.com',
                'description' => 'India’s leading paint and coatings manufacturing company.',
                'categories' => ['paints-coatings'],
            ],
            [
                'name' => 'Berger',
                'code' => 'BERGER',
                'slug' => 'berger-paints',
                'website' => 'https://www.bergerpaints.com',
                'description' => 'Innovative decorative paints and industrial coatings.',
                'categories' => ['paints-coatings'],
            ],
            [
                'name' => 'Kajaria',
                'code' => 'KAJARIA',
                'slug' => 'kajaria-ceramics',
                'website' => 'https://www.kajariaceramics.com',
                'description' => 'India’s largest manufacturer of ceramic and vitrified floor and wall tiles.',
                'categories' => ['tiles-flooring'],
            ],
            [
                'name' => 'Jaquar',
                'code' => 'JAQUAR',
                'slug' => 'jaquar-bathware',
                'website' => 'https://www.jaquar.com',
                'description' => 'Premium bathroom fittings, sanitaryware, and plumbing fixtures.',
                'categories' => ['sanitaryware-plumbing'],
            ],
            [
                'name' => 'Supreme Pipes',
                'code' => 'SUPREME',
                'slug' => 'supreme-pipes',
                'website' => 'https://www.supreme.co.in',
                'description' => 'Leading producer of PVC, CPVC, drainage, and plumbing piping systems.',
                'categories' => ['sanitaryware-plumbing'],
            ],
        ];

        $brandMap = [];
        foreach ($brands as $b) {
            $brand = Brand::updateOrCreate(
                ['name' => $b['name']],
                [
                    'code' => $b['code'],
                    'slug' => $b['slug'],
                    'website' => $b['website'],
                    'description' => $b['description'],
                    'status' => 'active',
                ]
            );
            $brandMap[$b['name']] = $brand->id;
            if (isset($b['code'])) {
                $brandMap[$b['code']] = $brand->id;
            }

            if (! empty($b['categories'])) {
                $matchedCatIds = Category::whereIn('slug', $b['categories'])->pluck('id');
                $brand->categories()->syncWithoutDetaching($matchedCatIds);
            }
        }

        // 5. Seed Sample Products
        $products = [
            [
                'name' => 'UltraTech OPC 53 Grade Cement',
                'slug' => 'ultratech-opc-53-grade-cement',
                'category' => 'cement-concrete',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-OPC-53-UT',
                'brand' => 'UltraTech Cement',
                'base_price' => 380.00,
                'description' => 'UltraTech OPC 53 Grade Cement — 50kg bag. Premium quality Portland cement for all construction purposes.',
            ],
            [
                'name' => 'ACC Gold OPC 53 Cement',
                'slug' => 'acc-gold-opc-53-cement',
                'category' => 'cement-concrete',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-OPC-53-ACC',
                'brand' => 'ACC Limited',
                'base_price' => 370.00,
                'description' => 'ACC Gold OPC 53 Grade Cement — 50kg bag.',
            ],
            [
                'name' => 'Ambuja PPC Cement',
                'slug' => 'ambuja-ppc-cement',
                'category' => 'cement-concrete',
                'unit' => 'Bag',
                'hsn_code' => '252329',
                'sku' => 'CEM-PPC-AMB',
                'brand' => 'Ambuja Cements',
                'base_price' => 360.00,
                'description' => 'Ambuja Portland Pozzolana Cement — 50kg bag.',
            ],
            [
                'name' => 'TMT Fe-500 12mm Bars',
                'slug' => 'tmt-fe500-12mm-bars',
                'category' => 'steel-iron',
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
                'category' => 'steel-iron',
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
                'category' => 'paints-coatings',
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
                'category' => 'paints-coatings',
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
                'category' => 'tiles-flooring',
                'unit' => 'Sqft',
                'hsn_code' => '690721',
                'sku' => 'TLE-VIT-600P',
                'brand' => 'Kajaria',
                'base_price' => 65.00,
                'description' => 'Kajaria 600x600mm double charged vitrified floor tiles — polished finish.',
            ],
        ];

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
                        'brand_id' => $brandMap[$p['brand']] ?? null,
                        'base_price' => $p['base_price'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
