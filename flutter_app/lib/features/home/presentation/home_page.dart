import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/constants/app_colors.dart';
import '../../../shared/models/anime.dart';
import '../../../shared/widgets/anime_card.dart';
import '../../../shared/widgets/app_error_state.dart';
import '../../../shared/widgets/loading_skeleton.dart';
import '../../../shared/widgets/section_title.dart';
import '../controllers/home_controller.dart';

class HomePage extends ConsumerWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final state = ref.watch(homeControllerProvider);

    return Scaffold(
      body: state.when(
        data: (data) => RefreshIndicator(
          color: AppColors.ember,
          onRefresh: () async => ref.invalidate(homeControllerProvider),
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
            slivers: [
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 14, 20, 0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const _HomeTopBar(),
                      const SizedBox(height: 22),
                      _FeaturedCarousel(items: data.featured),
                      const SizedBox(height: 28),
                      SectionTitle(
                        kicker: 'Mood entry',
                        title: 'Browse by genre',
                        subtitle: 'Masuk dari vibe yang paling kamu cari hari ini.',
                        actionLabel: 'Search',
                        onActionTap: () => context.go('/search'),
                      ),
                      const SizedBox(height: 14),
                      _GenreRail(genres: data.genres),
                      if (data.continueWatching.isNotEmpty) ...[
                        const SizedBox(height: 28),
                        const SectionTitle(
                          kicker: 'Continue watching',
                          title: 'Lanjut dari sesi terakhirmu',
                          subtitle: 'Kembali ke episode yang belum selesai tanpa cari ulang.',
                        ),
                        const SizedBox(height: 16),
                        SizedBox(
                          height: 282,
                          child: ListView.separated(
                            scrollDirection: Axis.horizontal,
                            itemCount: data.continueWatching.length,
                            separatorBuilder: (_, __) => const SizedBox(width: 14),
                            itemBuilder: (context, index) => AnimeCard(
                              anime: data.continueWatching[index],
                              width: 188,
                              showProgress: true,
                            ),
                          ),
                        ),
                      ],
                      const SizedBox(height: 28),
                      SectionTitle(
                        kicker: 'Trending now',
                        title: 'Yang lagi panas sekarang',
                        subtitle: 'Judul dengan engagement tinggi dan score kuat.',
                        actionLabel: 'See all',
                        onActionTap: () => context.go('/search'),
                      ),
                      const SizedBox(height: 16),
                    ],
                  ),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                sliver: SliverGrid(
                  delegate: SliverChildBuilderDelegate(
                    (context, index) => AnimeCard(anime: data.trending[index]),
                    childCount: data.trending.take(6).length,
                  ),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    mainAxisSpacing: 16,
                    crossAxisSpacing: 16,
                    childAspectRatio: 0.62,
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 8),
                      const SectionTitle(
                        kicker: 'Latest drop',
                        title: 'Episode dan update terbaru',
                        subtitle: 'Rilis baru yang cocok untuk dibuka cepat.',
                      ),
                      const SizedBox(height: 16),
                      ...data.latest.take(4).map((anime) => Padding(
                            padding: const EdgeInsets.only(bottom: 12),
                            child: _ListTileCard(anime: anime),
                          )),
                      const SizedBox(height: 26),
                      const SectionTitle(
                        kicker: 'Popular picks',
                        title: 'Serial ongoing yang paling sering dipilih',
                        subtitle: 'Kumpulan judul yang terasa stabil untuk jadi tontonan rutin.',
                      ),
                      const SizedBox(height: 16),
                      SizedBox(
                        height: 282,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          itemBuilder: (context, index) => AnimeCard(
                            anime: data.popular[index],
                            width: 188,
                          ),
                          separatorBuilder: (_, __) => const SizedBox(width: 14),
                          itemCount: data.popular.length,
                        ),
                      ),
                      const SizedBox(height: 26),
                      const SectionTitle(
                        kicker: 'Recommended for you',
                        title: 'Judul yang enak buat sesi berikutnya',
                        subtitle: 'Kurasi ringan dengan genre campuran dan visual premium.',
                      ),
                      const SizedBox(height: 16),
                      SizedBox(
                        height: 282,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          itemBuilder: (context, index) => AnimeCard(
                            anime: data.recommended[index],
                            width: 188,
                          ),
                          separatorBuilder: (_, __) => const SizedBox(width: 14),
                          itemCount: data.recommended.length,
                        ),
                      ),
                      const SizedBox(height: 12),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
        loading: () => const _HomeLoading(),
        error: (_, __) => AppErrorState(
          title: 'Home belum bisa dimuat',
          message: 'Terjadi masalah saat menyiapkan katalog anime. Coba refresh lagi.',
          onRetry: () => ref.invalidate(homeControllerProvider),
        ),
      ),
    );
  }
}

class _HomeTopBar extends StatelessWidget {
  const _HomeTopBar();

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'AnimeStream',
              style: Theme.of(context).textTheme.headlineMedium,
            ),
            const SizedBox(height: 4),
            Text(
              'Premium anime lounge for your next binge.',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ],
        ),
        const Spacer(),
        Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            color: const Color(0x12FFFFFF),
            border: Border.all(color: AppColors.stroke),
          ),
          child: const Icon(Icons.notifications_none_rounded),
        ),
      ],
    );
  }
}

