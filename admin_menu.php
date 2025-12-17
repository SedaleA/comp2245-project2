<?php

function getAdminNavItems(): array
{
    return [
        [
            'key' => 'home',
            'label' => 'Home',
            'href' => 'dashboard.php',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" role="img" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 9.5V21h5.5v-5.5h3V21H20V9.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ],
        [
            'key' => 'new_contact',
            'label' => 'New Contact',
            'href' => 'add_contact.php',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" role="img" aria-hidden="true"><path d="M12 12.5a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 19.5a7 7 0 0114 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 6.5v3m1.5-1.5h-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        ],
        [
            'key' => 'users',
            'label' => 'Users',
            'href' => 'view_users.php',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" role="img" aria-hidden="true"><path d="M8.5 12a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M15.5 12a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M3.5 19.5a5 5 0 015-5h1a5 5 0 015 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M13.5 15.5h2a5 5 0 015 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        ],
        [
            'key' => 'logout',
            'label' => 'Logout',
            'href' => 'logout.php',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" role="img" aria-hidden="true"><path d="M15 7V4.5A1.5 1.5 0 0013.5 3h-7A1.5 1.5 0 005 4.5v15A1.5 1.5 0 006.5 21h7a1.5 1.5 0 001.5-1.5V18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M10 12h10m0 0l-3-3m3 3l-3 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'divider' => true,
        ],
    ];
}

function renderAdminSidebar(string $activeKey): void
{
    foreach (getAdminNavItems() as $item) {
        $classes = ['nav-link'];
        if ($item['key'] === $activeKey) {
            $classes[] = 'active';
        }

        if (!empty($item['divider'])) {
            $classes[] = 'has-divider';
        }

        printf(
            '<a href="%s" class="%s"><span class="nav-icon">%s</span>%s</a>',
            htmlspecialchars($item['href'], ENT_QUOTES),
            implode(' ', $classes),
            $item['icon'],
            htmlspecialchars($item['label'], ENT_QUOTES)
        );
    }
}
