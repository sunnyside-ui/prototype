@props(['name'])
<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
<path d="{{ match($name) {
    'dashboard' => 'M3 10l9-7 9 7v10H3z M9 20v-6h6v6',
    'study', 'classes' => 'M3 5c4-1 7 0 9 2 2-2 5-3 9-2v14c-4-1-7 0-9 2-2-2-5-3-9-2z M12 7v14',
    'work', 'file' => 'M5 3h10l4 4v14H5z M14 3v5h5 M8 12h8 M8 16h8',
    'results' => 'M4 20h16 M7 16v-4 M12 16V8 M17 16V4',
    'program', 'admissions', 'folder' => 'M4 7h6l2 2h8v11H4z M4 7V4h6l2 3h8v2',
    'announcements' => 'M3 10h5l11-5v14L8 14H3z M8 14l2 6h3l-2-5',
    'notifications' => 'M5 17h14l-2-4V9a5 5 0 0 0-10 0v4z M10 21h4',
    'calendar' => 'M4 6h16v15H4z M8 3v6 M16 3v6 M4 11h16',
    'user' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
    'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M22 21v-2a4 4 0 0 0-3-3.9 M16 3.1a4 4 0 0 1 0 7.8',
    'clipboard' => 'M9 4h6v3H9z M7 5H5v16h14V5h-2 M9 12h6 M9 16h6',
    'check' => 'M20 6L9 17l-5-5',
    'circle-check' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18 M8 12l3 3 5-6',
    'clock' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18 M12 7v5l3 2',
    'upload' => 'M12 16V4 M7 9l5-5 5 5 M4 20h16',
    'award' => 'M12 15a6 6 0 1 0 0-12 6 6 0 0 0 0 12 M8.5 14L7 22l5-3 5 3-1.5-8',
    'edit' => 'M4 20h4L19 9l-4-4L4 16z M14 6l4 4',
    'lock' => 'M5 11h14v10H5z M8 11V7a4 4 0 0 1 8 0v4',
    default => 'M4 12h15 M13 6l6 6-6 6',
} }}"/>
</svg>
