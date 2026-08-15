<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admins
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'tier' => 'premium',
        ]);

        // Premium User
        User::create([
            'name' => 'Premium User',
            'email' => 'premium@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'tier' => 'premium',
        ]);

        // Free User
        User::create([
            'name' => 'Free User',
            'email' => 'free@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'tier' => 'free',
        ]);

        // Dummy Prompts
        \App\Models\Prompt::create([
            'title' => 'Cyberpunk City Skyline',
            'description' => 'A highly detailed neon-lit cyberpunk city at night.',
            'prompt_text' => 'cyberpunk cityscape, neon lights, rainy streets, dark alleys, high tech low life, 8k resolution, photorealistic, intricate detail, octane render, volumetric lighting --ar 16:9',
            'is_premium' => false,
        ]);

        \App\Models\Prompt::create([
            'title' => 'Watercolor Stray Cat',
            'description' => 'A gentle watercolor painting of a stray cat.',
            'prompt_text' => 'gentle watercolor painting of a calico tabby stray cat sitting under a cherry blossom tree, pastel colors, soft edges, studio ghibli style, emotional, peaceful',
            'is_premium' => false,
        ]);

        \App\Models\Prompt::create([
            'title' => 'Hyper-Realistic Astronaut Portrait',
            'description' => 'A stunning cinematic portrait of an astronaut on Mars.',
            'prompt_text' => 'cinematic portrait of an astronaut on mars, highly detailed spacesuit reflecting red dirt, intense stare, dramatic lighting, shot on 35mm lens, award-winning photography, hyper-realistic, 8k',
            'is_premium' => true,
        ]);

        \App\Models\Prompt::create([
            'title' => 'Steampunk Mechanical Owl',
            'description' => 'A complex clockwork owl.',
            'prompt_text' => 'steampunk mechanical clockwork owl made of brass and copper gears, glowing blue eyes, sitting on a vintage leather book, Victorian workshop background, hyper-detailed, unreal engine 5 render, cinematic lighting',
            'is_premium' => true,
        ]);
        
        \App\Models\Prompt::create([
            'title' => 'Matcha Strawberry Kawaii Cafe',
            'description' => 'A cute cafe with matcha and strawberry themes.',
            'prompt_text' => 'cute kawaii cafe interior design, matcha green and strawberry pink color palette, soft lighting, cozy atmosphere, small floral decorations, anime background style, highly detailed',
            'is_premium' => false,
        ]);
        
        \App\Models\Prompt::create([
            'title' => 'Abstract Glassmorphism UI',
            'description' => 'Background for a modern web app.',
            'prompt_text' => 'abstract glassmorphism background, floating translucent frosted glass shapes, vivid purple and pink gradient, clean modern UI aesthetic, smooth 3d render',
            'is_premium' => true,
        ]);
    }
}
