import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/utils/debouncer.dart';
import '../../../shared/widgets/anime_card.dart';
import '../../../shared/widgets/app_error_state.dart';
import '../../../shared/widgets/empty_state.dart';
import '../../../shared/widgets/loading_skeleton.dart';
import '../../../shared/widgets/search_field.dart';
import '../../../shared/widgets/section_title.dart';
import '../controllers/search_controller.dart';

class SearchPage extends ConsumerStatefulWidget {
  const SearchPage({super.key});

  @override
  ConsumerState<SearchPage> createState() => _SearchPageState();
}

class _SearchPageState extends ConsumerState<SearchPage> {
  late final TextEditingController _controller;
  late final Debouncer _debouncer;

  @override
  void initState() {
    super.initState();
    _controller = TextEditingController();
    _debouncer = Debouncer(delay: const Duration(milliseconds: 360));
  }

  @override
  void dispose() {
    _controller.dispose();
    _debouncer.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final results = ref.watch(searchResultsProvider);
    final query = ref.watch(searchQueryProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Search anime')),
      body: Padding(
        padding: const EdgeInsets.fromLTRB(20, 10, 20, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SectionTitle(
              kicker: 'Search',
              title: 'Temukan judul favoritmu',
              subtitle: 'Realtime search dengan struktur yang siap dihubungkan ke API.',
            ),
            const SizedBox(height: 16),
            SearchField(
              controller: _controller,
              onChanged: (value) {
                setState(() {});
                _debouncer(() {
                  ref.read(searchQueryProvider.notifier).state = value;
                });
              },
            ),
            const SizedBox(height: 20),
            if (query.trim().isNotEmpty)
              Padding(
                padding: const EdgeInsets.only(bottom: 14),
                child: Text(
                  'Results for "$query"',
                  style: Theme.of(context).textTheme.titleMedium,
                ),
              ),
            Expanded(
              child: query.trim().isEmpty
                  ? const EmptyState(
                      title: 'Mulai pencarian',
                      message: 'Masukkan judul, genre, atau studio untuk menemukan anime.',
                      icon: Icons.search_rounded,
                    )
                  : results.when(
                      data: (items) {
                        if (items.isEmpty) {
                          return const EmptyState(
                            title: 'Tidak ada hasil',
                            message: 'Coba keyword lain atau gunakan pencarian yang lebih singkat.',
                            icon: Icons.travel_explore_rounded,
                          );
                        }

                        return GridView.builder(
                          itemCount: items.length,
                          physics: const BouncingScrollPhysics(),
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
                        title: 'Pencarian gagal',
                        message: 'Ada masalah saat memuat hasil pencarian.',
                        onRetry: () => ref.invalidate(searchResultsProvider),
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
