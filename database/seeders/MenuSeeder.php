<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuAddon;
use App\Models\MenuRecipe;
use App\Models\MenuVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $catMain    = Category::where('slug', 'main-course')->first();
        $catFav     = Category::where('slug', 'favorit')->first();
        $catFood    = Category::where('slug', 'food')->first();
        $catDrink   = Category::where('slug', 'drink')->first();
        $catCoffee  = Category::where('slug', 'coffee')->first();
        $catDessert = Category::where('slug', 'dessert')->first();
        $catSalad   = Category::where('slug', 'salad')->first();
        $catSoup    = Category::where('slug', 'soup')->first();
        $catIce     = Category::where('slug', 'ice-cream')->first();

        // Ingredients references
        $ingWagyu   = Ingredient::where('sku', 'ING-WAGYU-01')->first();
        $ingBuntut  = Ingredient::where('sku', 'ING-BUNTUT-01')->first();
        $ingAyam    = Ingredient::where('sku', 'ING-AYAM-01')->first();
        $ingBebek   = Ingredient::where('sku', 'ING-BEBEK-01')->first();
        $ingTelur   = Ingredient::where('sku', 'ING-TELUR-01')->first();
        $ingBeras   = Ingredient::where('sku', 'ING-BERAS-01')->first();
        $ingBawang  = Ingredient::where('sku', 'ING-BAWANG-01')->first();
        $ingCabe    = Ingredient::where('sku', 'ING-CABE-01')->first();
        $ingMinyak  = Ingredient::where('sku', 'ING-MINYAK-01')->first();
        $ingMozza   = Ingredient::where('sku', 'ING-MOZZA-01')->first();
        $ingKopi    = Ingredient::where('sku', 'ING-KOPI-01')->first();
        $ingSusu    = Ingredient::where('sku', 'ING-SUSU-01')->first();
        $ingMatcha  = Ingredient::where('sku', 'ING-MATCHA-01')->first();
        $ingAren    = Ingredient::where('sku', 'ING-AREN-01')->first();
        $ingRamen   = Ingredient::where('sku', 'ING-RAMEN-01')->first();

        $menusData = [
            // 1. Favorit / Main Course
            [
                'category_id' => $catFav?->id ?? $catMain->id,
                'name' => 'Wagyu Beef Truffle Donburi',
                'slug' => 'wagyu-beef-truffle-donburi',
                'description' => 'Irisan daging sapi Wagyu MB5 lembut beraroma minyak truffle di atas nasi jepang hangat dengan onsen egg.',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
                'price' => 88000,
                'cost_price' => 38000,
                'is_favorite' => true,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Ukuran', 'name' => 'Reguler', 'price' => 0],
                    ['group' => 'Ukuran', 'name' => 'Double Meat (+60g)', 'price' => 32000],
                    ['group' => 'Tingkat Kematangan', 'name' => 'Medium Rare', 'price' => 0],
                    ['group' => 'Tingkat Kematangan', 'name' => 'Well Done', 'price' => 0],
                ],
                'recipes' => [
                    ['ing' => $ingWagyu, 'qty' => 120], // 120 gram
                    ['ing' => $ingBeras, 'qty' => 150], // 150 gram beras
                    ['ing' => $ingTelur, 'qty' => 1],   // 1 butir onsen egg
                    ['ing' => $ingMinyak, 'qty' => 15], // 15 ml
                ]
            ],
            [
                'category_id' => $catFav?->id ?? $catMain->id,
                'name' => 'Nasi Goreng Wagyu Kecombrang',
                'slug' => 'nasi-goreng-wagyu-kecombrang',
                'description' => 'Nasi goreng harum rempah bunga kecombrang dengan potongan Wagyu empuk dan telur mata sapi renyah.',
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=600&auto=format&fit=crop&q=80',
                'price' => 65000,
                'cost_price' => 26000,
                'is_favorite' => true,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Level Pedas', 'name' => 'Tidak Pedas', 'price' => 0],
                    ['group' => 'Level Pedas', 'name' => 'Sedang (Level 1)', 'price' => 0],
                    ['group' => 'Level Pedas', 'name' => 'Pedas Nampol (Level 2)', 'price' => 0],
                ],
                'recipes' => [
                    ['ing' => $ingWagyu, 'qty' => 70],
                    ['ing' => $ingBeras, 'qty' => 150],
                    ['ing' => $ingTelur, 'qty' => 1],
                    ['ing' => $ingBawang, 'qty' => 25],
                    ['ing' => $ingCabe, 'qty' => 15],
                    ['ing' => $ingMinyak, 'qty' => 20],
                ]
            ],
            [
                'category_id' => $catMain?->id,
                'name' => 'Sop Buntut Sapi Kuah Hangat',
                'slug' => 'sop-buntut-sapi-kuah-hangat',
                'description' => 'Potongan buntut sapi empuk berkuah kaldu rempah gurih harum cengkeh, wortel, kentang, dan emping.',
                'image' => 'https://images.unsplash.com/photo-1547928576-a4a33237cbc3?w=600&auto=format&fit=crop&q=80',
                'price' => 85000,
                'cost_price' => 37000,
                'is_favorite' => true,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Penyajian', 'name' => 'Kuah Rebus Segar', 'price' => 0],
                    ['group' => 'Penyajian', 'name' => 'Bakar Madu (Kuah Terpisah)', 'price' => 8000],
                ],
                'recipes' => [
                    ['ing' => $ingBuntut, 'qty' => 250],
                    ['ing' => $ingBawang, 'qty' => 30],
                    ['ing' => $ingCabe, 'qty' => 10],
                ]
            ],
            [
                'category_id' => $catMain?->id,
                'name' => 'Chicken Katsu Japanese Curry Rice',
                'slug' => 'chicken-katsu-curry-rice',
                'description' => 'Fillet ayam crispy renyah keemasan disiram saus kari jepang kental kaya rasa dengan wortel dan kentang lembut.',
                'image' => 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=600&auto=format&fit=crop&q=80',
                'price' => 52000,
                'cost_price' => 19000,
                'is_favorite' => false,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Level Pedas Kari', 'name' => 'Mild', 'price' => 0],
                    ['group' => 'Level Pedas Kari', 'name' => 'Hot Spicy', 'price' => 2000],
                ],
                'recipes' => [
                    ['ing' => $ingAyam, 'qty' => 160],
                    ['ing' => $ingBeras, 'qty' => 150],
                    ['ing' => $ingMinyak, 'qty' => 40],
                ]
            ],
            [
                'category_id' => $catMain?->id,
                'name' => 'Bebek Goreng Sambal Korek Amarta',
                'slug' => 'bebek-goreng-sambal-korek',
                'description' => 'Bebek bumbu ungkep gurih meresap dengan kulit super garing, disajikan lalapan segar dan sambal korek uleg pedas.',
                'image' => 'https://images.unsplash.com/photo-1514944298352-f6745145b23e?w=600&auto=format&fit=crop&q=80',
                'price' => 58000,
                'cost_price' => 24000,
                'is_favorite' => false,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Porsi', 'name' => 'Paha', 'price' => 0],
                    ['group' => 'Porsi', 'name' => 'Dada', 'price' => 2000],
                ],
                'recipes' => [
                    ['ing' => $ingBebek, 'qty' => 0.25], // 1/4 ekor
                    ['ing' => $ingCabe, 'qty' => 30],
                    ['ing' => $ingBawang, 'qty' => 20],
                    ['ing' => $ingMinyak, 'qty' => 50],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Shoyu Ramen Chashu Sapi',
                'slug' => 'shoyu-ramen-chashu-sapi',
                'description' => 'Mie ramen kenyal dengan kuah kaldu shoyu bening gurih, topping daging sapi iris lembut, ajitsuke tamago, dan nori.',
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=600&auto=format&fit=crop&q=80',
                'price' => 56000,
                'cost_price' => 21000,
                'is_favorite' => false,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Tekstur Mie', 'name' => 'Normal', 'price' => 0],
                    ['group' => 'Tekstur Mie', 'name' => 'Kenyal / Hard', 'price' => 0],
                ],
                'recipes' => [
                    ['ing' => $ingRamen, 'qty' => 1],
                    ['ing' => $ingWagyu, 'qty' => 60],
                    ['ing' => $ingTelur, 'qty' => 1],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Ayam Bakar Madu Taliwang',
                'slug' => 'ayam-bakar-madu-taliwang',
                'description' => 'Ayam bakar dengan bumbu khas Taliwang pedas manis meresap beraroma asap arang bakar.',
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600&auto=format&fit=crop&q=80',
                'price' => 45000,
                'cost_price' => 17000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingAyam, 'qty' => 200],
                    ['ing' => $ingCabe, 'qty' => 20],
                    ['ing' => $ingBawang, 'qty' => 20],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Sate Ayam Madura Saus Kacang (10 Tusuk)',
                'slug' => 'sate-ayam-madura',
                'description' => 'Daging ayam empuk berbumbu dipanggang arang, disiram bumbu kacang lembut legit dan kecap manis.',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600&auto=format&fit=crop&q=80',
                'price' => 42000,
                'cost_price' => 16000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingAyam, 'qty' => 220],
                    ['ing' => $ingBawang, 'qty' => 15],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Gyoza Ayam Panggang (5 pcs)',
                'slug' => 'gyoza-ayam-panggang',
                'description' => 'Pangsit jepang isi daging ayam cincang renyah di bawah, lembut juicy di atas dengan saus tare asam pedas.',
                'image' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=600&auto=format&fit=crop&q=80',
                'price' => 32000,
                'cost_price' => 11000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingAyam, 'qty' => 100],
                    ['ing' => $ingBawang, 'qty' => 15],
                    ['ing' => $ingMinyak, 'qty' => 10],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Rendang Daging Sapi Minang',
                'slug' => 'rendang-daging-sapi-minang',
                'description' => 'Daging sapi dimasak perlahan dengan santan dan rempah otentik hingga hitam kecoklatan kaya minyak bumbu.',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
                'price' => 55000,
                'cost_price' => 22000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingWagyu, 'qty' => 140],
                    ['ing' => $ingBawang, 'qty' => 30],
                    ['ing' => $ingCabe, 'qty' => 20],
                ]
            ],

            // 2. Coffee & Drinks
            [
                'category_id' => $catCoffee?->id,
                'name' => 'Kopi Susu Gula Aren Amarta',
                'slug' => 'kopi-susu-gula-aren',
                'description' => 'Espresso biji kopi Arabika Gayo berpadu dengan susu fresh milk creamy dan manis harum gula aren organik.',
                'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=80',
                'price' => 28000,
                'cost_price' => 8500,
                'is_favorite' => true,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Penyajian', 'name' => 'Ice (Dingin)', 'price' => 0],
                    ['group' => 'Penyajian', 'name' => 'Hot (Hangat)', 'price' => 0],
                    ['group' => 'Tingkat Gula', 'name' => 'Normal Sweet', 'price' => 0],
                    ['group' => 'Tingkat Gula', 'name' => 'Less Sweet (50%)', 'price' => 0],
                ],
                'recipes' => [
                    ['ing' => $ingKopi, 'qty' => 18],  // 18 gram espresso
                    ['ing' => $ingSusu, 'qty' => 120], // 120 ml
                    ['ing' => $ingAren, 'qty' => 25],  // 25 ml
                ]
            ],
            [
                'category_id' => $catCoffee?->id,
                'name' => 'Caffe Latte Signature',
                'slug' => 'caffe-latte-signature',
                'description' => 'Double shot espresso dipadukan dengan microfoam steamed milk lembut, seimbang dan kaya rasa.',
                'image' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=600&auto=format&fit=crop&q=80',
                'price' => 35000,
                'cost_price' => 10500,
                'is_favorite' => false,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Suhu', 'name' => 'Hot', 'price' => 0],
                    ['group' => 'Suhu', 'name' => 'Iced', 'price' => 2000],
                ],
                'recipes' => [
                    ['ing' => $ingKopi, 'qty' => 20],
                    ['ing' => $ingSusu, 'qty' => 180],
                ]
            ],
            [
                'category_id' => $catCoffee?->id,
                'name' => 'Americano Arabika Single Origin',
                'slug' => 'americano-arabika',
                'description' => 'Double espresso dipadu air mineral murni, menonjolkan notes buah dan coklat khas Arabika Gayo.',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&auto=format&fit=crop&q=80',
                'price' => 25000,
                'cost_price' => 5000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingKopi, 'qty' => 18],
                ]
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Uji Matcha Latte Creamy',
                'slug' => 'uji-matcha-latte',
                'description' => 'Bubuk matcha murni asal Uji, Kyoto dipadukan dengan susu fresh milk hangat atau dingin yang velvety.',
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&auto=format&fit=crop&q=80',
                'price' => 36000,
                'cost_price' => 11000,
                'is_favorite' => true,
                'is_available' => true,
                'variants' => [
                    ['group' => 'Suhu', 'name' => 'Iced', 'price' => 0],
                    ['group' => 'Suhu', 'name' => 'Hot', 'price' => 0],
                ],
                'recipes' => [
                    ['ing' => $ingMatcha, 'qty' => 10],
                    ['ing' => $ingSusu, 'qty' => 160],
                ]
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Ice Lychee Tea with Real Fruit',
                'slug' => 'ice-lychee-tea',
                'description' => 'Teh hitam wangi diseduh segar dengan sirup leci dan 2 buah leci utuh manis menyegarkan.',
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&auto=format&fit=crop&q=80',
                'price' => 26000,
                'cost_price' => 7000,
                'is_favorite' => false,
                'is_available' => true,
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Fresh Lemonade Mint Cooler',
                'slug' => 'fresh-lemonade-mint',
                'description' => 'Perasan lemon segar dengan daun mint dingin dan madu murni, penawar dahaga terbaik.',
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&auto=format&fit=crop&q=80',
                'price' => 25000,
                'cost_price' => 6000,
                'is_favorite' => false,
                'is_available' => true,
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Jus Alpukat Kocok Kerok',
                'slug' => 'jus-alpukat-kerok',
                'description' => 'Alpukat mentega matang pohon kental dikocok dengan susu kental manis dan lelehan saus coklat.',
                'image' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80',
                'price' => 30000,
                'cost_price' => 10000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingSusu, 'qty' => 80],
                ]
            ],

            // 3. Dessert & Ice Cream
            [
                'category_id' => $catDessert?->id,
                'name' => 'Matcha Molten Lava Cake',
                'slug' => 'matcha-molten-lava-cake',
                'description' => 'Kue coklat matcha lembut dengan lelehan saus matcha hijau hangat di bagian dalam saat dibelah.',
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
                'price' => 45000,
                'cost_price' => 15000,
                'is_favorite' => true,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingMatcha, 'qty' => 15],
                    ['ing' => $ingTelur, 'qty' => 2],
                    ['ing' => $ingSusu, 'qty' => 50],
                ]
            ],
            [
                'category_id' => $catDessert?->id,
                'name' => 'Japanese Souffle Pancake Fluffy',
                'slug' => 'japanese-souffle-pancake',
                'description' => 'Pancake khas Jepang super lembut mengembang seperti awan, disajikan dengan maple syrup dan whipped butter.',
                'image' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&auto=format&fit=crop&q=80',
                'price' => 48000,
                'cost_price' => 13000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingTelur, 'qty' => 2],
                    ['ing' => $ingSusu, 'qty' => 60],
                ]
            ],
            [
                'category_id' => $catDessert?->id,
                'name' => 'Pisang Bakar Coklat Keju Wijen',
                'slug' => 'pisang-bakar-coklat-keju',
                'description' => 'Pisang raja bakar karamel ditaburi parutan keju cheddar melimpah, meses coklat dan susu manis.',
                'image' => 'https://images.unsplash.com/photo-1528975604071-b4dc52a2d18c?w=600&auto=format&fit=crop&q=80',
                'price' => 28000,
                'cost_price' => 8000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingMozza, 'qty' => 40],
                    ['ing' => $ingSusu, 'qty' => 30],
                ]
            ],
            [
                'category_id' => $catIce?->id,
                'name' => 'Gelato Artisan Uji Matcha (2 Scoop)',
                'slug' => 'gelato-artisan-matcha',
                'description' => 'Gelato italia bertekstur padat lembut dengan rasa teh hijau pekat pahit manis otentik.',
                'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=600&auto=format&fit=crop&q=80',
                'price' => 38000,
                'cost_price' => 12000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingMatcha, 'qty' => 10],
                    ['ing' => $ingSusu, 'qty' => 120],
                ]
            ],
            [
                'category_id' => $catIce?->id,
                'name' => 'Es Teler Sultan Durian',
                'slug' => 'es-teler-sultan-durian',
                'description' => 'Alpukat, kelapa muda, nangka wangi dengan kuah santan susu creamy dan topping daging durian medan asli.',
                'image' => 'https://images.unsplash.com/photo-1501443762994-82bd5dace89a?w=600&auto=format&fit=crop&q=80',
                'price' => 45000,
                'cost_price' => 18000,
                'is_favorite' => true,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingSusu, 'qty' => 90],
                ]
            ],

            // 4. Salad & Soup
            [
                'category_id' => $catSalad?->id,
                'name' => 'Caesar Salad with Crispy Chicken Fillet',
                'slug' => 'caesar-salad-chicken',
                'description' => 'Selada romaine renyah segar, croutons gurih, taburan keju parmesan dan irisan dada ayam krispi.',
                'image' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=600&auto=format&fit=crop&q=80',
                'price' => 42000,
                'cost_price' => 14000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingAyam, 'qty' => 120],
                ]
            ],
            [
                'category_id' => $catSalad?->id,
                'name' => 'Kani Wakame Salad Dressing Wijen',
                'slug' => 'kani-wakame-salad',
                'description' => 'Rumput laut wakame jepang kenyal dengan suwiran kani crabstick, tobiko, dan roasted sesame dressing.',
                'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
                'price' => 38000,
                'cost_price' => 12500,
                'is_favorite' => false,
                'is_available' => true,
            ],
            [
                'category_id' => $catSoup?->id,
                'name' => 'Soto Betawi Kuah Susu Rempah',
                'slug' => 'soto-betawi-kuah-susu',
                'description' => 'Potongan daging sapi gurih dalam kuah kaldu susu rempah harum tanpa santan berat, disajikan dengan emping dan tomat.',
                'image' => 'https://images.unsplash.com/photo-1547928576-a4a33237cbc3?w=600&auto=format&fit=crop&q=80',
                'price' => 54000,
                'cost_price' => 20000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingWagyu, 'qty' => 120],
                    ['ing' => $ingSusu, 'qty' => 120],
                    ['ing' => $ingBawang, 'qty' => 25],
                ]
            ],
            [
                'category_id' => $catSoup?->id,
                'name' => 'Rawon Daging Sapi Kluwek Surabaya',
                'slug' => 'rawon-daging-surabaya',
                'description' => 'Sup daging sapi kuah hitam pekat kluwek khas Jawa Timur, disajikan bersama sambal terasi dan kecambah.',
                'image' => 'https://images.unsplash.com/photo-1572656639666-054f49495147?w=600&auto=format&fit=crop&q=80',
                'price' => 52000,
                'cost_price' => 19500,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingWagyu, 'qty' => 110],
                    ['ing' => $ingBawang, 'qty' => 25],
                ]
            ],
            [
                'category_id' => $catFood?->id,
                'name' => 'Gado-Gado Siram Spesial Saus Mede',
                'slug' => 'gado-gado-siram-mede',
                'description' => 'Sayuran rebus segar, tahu, tempe, telur rebus disiram saus kacang mede lembut kental yang gurih legit.',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
                'price' => 38000,
                'cost_price' => 12000,
                'is_favorite' => false,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingTelur, 'qty' => 1],
                ]
            ],
            [
                'category_id' => $catMain?->id,
                'name' => 'Salmon Teriyaki Glaze Donburi',
                'slug' => 'salmon-teriyaki-donburi',
                'description' => 'Fillet ikan salmon Norwegia panggang diselimuti saus teriyaki manis gurih di atas nasi hangat dan brokoli.',
                'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&auto=format&fit=crop&q=80',
                'price' => 95000,
                'cost_price' => 45000,
                'is_favorite' => true,
                'is_available' => true,
                'recipes' => [
                    ['ing' => $ingBeras, 'qty' => 150],
                ]
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Ice Lemon Tea Segar',
                'slug' => 'ice-lemon-tea',
                'description' => 'Teh hitam seduh dengan sari lemon segar dan irisan lemon iris.',
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&auto=format&fit=crop&q=80',
                'price' => 18000,
                'cost_price' => 4000,
                'is_favorite' => false,
                'is_available' => true,
            ],
            [
                'category_id' => $catDrink?->id,
                'name' => 'Mineral Water Artisan 600ml',
                'slug' => 'mineral-water-artisan',
                'description' => 'Air mineral pegunungan botol kaca dingin.',
                'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=600&auto=format&fit=crop&q=80',
                'price' => 12000,
                'cost_price' => 3500,
                'is_favorite' => false,
                'is_available' => true,
            ],
        ];

        // Global Addons
        $addons = [
            ['name' => 'Telur Mata Sapi / Onsen Egg', 'price' => 6000],
            ['name' => 'Extra Keju Mozzarella Melted', 'price' => 12000],
            ['name' => 'Extra Sambal Korek Uleg', 'price' => 5000],
            ['name' => 'Nasi Putih Pandan Wangi', 'price' => 8000],
            ['name' => 'Extra Wagyu Slice (50g)', 'price' => 28000],
        ];

        foreach ($addons as $addon) {
            MenuAddon::firstOrCreate(['name' => $addon['name']], $addon);
        }

        // Seed Menus, Variants, and Recipes
        foreach ($menusData as $m) {
            $variants = $m['variants'] ?? [];
            $recipes  = $m['recipes'] ?? [];
            unset($m['variants'], $m['recipes']);

            $menu = Menu::firstOrCreate(['slug' => $m['slug']], $m);

            // Variants
            foreach ($variants as $v) {
                MenuVariant::firstOrCreate(
                    [
                        'menu_id' => $menu->id,
                        'group_name' => $v['group'],
                        'name' => $v['name'],
                    ],
                    [
                        'price_adjustment' => $v['price'],
                        'is_available' => true,
                    ]
                );
            }

            // Recipes
            foreach ($recipes as $r) {
                if ($r['ing']) {
                    MenuRecipe::firstOrCreate(
                        [
                            'menu_id' => $menu->id,
                            'ingredient_id' => $r['ing']->id,
                        ],
                        [
                            'quantity_required' => $r['qty'],
                        ]
                    );
                }
            }
        }
    }
}
