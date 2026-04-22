<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Fer6origami')</title>
        <meta name="description" content="Fer6origami adalah hub anime dengan katalog luas, tampilan editorial modern, dan pengalaman jelajah yang lebih rapi di desktop maupun mobile.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Syne:wght@500;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Manrope', 'sans-serif'],
                            display: ['Syne', 'sans-serif'],
                        },
                        colors: {
                            ember: {
                                300: '#fda172',
                                400: '#ff8d5c',
                                500: '#ff6b3d',
                                600: '#e64f24',
                            },
                            tide: {
                                300: '#7ce0db',
                                400: '#4bc6c0',
                                500: '#1ba7a0',
                            },
                            coal: {
                                950: '#08090d',
                                900: '#111318',
                                800: '#1a1f27',
                            },
                        },
                        boxShadow: {
                            glow: '0 20px 60px rgba(0,0,0,.35)',
                            card: '0 18px 40px rgba(8,9,13,.22)',
                        },
                    },
                },
            };
        </script>
        <style>
            :root {
                --bg: #08090d;
                --panel: rgba(20, 24, 31, 0.82);
                --panel-soft: rgba(255, 255, 255, 0.045);
                --line: rgba(255, 255, 255, 0.09);
                --text-soft: #8f98a7;
                --accent: #ff6b3d;
                --accent-soft: rgba(255, 107, 61, 0.18);
                --mint: #4bc6c0;
            }

            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(255, 107, 61, 0.4) rgba(255, 255, 255, 0.06);
            }

            body {
                background:
                    radial-gradient(circle at top left, rgba(255, 107, 61, 0.22), transparent 24%),
                    radial-gradient(circle at 88% 8%, rgba(75, 198, 192, 0.18), transparent 18%),
                    radial-gradient(circle at 50% 30%, rgba(255, 255, 255, 0.04), transparent 24%),
                    linear-gradient(180deg, #08090d 0%, #0c1015 40%, #08090d 100%);
                overflow-x: hidden;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
                background-size: 72px 72px;
                mask-image: radial-gradient(circle at center, black 35%, transparent 85%);
                opacity: 0.4;
            }

            .glass-panel {
                background: var(--panel);
                backdrop-filter: blur(20px);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.03), 0 18px 50px rgba(0,0,0,0.25);
            }

            .orb {
                position: absolute;
                border-radius: 9999px;
                filter: blur(16px);
                opacity: 0.5;
                pointer-events: none;
            }

            .brand-frame {
                position: relative;
                isolation: isolate;
            }

            .brand-frame::after {
                content: "";
                position: absolute;
                inset: 0;
                border: 1px solid rgba(255,255,255,0.06);
                border-radius: inherit;
                pointer-events: none;
            }

            .section-kicker {
                letter-spacing: 0.35em;
                text-transform: uppercase;
                color: var(--text-soft);
            }

            .hero-scrim {
                background: linear-gradient(120deg, rgba(8, 9, 13, 0.94) 15%, rgba(8, 9, 13, 0.72) 52%, rgba(8, 9, 13, 0.18) 100%);
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
            }

            select option {
                background: #14181f;
                color: #f8fafc;
            }
        </style>
    </head>
    <body class="min-h-screen bg-coal-950 font-sans text-slate-100">
        <div class="relative min-h-screen overflow-hidden">
            <div class="orb left-[-4rem] top-20 h-48 w-48 bg-ember-500/30"></div>
            <div class="orb bottom-20 right-[-3rem] h-56 w-56 bg-tide-400/20"></div>

            @include('layouts.partials.navbar')

            <main class="relative z-10">
                @if (request()->routeIs('admin.*'))
                    <div class="mx-auto flex w-full max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:px-8">
                        @include('admin.partials.sidebar')

                        <div class="min-w-0 flex-1">
                            <x-flash-message />
                            @yield('content')
                        </div>
                    </div>
                @else
                    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                        <x-flash-message />
                        @yield('content')
                    </div>
                @endif
            </main>

            @include('layouts.partials.footer')
        </div>

        <script>
            document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const target = document.getElementById(button.dataset.menuToggle);

                    if (!target) {
                        return;
                    }

                    target.classList.toggle('hidden');
                });
            });

            document.querySelectorAll('img[data-fallback-src]').forEach((image) => {
                image.addEventListener('error', () => {
                    const fallback = image.dataset.fallbackSrc;

                    if (!fallback || image.src === fallback) {
                        return;
                    }

                    image.src = fallback;
                }, { once: true });
            });
        </script>
    </body>
</html>
