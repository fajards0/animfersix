import '../shared/models/anime.dart';
import 'anime_repository.dart';

class MockAnimeRepository implements AnimeRepository {
  MockAnimeRepository() : _items = _buildMockAnime();

  final List<Anime> _items;

  @override
  Future<List<Anime>> getFeaturedAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 380));
    return _items.where((item) => item.isFeatured).take(5).toList();
  }

  @override
  Future<List<Anime>> getTrendingAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 420));
    return [..._items]..sort((a, b) => b.score.compareTo(a.score));
  }

  @override
  Future<List<Anime>> getPopularAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 420));
    return _items.where((item) => item.status == 'Ongoing').take(8).toList();
  }

  @override
  Future<List<Anime>> getLatestAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 420));
    return _items.reversed.take(10).toList();
  }

  @override
  Future<List<Anime>> getRecommendedAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 420));
    return _items.where((item) => item.type == 'TV').skip(2).take(8).toList();
  }

  @override
  Future<List<Anime>> getContinueWatchingAnime() async {
    await Future<void>.delayed(const Duration(milliseconds: 320));
    return _items.where((item) => item.watchProgress > 0).take(5).toList();
  }

  @override
  Future<List<String>> getGenres() async {
    await Future<void>.delayed(const Duration(milliseconds: 220));
    final genres = _items.expand((item) => item.genres).toSet().toList()..sort();
    return genres;
  }

  @override
  Future<List<Anime>> searchAnime(String query) async {
    await Future<void>.delayed(const Duration(milliseconds: 320));
    final normalized = query.trim().toLowerCase();
    if (normalized.isEmpty) {
      return [];
    }

    final results = _items.where((item) {
      return item.title.toLowerCase().contains(normalized) ||
          item.genres.any((genre) => genre.toLowerCase().contains(normalized)) ||
          item.studio.toLowerCase().contains(normalized) ||
          item.headline.toLowerCase().contains(normalized);
    }).toList();

    results.sort((a, b) => b.score.compareTo(a.score));
    return results;
  }

  @override
  Future<Anime?> getAnimeById(String id) async {
    await Future<void>.delayed(const Duration(milliseconds: 320));
    for (final item in _items) {
      if (item.id == id) {
        return item;
      }
    }
    return null;
  }
}

