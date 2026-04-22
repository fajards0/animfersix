import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../shared/widgets/anime_card.dart';
import '../../../shared/widgets/app_error_state.dart';
import '../../../shared/widgets/empty_state.dart';
import '../../../shared/widgets/loading_skeleton.dart';
import '../../../shared/widgets/section_title.dart';
import '../controllers/bookmarks_controller.dart';

class BookmarksPage extends ConsumerWidget {
  const BookmarksPage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final state = ref.watch(bookmarkedAnimeProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Saved list')),
      body: Padding(
        padding: const EdgeInsets.fromLTRB(20, 10, 20, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SectionTitle(
              kicker: 'Bookmarks',
              title: 'Anime yang kamu simpan',
              subtitle: 'Daftar lokal untuk quick access sebelum kita sambungkan ke akun cloud.',
            ),
            const SizedBox(height: 16),
            Expanded(
              child: state.when(
                data: (items) {
                  if (items.isEmpty) {
                    return const EmptyState(
                      title: 'Belum ada bookmark',
                      message: 'Simpan anime favoritmu dari halaman detail agar muncul di sini.',
                      icon: Icons.bookmark_border_rounded,
                    );
                  }

                  return GridView.builder(
                    itemCount: items.length,
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 16,
                      mainAxisSpacing: 16,
                      childAspectRatio: 0.62,
                    ),
                    itemBuilder: (context, index) => AnimeCard(anime: items[index]),
                  );
                },
                loading: () => GridView.builder(
                  itemCount: 4,
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 16,
                    mainAxisSpacing: 16,
                    childAspectRatio: 0.62,
                  ),
                  itemBuilder: (_, __) => const LoadingSkeleton(height: 260, radius: 28),
                ),
                error: (_, __) => AppErrorState(
                  title: 'Bookmark belum bisa dimuat',
                  message: 'Terjadi masalah saat membaca bookmark dari penyimpanan lokal.',
                  onRetry: () => ref.invalidate(bookmarkedAnimeProvider),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
