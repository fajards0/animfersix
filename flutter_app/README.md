# AnimeStream Mobile

Flutter rebuild untuk AnimeStream dengan fokus pada UI premium, struktur scalable, dan kesiapan untuk tahap publish.

## Struktur

```text
lib/
  app/
  core/
  features/
  services/
  shared/
```

## Paket utama

- `flutter_riverpod`: state management
- `go_router`: routing dan shell navigation
- `shared_preferences`: simpan onboarding, bookmark, dan preferensi
- `cached_network_image`: caching image network
- `flutter_animate`: splash dan micro-animation
- `dio`: siap untuk integrasi API production

## Screen yang sudah ada

- Splash cinematic
- Onboarding
- Home premium feed
- Anime detail
- Search realtime
- Bookmark
- Profile / settings

## Menjalankan project

```bash
flutter pub get
flutter analyze
flutter run
```

## Catatan integrasi API

Saat ini app memakai `MockAnimeRepository` agar UI dan flow mobile bisa langsung dijalankan. Struktur repository sudah disiapkan supaya nanti mudah diganti ke API Laravel/mobile endpoint.
