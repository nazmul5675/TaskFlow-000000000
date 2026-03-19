<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow — Your Smart Task Manager</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.04)',
                        'card-hover': '0 4px 16px 0 rgba(0,0,0,0.10), 0 2px 6px -2px rgba(0,0,0,0.06)',
                        'auth': '0 20px 60px -12px rgba(79,70,229,0.18)',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/TaskFlow/assets/style.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">