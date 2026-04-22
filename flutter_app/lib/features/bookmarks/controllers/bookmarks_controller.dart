import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/storage/preferences_service.dart';
import '../../../shared/models/anime.dart';
import '../../settings/controllers/settings_controller.dart';
import '../../home/controllers/anime_repository_provider.dart';

class BookmarksController extends StateNotifier<Set<String>> {
  BookmarksController(this._prefs) : super({}) {
    load();
  }

  final PreferencesService _prefs;

  Future<void> load() async {
    state = await _prefs.getBookmarks();
  }

  Future<void> toggle(String animeId) async {
    final next = {...state};
    if (next.contains(animeId)) {
      next.remove(animeId);
    } else {
      next.add(animeId);
    }
    state = next;
    await _prefs.setBookmarks(next);
  }
}

final bookmarksControllerProvider = StateNotifierProvider<BookmarksController, Set<String>>((ref) {
  return BookmarksController(ref.watch(preferencesServiceProvider));
});

final bookmarkedAnimeProvider = FutureProvider<List<Anime>>((ref) async {
  final ids = ref.watch(bookmarksControllerProvider);
  final repository = ref.watch(animeRepositoryProvider);
  final items = await repository.getPopularAnime();
  final trending = await repository.getTrendingAnime();
  final combined = [...items, ...trending];
  final seen = <String>{};
  final unique = combined.where((item) => seen.add(item.id)).toList();
  return unique.where((anime) => ids.contains(anime.id)).toList();
});
