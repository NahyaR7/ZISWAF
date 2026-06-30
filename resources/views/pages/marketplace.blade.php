@extends('layouts.app')

@section('title', 'Marketplace Donasi - ZISWAF BMT')
@section('page-title', 'Marketplace Donasi')
@section('page-subtitle', 'Salurkan donasi Anda ke program terpercaya')

@section('content')
<div style="padding: 32px 36px;">
    <div class="hero-banner mb-24">
      <h2>🕌 Marketplace Donasi ZISWAF</h2>
      <p>Salurkan zakat, infaq, sedekah, dan wakaf Anda ke program-program terpercaya dari lembaga mitra resmi BMT Pondok Hijau</p>
    </div>
    
    <div class="filter-bar">
      <button class="filter-btn active" onclick="filterProgram('semua',this)">🌟 Semua</button>
      <button class="filter-btn" onclick="filterProgram('zakat',this)">🕌 Zakat</button>
      <button class="filter-btn" onclick="filterProgram('infaq',this)">💛 Infaq & Sedekah</button>
      <button class="filter-btn" onclick="filterProgram('wakaf',this)">🌿 Wakaf</button>
      <button class="filter-btn" onclick="filterProgram('sosial',this)">🤲 Sosial</button>
    </div>
    
    <div class="program-grid" id="program-grid">
        </div>
</div>
@endsection