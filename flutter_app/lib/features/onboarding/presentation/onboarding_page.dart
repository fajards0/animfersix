import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/constants/app_colors.dart';
import '../../../shared/widgets/custom_button.dart';
import '../../settings/controllers/settings_controller.dart';

class OnboardingPage extends ConsumerStatefulWidget {
  const OnboardingPage({super.key});

  @override
  ConsumerState<OnboardingPage> createState() => _OnboardingPageState();
}

class _OnboardingPageState extends ConsumerState<OnboardingPage> {
  late final PageController _pageController;
  int _page = 0;

  static const _items = [
    (
      title: 'Feel the premium anime lounge',
      message: 'Home dibuat seperti streaming app modern dengan hero cinematic, section terkurasi, dan ritme layout yang elegan.',
      icon: Icons.auto_awesome_rounded,
      colors: [Color(0xFFFF6A3D), Color(0xFFFD8C53)],
    ),
    (
      title: 'Search, bookmark, and continue faster',
      message: 'Pencarian realtime, bookmark lokal, dan progress card membuat sesi nonton terasa ringan dan profesional.',
      icon: Icons.rocket_launch_rounded,
      colors: [Color(0xFF53D1C8), Color(0xFF1F9D95)],
    ),
    (
      title: 'Built to scale into a real product',
      message: 'Riverpod, go_router, reusable widgets, dan local persistence sudah disiapkan agar app ini enak dikembangkan.',
      icon: Icons.widgets_rounded,
      colors: [Color(0xFFFF6A3D), Color(0xFF53D1C8)],
    ),
  ];

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  Future<void> _finish() async {
    await ref.read(settingsControllerProvider.notifier).completeOnboarding();
    if (mounted) {
      context.go('/');
    }
  }

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Scaffold(
      body: DecoratedBox(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF11131C), AppColors.abyss, Color(0xFF0C0F17)],
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(22, 16, 22, 22),
            child: Column(
              children: [
                Row(
                  children: [
                    const _BrandMark(),
                    const Spacer(),
                    TextButton(
                      onPressed: _finish,
                      child: const Text('Lewati'),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Expanded(
                  child: PageView.builder(
                    controller: _pageController,
                    itemCount: _items.length,
                    onPageChanged: (value) => setState(() => _page = value),
                    itemBuilder: (context, index) {
                      final item = _items[index];
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(28),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(34),
                                gradient: LinearGradient(
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                  colors: [
                                    item.colors.first.withValues(alpha: 0.28),
                                    item.colors.last.withValues(alpha: 0.14),
                                  ],
                                ),
                                border: Border.all(color: AppColors.stroke),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    width: 74,
                                    height: 74,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(24),
                                      gradient: LinearGradient(colors: item.colors),
                                      boxShadow: [
                                        BoxShadow(
                                          color: item.colors.first.withValues(alpha: 0.28),
                                          blurRadius: 22,
                                          offset: const Offset(0, 12),
                                        ),
                                      ],
                                    ),
                                    alignment: Alignment.center,
                                    child: Icon(item.icon, size: 34, color: Colors.white),
                                  ),
                                  const Spacer(),
                                  Text(
                                    '0${index + 1}',
                                    style: textTheme.displayLarge?.copyWith(color: Colors.white.withValues(alpha: 0.16)),
                                  ),
                                  const SizedBox(height: 10),
                                  Text(item.title, style: textTheme.displaySmall),
                                  const SizedBox(height: 14),
                                  Text(
                                    item.message,
                                    style: textTheme.bodyLarge?.copyWith(color: Colors.white70),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 28),
                          Row(
                            children: List.generate(
                              _items.length,
                              (index) => AnimatedContainer(
                                duration: const Duration(milliseconds: 260),
                                width: _page == index ? 28 : 10,
                                height: 10,
                                margin: const EdgeInsets.only(right: 8),
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(999),
                                  color: _page == index ? AppColors.ember : const Color(0x22FFFFFF),
                                ),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
                const SizedBox(height: 18),
                CustomButton.primary(
                  label: _page == _items.length - 1 ? 'Mulai sekarang' : 'Lanjut',
                  onPressed: () async {
                    if (_page == _items.length - 1) {
                      await _finish();
                      return;
                    }

                    await _pageController.nextPage(
                      duration: const Duration(milliseconds: 320),
                      curve: Curves.easeOutCubic,
                    );
                  },
                  icon: Icons.arrow_forward_rounded,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _BrandMark extends StatelessWidget {
  const _BrandMark();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        color: const Color(0x12FFFFFF),
        border: Border.all(color: AppColors.stroke),
      ),
      child: const Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.play_circle_fill_rounded, color: AppColors.ember),
          SizedBox(width: 10),
          Text(
            'AnimeStream',
            style: TextStyle(fontWeight: FontWeight.w800),
          ),
        ],
      ),
    );
  }
}