List<Anime> _buildMockAnime() {
  const entries = [
    (
      id: 'celestial-breaker',
      title: 'Celestial Breaker',
      score: 9.3,
      status: 'Ongoing',
      episodeInfo: 'Episode 11',
      type: 'TV',
      year: 2026,
      studio: 'Nova Frame',
      featuredTag: 'Tonight Spotlight',
      headline: 'Space opera dengan tensi perang yang mewah',
      highlight: 'Visual orbital battle dan chemistry squad yang bikin binge.',
      genres: ['Action', 'Sci-Fi', 'Adventure'],
      synopsis:
          'Di ujung galaksi yang hampir pecah, seorang pilot buangan memimpin tim kecil untuk merebut kembali koridor bintang yang dikuasai rezim militer.',
      watchProgress: 0.68,
      lastEpisode: 9,
      isFeatured: true,
      imageSeed: 'photo-1541560052-77ec1bbc09f7',
    ),
    (
      id: 'velvet-eclipse',
      title: 'Velvet Eclipse',
      score: 8.9,
      status: 'Completed',
      episodeInfo: '24 Episodes',
      type: 'TV',
      year: 2025,
      studio: 'Clover Motion',
      featuredTag: 'Editor Choice',
      headline: 'Dark fantasy stylish dengan atmosfer premium',
      highlight: 'Cast kuat, soundtrack dingin, dan pacing dewasa.',
      genres: ['Fantasy', 'Drama', 'Mystery'],
      synopsis:
          'Ketika matahari buatan kota runtuh, seorang pewaris keluarga pengusir roh harus menavigasi politik gelap dan kutukan yang menelan memorinya.',
      watchProgress: 0.24,
      lastEpisode: 4,
      isFeatured: true,
      imageSeed: 'photo-1515879218367-8466d910aaa4',
    ),
    (
      id: 'neon-shogun',
      title: 'Neon Shogun',
      score: 8.7,
      status: 'Ongoing',
      episodeInfo: 'Episode 8',
      type: 'TV',
      year: 2026,
      studio: 'Blue Ember',
      featuredTag: 'New Drop',
      headline: 'Cyber samurai dengan duel neon futuristik',
      highlight: 'Aksi cepat, armor tajam, dan direction yang modern.',
      genres: ['Action', 'Cyberpunk', 'Thriller'],
      synopsis:
          'Samurai tanpa klan memburu AI kuno yang bisa menulis ulang loyalitas seluruh distrik bawah tanah Neo-Edo.',
      watchProgress: 0.52,
      lastEpisode: 5,
      isFeatured: true,
      imageSeed: 'photo-1498050108023-c5249f4df085',
    ),
    (
      id: 'aether-garden',
      title: 'Aether Garden',
      score: 8.5,
      status: 'Completed',
      episodeInfo: '12 Episodes',
      type: 'TV',
      year: 2024,
      studio: 'Aether Works',
      featuredTag: 'Comfort Pick',
      headline: 'Fantasy cozy yang elegan dan menenangkan',
      highlight: 'Palet warna hangat dan worldbuilding yang lembut.',
      genres: ['Fantasy', 'Slice of Life', 'Romance'],
      synopsis:
          'Seorang peracik benih sihir dan penjaga kebun tua menumbuhkan tanaman yang bisa menyembuhkan kota terapung yang retak dari dalam.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: true,
      imageSeed: 'photo-1493246507139-91e8fad9978e',
    ),
    (
      id: 'last-anthem',
      title: 'Last Anthem',
      score: 8.4,
      status: 'Ongoing',
      episodeInfo: 'Episode 6',
      type: 'TV',
      year: 2026,
      studio: 'Nova Frame',
      featuredTag: 'Hot Episode',
      headline: 'Idol warfare dengan panggung megah',
      highlight: 'Pertarungan musik dan emosi tim yang intens.',
      genres: ['Music', 'Action', 'Drama'],
      synopsis:
          'Lima performer elite bertarung melalui panggung tempur holografik untuk mempertahankan kota yang hidup dari resonansi lagu.',
      watchProgress: 0.37,
      lastEpisode: 3,
      isFeatured: true,
      imageSeed: 'photo-1500530855697-b586d89ba3ee',
    ),
    (
      id: 'project-oni',
      title: 'Project ONI',
      score: 8.2,
      status: 'Completed',
      episodeInfo: '13 Episodes',
      type: 'TV',
      year: 2023,
      studio: 'Blue Ember',
      featuredTag: 'Binge Tonight',
      headline: 'Mystery school yang rapi dan bikin penasaran',
      highlight: 'Twist padat dan visual malam yang tajam.',
      genres: ['Mystery', 'School', 'Supernatural'],
      synopsis:
          'Sebuah klub rahasia di akademi elit menyelidiki eksperimen iblis sintetis yang diam-diam meniru kehidupan para murid.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: false,
      imageSeed: 'photo-1516321318423-f06f85e504b3',
    ),
    (
      id: 'mirage-rider',
      title: 'Mirage Rider',
      score: 8.8,
      status: 'Ongoing',
      episodeInfo: 'Episode 14',
      type: 'TV',
      year: 2026,
      studio: 'Aether Works',
      featuredTag: 'Fast Lane',
      headline: 'Road anime futuristik dengan aura outlaw',
      highlight: 'Balapan gurun dan partner dynamic yang kuat.',
      genres: ['Adventure', 'Sci-Fi', 'Drama'],
      synopsis:
          'Kurir lintas gurun memburu perangkat cuaca purba sembari menghindari sindikat yang ingin mengubah seluruh jalur migrasi manusia.',
      watchProgress: 0.81,
      lastEpisode: 12,
      isFeatured: false,
      imageSeed: 'photo-1500534314209-a25ddb2bd429',
    ),
    (
      id: 'moonlit-archive',
      title: 'Moonlit Archive',
      score: 8.6,
      status: 'Completed',
      episodeInfo: '10 Episodes',
      type: 'Movie',
      year: 2025,
      studio: 'Clover Motion',
      featuredTag: 'Movie Night',
      headline: 'Detective occult dengan framing sinematik',
      highlight: 'Riddle kuat dan sinematografi yang eksklusif.',
      genres: ['Mystery', 'Fantasy', 'Thriller'],
      synopsis:
          'Arsiparis malam menelusuri perpustakaan bawah bulan yang menyimpan naskah hidup dan catatan kematian yang belum terjadi.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: false,
      imageSeed: 'photo-1516383740770-fbcc5ccbece0',
    ),
    (
      id: 'sora-vanguard',
      title: 'Sora Vanguard',
      score: 9.0,
      status: 'Ongoing',
      episodeInfo: 'Episode 19',
      type: 'TV',
      year: 2026,
      studio: 'Nova Frame',
      featuredTag: 'Top Rated',
      headline: 'War epic airborne dengan squad elite',
      highlight: 'Dogfight halus dan momentum tiap episode kuat.',
      genres: ['Action', 'Military', 'Adventure'],
      synopsis:
          'Pilot muda dengan bakat sinkronisasi angin dipaksa memimpin armada kecil demi menggagalkan invasi di lapisan langit paling bawah.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: false,
      imageSeed: 'photo-1474302770737-173ee21bab63',
    ),
    (
      id: 'hikari-notes',
      title: 'Hikari Notes',
      score: 8.1,
      status: 'Completed',
      episodeInfo: '11 Episodes',
      type: 'TV',
      year: 2024,
      studio: 'Blue Ember',
      featuredTag: 'Soft Drama',
      headline: 'Coming-of-age musikal yang clean',
      highlight: 'Karakter hangat dan dramanya ringan tapi ngena.',
      genres: ['Drama', 'Music', 'School'],
      synopsis:
          'Seorang pianis yang kehilangan kepercayaan diri mulai menulis lagu anonim yang perlahan menyatukan teman-teman sekelasnya.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: false,
      imageSeed: 'photo-1493225457124-a3eb161ffa5f',
    ),
    (
      id: 'black-comet',
      title: 'Black Comet',
      score: 8.75,
      status: 'Ongoing',
      episodeInfo: 'Episode 7',
      type: 'TV',
      year: 2026,
      studio: 'Clover Motion',
      featuredTag: 'High Velocity',
      headline: 'Thriller luar angkasa dengan tempo rapat',
      highlight: 'Rasa bahaya konstan dan sound design agresif.',
      genres: ['Sci-Fi', 'Thriller', 'Action'],
      synopsis:
          'Tim salvage memburu komet hitam yang membawa sinyal kematian dan mengundang pemburu hadiah dari seluruh koloni.',
      watchProgress: 0.15,
      lastEpisode: 2,
      isFeatured: false,
      imageSeed: 'photo-1462331940025-496dfbfc7564',
    ),
    (
      id: 'garden-of-kitsune',
      title: 'Garden of Kitsune',
      score: 8.3,
      status: 'Completed',
      episodeInfo: '1 Movie',
      type: 'Movie',
      year: 2025,
      studio: 'Aether Works',
      featuredTag: 'Weekend Watch',
      headline: 'Folklore Jepang modern yang mewah',
      highlight: 'Mood lembut dan visual alam malam yang kaya.',
      genres: ['Fantasy', 'Romance', 'Drama'],
      synopsis:
          'Mahasiswi arsitektur bertemu penjaga kuil rubah yang memintanya memilih antara masa depan modern dan desa yang nyaris hilang dari peta.',
      watchProgress: 0.0,
      lastEpisode: null,
      isFeatured: false,
      imageSeed: 'photo-1506744038136-46273834b3fb',
    ),
  ];

  return entries.map((entry) {
    final episodes = List.generate(
      entry.type == 'Movie' ? 1 : 12,
      (index) => Episode(
        id: '${entry.id}-episode-${index + 1}',
        title: entry.type == 'Movie' ? '${entry.title} Full Movie' : 'Episode ${index + 1} - ${entry.title}',
        number: index + 1,
        duration: entry.type == 'Movie' ? '1h 46m' : '${22 + (index % 3)} min',
        label: entry.type == 'Movie' ? 'Movie' : 'Episode ${index + 1}',
        isAvailable720p: index != 10,
      ),
    );

    return Anime(
      id: entry.id,
      title: entry.title,
      posterUrl: 'https://images.unsplash.com/${entry.imageSeed}?auto=format&fit=crop&w=900&q=80',
      bannerUrl: 'https://images.unsplash.com/${entry.imageSeed}?auto=format&fit=crop&w=1600&q=80',
      score: entry.score,
      status: entry.status,
      episodeInfo: entry.episodeInfo,
      synopsis: entry.synopsis,
      genres: entry.genres,
      type: entry.type,
      year: entry.year,
      studio: entry.studio,
      featuredTag: entry.featuredTag,
      headline: entry.headline,
      highlight: entry.highlight,
      episodes: episodes,
      isFeatured: entry.isFeatured,
      watchProgress: entry.watchProgress,
      lastWatchedEpisode: entry.lastEpisode,
    );
  }).toList();
}
