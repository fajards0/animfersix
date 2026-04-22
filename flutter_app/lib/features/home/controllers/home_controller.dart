import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../shared/models/anime.dart';
import 'anime_repository_provider.dart';

class HomeViewData {
  const HomeViewData({
    required this.featured,
    required this.trending,
    required this.popular,
    required this.latest,
    required this.recommended,
    required this.continueWatching,
    required this.genres,
  });

  final List<Anime> featured;
  final List<Anime> trending;
  final List<Anime> popular;
  final List<Anime> latest;
  final List<Anime> recommended;
  final List<Anime> continueWatching;
  final List<String> genres;
}

final homeControllerProvider = FutureProvider<HomeViewData>((ref) async {
  final repository = ref.watch(animeRepositoryProvider);

  final results = await Future.wait<dynamic>([
    repository.getFeaturedAnime(),
    repository.getTrendingAnime(),
    repository.getPopularAnime(),
    repository.getLatestAnime(),
    repository.getRecommendedAnime(),
    repository.getContinueWatchingAnime(),
    repository.getGenres(),
  ]);

  return HomeViewData(
    featured: results[0] as List<Anime>,
    trending: results[1] as List<Anime>,
    popular: results[2] as List<Anime>,
    latest: results[3] as List<Anime>,
    recommended: results[4] as List<Anime>,
    continueWatching: results[5] as List<Anime>,
    genres: (results[6] as List<String>).take(14).toList(),
  );
});
