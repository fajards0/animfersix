import 'package:flutter/material.dart';

import '../../core/constants/app_colors.dart';

class CustomButton extends StatelessWidget {
  const CustomButton.primary({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
  }) : isPrimary = true;

  const CustomButton.secondary({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
  }) : isPrimary = false;

  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final bool isPrimary;

  @override
  Widget build(BuildContext context) {
    final backgroundColor = isPrimary ? AppColors.ember : const Color(0x12FFFFFF);
    final foregroundColor = Colors.white;

    return SizedBox(
      height: 56,
      child: FilledButton(
        onPressed: onPressed,
        style: FilledButton.styleFrom(
          backgroundColor: backgroundColor,
          foregroundColor: foregroundColor,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 18),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
            side: isPrimary ? BorderSide.none : const BorderSide(color: AppColors.stroke),
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon ?? Icons.play_arrow_rounded, size: 20),
            const SizedBox(width: 10),
            Text(
              label,
              style: const TextStyle(fontWeight: FontWeight.w800, letterSpacing: 0.15),
            ),
          ],
        ),
      ),
    );
  }
}
