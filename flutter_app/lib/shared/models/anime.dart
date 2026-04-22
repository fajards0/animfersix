class Episode {
  const Episode({
    required this.id,
    required this.title,
    required this.number,
    required this.duration,
    required this.label,
    this.isAvailable720p = true,
  });

  final String id;
  final String title;
  final int number;
  final String duration;
  final String label;
  final bool isAvailable720p;
}

class Anime {
  const Anime({
    required this.id,
    required this.title,
    required this.posterUrl,
    required this.bannerUrl,
    required this.score,
    required this.status,
    required this.episodeInfo,
    required this.synopsis,
    required this.genres,
    required this.type,
    required this.year,
    required this.studio,
    required this.featuredTag,
    required this.headline,
    required this.highlight,
    required this.episodes,
    this.isFeatured = false,
    this.watchProgress = 0,
    this.lastWatchedEpisode,
  });

  final String id;
  final String title;
  final String posterUrl;
  final String bannerUrl;
  final double score;
  final String status;
  final String episodeInfo;
  final String synopsis;
  final List<String> genres;
  final String type;
  final int year;
  final String studio;
  final String featuredTag;
  final String headline;
  final String highlight;
  final List<Episode> episodes;
  final bool isFeatured;
  final double watchProgress;
  final int? lastWatchedEpisode;
}
