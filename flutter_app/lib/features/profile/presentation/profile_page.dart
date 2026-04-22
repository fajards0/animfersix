import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/constants/app_colors.dart';
import '../../../shared/widgets/section_title.dart';
import '../../settings/controllers/settings_controller.dart';

class ProfilePage extends ConsumerWidget {
  const ProfilePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final settings = ref.watch(settingsControllerProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Profile & settings')),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(20, 10, 20, 24),
        children: [
          const SectionTitle(
            kicker: 'Profile',
            title: 'Preferensi pengguna',
            subtitle: 'Tempat sederhana untuk menyesuaikan pengalaman app sebelum auth account ditambahkan.',
          ),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(28),
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0x24FF6A3D), Color(0x1453D1C8)],
              ),
              border: Border.all(color: AppColors.stroke),
            ),
            child: Row(
              children: [
                Container(
                  width: 64,
                  height: 64,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(22),
                    gradient: const LinearGradient(
                      colors: [AppColors.ember, AppColors.cyan],
                    ),
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    settings.profileName.characters.first.toUpperCase(),
                    style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 24),
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(settings.profileName, style: Theme.of(context).textTheme.titleLarge),
                      const SizedBox(height: 4),
                      Text(
                        'AnimeStream premium member',
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          Card(
            child: Column(
              children: [
                SwitchListTile(
                  value: settings.themeMode != ThemeMode.light,
                  onChanged: (value) => ref.read(settingsControllerProvider.notifier).toggleTheme(value),
                  title: const Text('Dark mode'),
                  subtitle: const Text('Gunakan tampilan gelap premium sebagai mode utama.'),
                ),
                ListTile(
                  title: const Text('Edit profile name'),
                  subtitle: const Text('Ganti nama tampilan sederhana'),
                  trailing: const Icon(Icons.chevron_right_rounded),
                  onTap: () => _showNameDialog(context, ref, settings.profileName),
                ),
                const Divider(height: 1),
                const ListTile(
                  title: Text('About app'),
                  subtitle: Text('AnimeStream version 1.0.0'),
                ),
                const Divider(height: 1),
                const ListTile(
                  title: Text('Privacy policy'),
                  subtitle: Text('Placeholder kebijakan privasi untuk tahap publish.'),
                ),
                const Divider(height: 1),
                const ListTile(
                  title: Text('Terms & conditions'),
                  subtitle: Text('Placeholder syarat dan ketentuan aplikasi.'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _showNameDialog(BuildContext context, WidgetRef ref, String currentName) async {
    final controller = TextEditingController(text: currentName);

    await showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Edit profile name'),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(hintText: 'Masukkan nama'),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () {
              final nextName = controller.text.trim().isEmpty ? 'Anime Explorer' : controller.text.trim();
              ref.read(settingsControllerProvider.notifier).updateProfileName(nextName);
              Navigator.of(context).pop();
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }
}
