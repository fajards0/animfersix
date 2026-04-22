import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/storage/preferences_service.dart';

final preferencesServiceProvider = Provider((ref) => PreferencesService());

class SettingsState {
  const SettingsState({
    this.themeMode = ThemeMode.dark,
    this.didFinishOnboarding = false,
    this.profileName = 'Anime Explorer',
  });

  final ThemeMode themeMode;
  final bool didFinishOnboarding;
  final String profileName;

  SettingsState copyWith({
    ThemeMode? themeMode,
    bool? didFinishOnboarding,
    String? profileName,
  }) {
    return SettingsState(
      themeMode: themeMode ?? this.themeMode,
      didFinishOnboarding: didFinishOnboarding ?? this.didFinishOnboarding,
      profileName: profileName ?? this.profileName,
    );
  }
}

class SettingsController extends StateNotifier<SettingsState> {
  SettingsController(this._prefs) : super(const SettingsState()) {
    load();
  }

  final PreferencesService _prefs;

  Future<void> load() async {
    final themeMode = await _prefs.getThemeMode();
    final didFinishOnboarding = await _prefs.getDidFinishOnboarding();
    final profileName = await _prefs.getProfileName();

    state = state.copyWith(
      themeMode: themeMode,
      didFinishOnboarding: didFinishOnboarding,
      profileName: profileName,
    );
  }

  Future<void> completeOnboarding() async {
    await _prefs.setDidFinishOnboarding(true);
    state = state.copyWith(didFinishOnboarding: true);
  }

  Future<void> toggleTheme(bool isDark) async {
    final mode = isDark ? ThemeMode.dark : ThemeMode.light;
    await _prefs.setThemeMode(mode);
    state = state.copyWith(themeMode: mode);
  }

  Future<void> updateProfileName(String value) async {
    await _prefs.setProfileName(value);
    state = state.copyWith(profileName: value);
  }
}

final settingsControllerProvider = StateNotifierProvider<SettingsController, SettingsState>((ref) {
  return SettingsController(ref.watch(preferencesServiceProvider));
});
