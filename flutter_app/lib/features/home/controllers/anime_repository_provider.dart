import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../services/anime_repository.dart';
import '../../../services/mock_anime_repository.dart';

final animeRepositoryProvider = Provider<AnimeRepository>((ref) {
  return MockAnimeRepository();
});
