<svg viewBox="0 0 500 300" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Background -->
    <rect width="500" height="300" fill="white"/>
    
    <!-- Abstract figures representing people/team -->
    <!-- Teal figure -->
    <ellipse cx="120" cy="80" rx="25" ry="35" fill="#4FD1C7"/>
    <path d="M80 120 Q120 100 160 120 Q140 160 120 200 Q100 160 80 120" fill="#4FD1C7" opacity="0.8"/>
    
    <!-- Blue figure -->
    <ellipse cx="280" cy="75" rx="22" ry="32" fill="#3B82F6"/>
    <path d="M240 115 Q280 95 320 115 Q300 155 280 195 Q260 155 240 115" fill="#3B82F6" opacity="0.8"/>
    
    <!-- Orange figure -->
    <ellipse cx="200" cy="110" rx="20" ry="28" fill="#F97316"/>
    <path d="M170 140 Q200 125 230 140 Q215 175 200 205 Q185 175 170 140" fill="#F97316" opacity="0.8"/>
    
    <!-- Red/Pink flowing element connecting all -->
    <path d="M80 150 Q150 120 200 140 Q250 160 320 130 Q350 140 380 160 Q350 180 320 170 Q250 190 200 170 Q150 150 80 180" fill="#EF4444" opacity="0.7"/>
    
    <!-- Gradient overlay for depth -->
    <defs>
        <linearGradient id="teamGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4FD1C7;stop-opacity:0.3" />
            <stop offset="50%" style="stop-color:#3B82F6;stop-opacity:0.2" />
            <stop offset="100%" style="stop-color:#F97316;stop-opacity:0.3" />
        </linearGradient>
    </defs>
    <rect width="500" height="300" fill="url(#teamGradient)"/>
    
    <!-- Text elements -->
    <text x="50" y="250" font-family="Arial, sans-serif" font-size="36" font-weight="bold" fill="#F97316">SELLORA</text>
    <text x="50" y="270" font-family="Arial, sans-serif" font-size="12" fill="#4FD1C7" letter-spacing="2px">E - P O R I C H O Y</text>
    <text x="50" y="285" font-family="Arial, sans-serif" font-size="10" fill="#6B7280">POWERING TEAMS, DRIVING GROWTH.</text>
</svg>