class _FeaturedCarousel extends StatelessWidget {
  const _FeaturedCarousel({required this.items});

  final List<Anime> items;

  @override
  Widget build(BuildContext context) {
    final featuredItems = items.take(4).toList();

    return SizedBox(
      height: 456,
      child: PageView.builder(
        controller: PageController(viewportFraction: 0.92),
        itemCount: featuredItems.length,
        itemBuilder: (context, index) {
          final anime = featuredItems[index];
          return Padding(
            padding: EdgeInsets.only(right: index == featuredItems.length - 1 ? 0 : 14),
            child: InkWell(
              borderRadius: BorderRadius.circular(36),
              onTap: () => context.push('/anime/${anime.id}'),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(36),
                  border: Border.all(color: AppColors.stroke),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x66000000),
                      blurRadius: 24,
                      offset: Offset(0, 12),
                    ),
                  ],
                ),
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(36),
                      child: CachedNetworkImage(
                        imageUrl: anime.bannerUrl,
                        fit: BoxFit.cover,
                        errorWidget: (_, __, ___) => Container(color: AppColors.slate),
                      ),
                    ),
                    DecoratedBox(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(36),
                        gradient: const LinearGradient(
                          begin: Alignment.bottomCenter,
                          end: Alignment.topCenter,
                          colors: [Color(0xF307080C), Color(0x7007080C), Colors.transparent],
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              _GlassPill(label: anime.featuredTag),
                              const Spacer(),
                              _GlassPill(label: '${anime.score.toStringAsFixed(1)} rating'),
                            ],
                          ),
                          const Spacer(),
                          Text(
                            anime.title,
                            style: Theme.of(context).textTheme.displaySmall,
                          ),
                          const SizedBox(height: 10),
                          Text(
                            anime.headline,
                            style: Theme.of(context).textTheme.titleLarge?.copyWith(color: Colors.white70),
                          ),
                          const SizedBox(height: 12),
                          Text(
                            anime.synopsis,
                            maxLines: 3,
                            overflow: TextOverflow.ellipsis,
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.white70),
                          ),
                          const SizedBox(height: 18),
                          Row(
                            children: [
                              _MetricChip(label: anime.episodeInfo),
                              const SizedBox(width: 10),
                              _MetricChip(label: anime.status),
                              const SizedBox(width: 10),
                              _MetricChip(label: anime.genres.first),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class _GenreRail extends StatelessWidget {
  const _GenreRail({required this.genres});

  final List<String> genres;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 42,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: genres.length,
        separatorBuilder: (_, __) => const SizedBox(width: 10),
        itemBuilder: (context, index) {
          final genre = genres[index];
          return Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              color: const Color(0x12FFFFFF),
              borderRadius: BorderRadius.circular(999),
              border: Border.all(color: AppColors.stroke),
            ),
            child: Center(
              child: Text(
                genre,
                style: Theme.of(context).textTheme.labelLarge,
              ),
            ),
          );
        },
      ),
    );
  }
}

class _ListTileCard extends StatelessWidget {
  const _ListTileCard({required this.anime});

  final Anime anime;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(28),
      onTap: () => context.push('/anime/${anime.id}'),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(22),
                child: CachedNetworkImage(
                  imageUrl: anime.posterUrl,
                  width: 82,
                  height: 106,
                  fit: BoxFit.cover,
                  errorWidget: (_, __, ___) => Container(
                    width: 82,
                    height: 106,
                    color: AppColors.slate,
                  ),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(anime.title, style: Theme.of(context).textTheme.titleLarge),
                    const SizedBox(height: 6),
                    Text(anime.highlight, style: Theme.of(context).textTheme.bodyMedium),
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        _MetricChip(label: anime.episodeInfo),
                        _MetricChip(label: anime.status),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MetricChip extends StatelessWidget {
  const _MetricChip({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: const Color(0x14FFFFFF),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(label, style: const TextStyle(fontWeight: FontWeight.w700)),
    );
  }
}

class _GlassPill extends StatelessWidget {
  const _GlassPill({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        color: const Color(0x24000000),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: AppColors.stroke),
      ),
      child: Text(
        label,
        style: const TextStyle(fontWeight: FontWeight.w800, letterSpacing: 0.3),
      ),
    );
  }
}

class _HomeLoading extends StatelessWidget {
  const _HomeLoading();

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(20, 18, 20, 24),
      children: [
        const LoadingSkeleton(height: 28, width: 180),
        const SizedBox(height: 10),
        const LoadingSkeleton(height: 18, width: 220),
        const SizedBox(height: 20),
        const LoadingSkeleton(height: 456, radius: 36),
        const SizedBox(height: 24),
        const LoadingSkeleton(height: 24, width: 170),
        const SizedBox(height: 14),
        const LoadingSkeleton(height: 42, radius: 999),
        const SizedBox(height: 24),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 4,
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 16,
            crossAxisSpacing: 16,
            childAspectRatio: 0.62,
          ),
          itemBuilder: (_, __) => const LoadingSkeleton(height: 260, radius: 28),
        ),
      ],
    );
  }
}
