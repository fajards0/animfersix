import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/constants/app_colors.dart';
import '../../settings/controllers/settings_controller.dart';

class SplashPage extends ConsumerStatefulWidget {
  const SplashPage({super.key});

  @override
  ConsumerState<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends ConsumerState<SplashPage> {
  @override
  void initState() {
    super.initState();
    Future<void>.delayed(const Duration(milliseconds: 3200), _proceed);
  }

  void _proceed() {
    if (!mounted) {
      return;
    }

    final settings = ref.read(settingsControllerProvider);
    context.go(settings.didFinishOnboarding ? '/' : '/onboarding');
  }

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Scaffold(
      body: Stack(
        fit: StackFit.expand,
        children: [
          Container(
            decoration: const BoxDecoration(
              gradient: RadialGradient(
                radius: 1.15,
                center: Alignment(0, -0.3),
                colors: [Color(0x55FF6A3D), Color(0x2209B7A6), AppColors.abyss],
              ),
            ),
          ),
          Positioned(
            top: -80,
            left: -10,
            child: _GlowOrb(
              size: 220,
              color: AppColors.ember.withValues(alpha: 0.34),
            ),
          ),
          Positioned(
            bottom: -60,
            right: -20,
            child: _GlowOrb(
              size: 260,
              color: AppColors.cyan.withValues(alpha: 0.22),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Column(
              children: [
                const Spacer(),
                Container(
                  width: 132,
                  height: 132,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(42),
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [AppColors.ember, AppColors.cyan],
                    ),
                    boxShadow: const [
                      BoxShadow(color: Color(0x66FF6A3D), blurRadius: 44, spreadRadius: 4),
                    ],
                  ),
                  child: Stack(
                    children: [
                      Positioned.fill(
                        child: DecoratedBox(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(42),
                            border: Border.all(color: const Color(0x22FFFFFF), width: 1.2),
                          ),
                        ),
                      ),
                      const Center(
                        child: Text(
                          'AS',
                          style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.w900,
                            fontSize: 42,
                            letterSpacing: 1,
                          ),
                        ),
                      ),
                    ],
                  ),
                )
                    .animate()
                    .fadeIn(duration: 700.ms)
                    .scale(begin: const Offset(0.84, 0.84), end: const Offset(1, 1), curve: Curves.easeOutBack, duration: 1000.ms)
                    .shimmer(delay: 1050.ms, duration: 920.ms),
                const SizedBox(height: 30),
                Text('AnimeStream', style: textTheme.displayLarge, textAlign: TextAlign.center)
                    .animate()
                    .fadeIn(delay: 300.ms, duration: 700.ms)
                    .slideY(begin: 0.2, end: 0, curve: Curves.easeOutCubic),
                const SizedBox(height: 12),
                Text(
                  'Curated anime streaming experience with a cinematic mobile feel.',
                  style: textTheme.bodyLarge?.copyWith(color: Colors.white70),
                  textAlign: TextAlign.center,
                )
                    .animate()
                    .fadeIn(delay: 700.ms, duration: 700.ms)
                    .slideY(begin: 0.24, end: 0, curve: Curves.easeOutCubic),
                const Spacer(),
                Text(
                  'Opening your next binge session...',
                  style: textTheme.bodySmall?.copyWith(letterSpacing: 0.3),
                ).animate().fadeIn(delay: 1200.ms, duration: 500.ms),
                const SizedBox(height: 28),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _GlowOrb extends StatelessWidget {
  const _GlowOrb({
    required this.size,
    required this.color,
  });

  final double size;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return IgnorePointer(
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: color,
              blurRadius: 90,
              spreadRadius: 22,
            ),
          ],
        ),
      ),
    );
  }
}
