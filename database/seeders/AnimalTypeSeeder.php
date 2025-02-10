<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnimalType;

class AnimalTypeSeeder extends Seeder
{
    public function run()
    {
        $animalTypes = [
            // Mammals
            ['en' => 'Dog', 'ar' => 'كلب'],
            ['en' => 'Cat', 'ar' => 'قط'],
            ['en' => 'Horse', 'ar' => 'حصان'],
            ['en' => 'Elephant', 'ar' => 'فيل'],
            ['en' => 'Lion', 'ar' => 'أسد'],
            ['en' => 'Tiger', 'ar' => 'نمر'],
            ['en' => 'Bear', 'ar' => 'دب'],
            ['en' => 'Wolf', 'ar' => 'ذئب'],
            ['en' => 'Rabbit', 'ar' => 'أرنب'],
            ['en' => 'Cow', 'ar' => 'بقرة'],
            ['en' => 'Sheep', 'ar' => 'خروف'],
            ['en' => 'Goat', 'ar' => 'ماعز'],
            ['en' => 'Deer', 'ar' => 'غزال'],
            
            // Birds
            ['en' => 'Parrot', 'ar' => 'ببغاء'],
            ['en' => 'Eagle', 'ar' => 'نسر'],
            ['en' => 'Owl', 'ar' => 'بومة'],
            ['en' => 'Pigeon', 'ar' => 'حمامة'],
            ['en' => 'Sparrow', 'ar' => 'عصفور'],
            ['en' => 'Hawk', 'ar' => 'صقر'],
            ['en' => 'Peacock', 'ar' => 'طاووس'],
            ['en' => 'Flamingo', 'ar' => 'فلامنغو'],
            ['en' => 'Penguin', 'ar' => 'بطريق'],
            
            // Reptiles
            ['en' => 'Snake', 'ar' => 'ثعبان'],
            ['en' => 'Lizard', 'ar' => 'سحلية'],
            ['en' => 'Crocodile', 'ar' => 'تمساح'],
            ['en' => 'Turtle', 'ar' => 'سلحفاة'],
            ['en' => 'Chameleon', 'ar' => 'حرباء'],
            
            // Aquatic Animals
            ['en' => 'Shark', 'ar' => 'قرش'],
            ['en' => 'Dolphin', 'ar' => 'دلفين'],
            ['en' => 'Whale', 'ar' => 'حوت'],
            ['en' => 'Octopus', 'ar' => 'أخطبوط'],
            ['en' => 'Jellyfish', 'ar' => 'قنديل البحر'],
            ['en' => 'Starfish', 'ar' => 'نجم البحر'],
            ['en' => 'Crab', 'ar' => 'سلطعون'],
            ['en' => 'Seahorse', 'ar' => 'فرس البحر'],
            
            // Insects
            ['en' => 'Butterfly', 'ar' => 'فراشة'],
            ['en' => 'Bee', 'ar' => 'نحلة'],
            ['en' => 'Ant', 'ar' => 'نملة'],
            ['en' => 'Spider', 'ar' => 'عنكبوت'],
            ['en' => 'Mosquito', 'ar' => 'بعوضة'],
            ['en' => 'Grasshopper', 'ar' => 'جرادة'],
        ];

        foreach ($animalTypes as $type) {
            AnimalType::create([
                'name' => $type,
            ]);
        }
    }
}
