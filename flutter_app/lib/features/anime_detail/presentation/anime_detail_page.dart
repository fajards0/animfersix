import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/constants/app_colors.dart';
import '../../../shared/models/anime.dart';
import '../../../shared/widgets/app_error_state.dart';
import '../../../shared/widgets/custom_button.dart';
import '../../../shared/widgets/empty_state.dart';
import '../../../shared/widgets/loading_skeleton.dart';
import '../../../shared/widgets/section_title.dart';
import '../../bookmarks/controllers/bookmarks_controller.dart';
import '../controllers/anime_detail_controller.dart';

class AnimeDetailPage extends ConsumerWidget {
  const AnimeDetailPage({
    super.key,
    required this.animeId,
  });

  final String animeId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(animeDetailProvider(animeId));
    final bookmarks = ref.watch(bookmarksControllerProvider);

    return Scaffold(
      body: detail.when(
        data: (anime) {
          if (anime == null) {
            return const EmptyState(
              title: 'Anime tidak ditemukan',
              message: 'Detail anime belum tersedia untuk item ini.',
            );
          }

          final isBookmarked = bookmarks.contains(anime.id);

          return CustomScrollView(
            slivers: [
              SliverAppBar(
                pinned: true,
                expandedHeight: 360,
                actions: [
                  IconButton(
                    onPressed: () => ref.read(bookmarksControllerProvider.notifier).toggle(anime.id),
                    icon: Icon(isBookmarked ? Icons.bookmark_rounded : Icons.bookmark_outline_rounded),
                  ),
                ],
                flexibleSpace: FlexibleSpaceBar(
                  background: Stack(
                    fit: StackFit.expand,
                    children: [
                      CachedNetworkImage(
                        imageUrl: anime.bannerUrl,
                        fit: BoxFit.cover,
                        errorWidget: (_, __, ___) => Container(color: AppColors.slate),
                      ),
                      Container(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.bottomCenter,
                            end: Alignment.topCenter,
                            colors: [Color(0xF307080C), Color(0x6607080C), Colors.transparent],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(anime.title, style: Theme.of(context).textTheme.displaySmall),
                      const SizedBox(height: 10),
                      Text(
                        anime.headline,
                        style: Theme.of(context).textTheme.bodyLarge?.copyWith(color: Colors.white70),
                      ),
                      const SizedBox(height: 16),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: [
                          _InfoChip(label: '${anime.score.toStringAsFixed(1)} rating'),
                          _InfoChip(label: anime.status),
                          _InfoChip(label: anime.episodeInfo),
                          _InfoChip(label: '${anime.type} - ${anime.year}'),
                          _InfoChip(label: anime.studio),
                        ],
                      ),
                      const SizedBox(height: 22),
                      Row(
                        children: [
                          Expanded(
                            child: CustomButton.primary(
                              label: anime.watchProgress > 0 ? 'Lanjut nonton' : 'Watch now',
                              onPressed: () {},
                              icon: Icons.play_circle_fill_rounded,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: CustomButton.secondary(
                              label: isBookmarked ? 'Saved' : 'Bookmark',
                              onPressed: () => ref.read(bookmarksControllerProvider.notifier).toggle(anime.id),
                              icon: isBookmarked ? Icons.bookmark_rounded : Icons.bookmark_add_outlined,
                            ),
                          ),
                        ],
                      ),
                      if (anime.watchProgress > 0) ...[
                        const SizedBox(height: 18),
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: const Color(0x12FFFFFF),
                            borderRadius: BorderRadius.circular(24),
                            border: Border.all(color: AppColors.stroke),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Progress menonton', style: Theme.of(context).textTheme.titleMedium),
                              const SizedBox(height: 10),
                              ClipRRect(
                                borderRadius: BorderRadius.circular(999),
                                child: LinearProgressIndicator(
                                  value: anime.watchProgress,
                                  minHeight: 8,
                                  backgroundColor: const Color(0x18FFFFFF),
                                  valueColor: const AlwaysStoppedAnimation<Color>(AppColors.ember),
                                ),
                              ),
                              const SizedBox(height: 10),
                              Text(
                                'Terakhir di episode ${anime.lastWatchedEpisode ?? 1}',
                                style: Theme.of(context).textTheme.bodyMedium,
                              ),
                            ],
                          ),
                        ),
                      ],
                      const SizedBox(height: 28),
                      const SectionTitle(
                        kicker: 'Synopsis',
                        title: 'Cerita singkat',
                        subtitle: 'Ringkasan yang mudah dibaca sebelum mulai nonton.',
                      ),
                      const SizedBox(height: 14),
                      Text(anime.synopsis, style: Theme.of(context).textTheme.bodyLarge),
                      const SizedBox(height: 26),
                      const SectionTitle(
                        kicker: 'Genres',
                        title: 'Mood dan kategori',
                      ),
                      const SizedBox(height: 14),
                      Wrap(
                        spacing: 10,
                        runSpacing: 10,
                        children: anime.genres.map((genre) => Chip(label: Text(genre))).toList(),
                      ),
                      const SizedBox(height: 28),
                      const SectionTitle(
                        kicker: 'Episodes',
                        title: 'Daftar episode',
                        subtitle: 'Siap untuk dihubungkan ke API playback dan mirror stream.',
                      ),
                      const SizedBox(height: 14),
                      ...anime.episodes.map((episode) => _EpisodeTile(episode: episode)),
                    ],
                  ),
                ),
              ),
            ],
          );
        },
        loading: () => const _DetailLoading(),
        error: (_, __) => AppErrorState(
          title: 'Detail belum tersedia',
          message: 'Terjadi masalah saat memuat detail anime.',
          onRetry: () => ref.invalidate(animeDetailProvider(animeId)),
        ),
      ),
    );
  }
}

class _InfoChip extends StatelessWidget {
  const _InfoChip({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
      decoration: BoxDecoration(
        color: const Color(0x12FFFFFF),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: AppColors.stroke),
      ),
      child: Text(label, style: const TextStyle(fontWeight: FontWeight.w700)),
    );
  }
}

class _EpisodeTile extends StatelessWidget {
  const _EpisodeTile({required this.episode});

  final Episode episode;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Card(
        child: ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 8),
          leading: Container(
            width: 46,
            height: 46,
            decoration: BoxDecoration(
              color: const Color(0x12FFFFFF),
              borderRadius: BorderRadius.circular(16),
            ),
            alignment: Alignment.center,
            child: Text(
              episode.number.toString(),
              style: const TextStyle(fontWeight: FontWeight.w800),
            ),
          ),
          title: Text(episode.title),
          subtitle: Text(
            '${episode.duration} - ${episode.isAvailable720p ? '720p ready' : '480p only'}',
          ),
          trailing: const Icon(Icons.play_circle_outline_rounded),
        ),
      ),
    );
  }
}

class _DetailLoading extends StatelessWidget {
  const _DetailLoading();

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
      children: const [
        LoadingSkeleton(height: 340, radius: 34),
        SizedBox(height: 22),
        LoadingSkeleton(height: 32, width: 240),
        SizedBox(height: 12),
        LoadingSkeleton(height: 18, width: 210),
        SizedBox(height: 18),
        LoadingSkeleton(height: 56, radius: 20),
      ],
    );
  }
}
