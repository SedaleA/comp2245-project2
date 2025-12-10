<?php

function getAdminNavItems(): array
{
    return [
        [
            'key' => 'home',
            'label' => 'Home',
            'href' => 'dashboard.php',
            'icon' => '<svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M3 10l9-7 9 7h-3v9h-4v-6h-4v6H6v-9H3z"/></svg>',
        ],
        [
            'key' => 'new_contact',
            'label' => 'New Contact',
            'href' => 'add_contact.php',
            'icon' => '<svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M12 5a4 4 0 100 8 4 4 0 000-8zm-8 12a8 8 0 0116 0H4zm10-7v3H7v2h5v3h2v-3h5v-2h-5v-3h-2z"/></svg>',
        ],
        [
            'key' => 'users',
            'label' => 'Users',
            'href' => 'view_users.php',
            'icon' => '<svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M17 12a3 3 0 100-6 3 3 0 000 6zm-10 0a3 3 0 100-6 3 3 0 000 6zm0 2a5 5 0 00-5 5h2a3 3 0 013-3h4a3 3 0 013 3h2a5 5 0 00-5-5H7zM17 14a3 3 0 00-3 3h6a3 3 0 00-3-3z"/></svg>',
        ],
        [
            'key' => 'logout',
            'label' => 'Logout',
            'href' => 'logout.php',
            'icon' => '<svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M16 17l5-5-5-5v3H9v4h7v3zM4 3h11v2H4v14h11v2H4a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>',
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
