import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';

class LoadingSkeleton extends StatelessWidget {
  const LoadingSkeleton({
    super.key,
    this.height = 18,
    this.width = double.infinity,
    this.radius = 16,
  });

  final double height;
  final double width;
  final double radius;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: height,
      width: width,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(radius),
        color: const Color(0x14FFFFFF),
      ),
    )
        .animate(onPlay: (controller) => controller.repeat())
        .fade(begin: 0.35, end: 1, duration: 950.ms)
        .fade(begin: 1, end: 0.35, duration: 950.ms);
  }
}
