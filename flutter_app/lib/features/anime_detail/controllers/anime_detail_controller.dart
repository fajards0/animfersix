import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../shared/models/anime.dart';
import '../../home/controllers/anime_repository_provider.dart';

final animeDetailProvider = FutureProvider.family<Anime?, String>((ref, id) async {
  final repository = ref.watch(animeRepositoryProvider);
  return repository.getAnimeById(id);
});
