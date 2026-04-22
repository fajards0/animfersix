import '../shared/models/anime.dart';

abstract class AnimeRepository {
  Future<List<Anime>> getFeaturedAnime();
  Future<List<Anime>> getTrendingAnime();
  Future<List<Anime>> getPopularAnime();
  Future<List<Anime>> getLatestAnime();
  Future<List<Anime>> getRecommendedAnime();
  Future<List<Anime>> getContinueWatchingAnime();
  Future<List<String>> getGenres();
  Future<List<Anime>> searchAnime(String query);
  Future<Anime?> getAnimeById(String id);
}
