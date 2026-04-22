import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class PreferencesService {
  static const _onboardingKey = 'did_finish_onboarding';
  static const _bookmarksKey = 'anime_bookmarks';
  static const _darkModeKey = 'dark_mode_enabled';
  static const _profileNameKey = 'profile_name';

  Future<SharedPreferences> get _prefs => SharedPreferences.getInstance();

  Future<bool> getDidFinishOnboarding() async {
    final prefs = await _prefs;
    return prefs.getBool(_onboardingKey) ?? false;
  }

  Future<void> setDidFinishOnboarding(bool value) async {
    final prefs = await _prefs;
    await prefs.setBool(_onboardingKey, value);
  }

  Future<Set<String>> getBookmarks() async {
    final prefs = await _prefs;
    final raw = prefs.getString(_bookmarksKey);
    if (raw == null || raw.isEmpty) return {};
    return Set<String>.from((jsonDecode(raw) as List).map((item) => item.toString()));
  }

  Future<void> setBookmarks(Set<String> ids) async {
    final prefs = await _prefs;
    await prefs.setString(_bookmarksKey, jsonEncode(ids.toList()));
  }

  Future<ThemeMode> getThemeMode() async {
    final prefs = await _prefs;
    final isDark = prefs.getBool(_darkModeKey) ?? true;
    return isDark ? ThemeMode.dark : ThemeMode.light;
  }

  Future<void> setThemeMode(ThemeMode mode) async {
    final prefs = await _prefs;
    await prefs.setBool(_darkModeKey, mode != ThemeMode.light);
  }

  Future<String> getProfileName() async {
    final prefs = await _prefs;
    return prefs.getString(_profileNameKey) ?? 'Anime Explorer';
  }

  Future<void> setProfileName(String name) async {
    final prefs = await _prefs;
    await prefs.setString(_profileNameKey, name);
  }
}
