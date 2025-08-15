<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page_title ?? 'PulseNex - Website Monitoring Service' }}</title>
  <link rel="stylesheet" href="/assets/css/normalize.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/responsive.css">
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
  <!-- EXTRA CLASS header-transparent -->
  <header class="{{ ($active_page ?? '') === 'home' ? 'header-transparent' : '' }}">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <a href="/">
            <img src="/assets/images/logo.png" alt="PulseNex Logo">
          </a>
        </div>
        <nav class="main-nav">
          <ul>
            <li class="{{ ($active_page ?? '') === 'home' ? 'active' : '' }}">
              <a href="/">Home</a>
            </li>
            <li class="{{ ($active_page ?? '') === 'features' ? 'active' : '' }}">
              <a href="/features">Features</a>
            </li>
            <li class="{{ ($active_page ?? '') === 'pricing' ? 'active' : '' }}">
              <a href="/pricing">Pricing</a>
            </li>
            <li class="{{ ($active_page ?? '') === 'contact' ? 'active' : '' }}">
              <a href="/contact">Contact</a>
            </li>
          </ul>
        </nav>
        <div class="auth-buttons">
          @auth
            <a href="dashboard/" class="btn btn-secondary">Dashboard</a>
            <a href="{{ route('logout') }}" class="btn btn-outline">Logout</a>
          @endauth
          @guest
            <a href="/login" class="btn btn-outline">Login</a>
            <a href="/register" class="btn btn-primary">Sign Up</a>
          @endguest
        </div>
        <button class="mobile-menu-toggle">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>

  <div class="mobile-menu">
    <ul>
      <li class="{{ ($active_page ?? '') === 'home' ? 'active' : '' }}">
        <a href="/">Home</a>
      </li>
      <li class="{{ ($active_page ?? '') === 'features' ? 'active' : '' }}">
        <a href="/features">Features</a>
      </li>
      <li class="{{ ($active_page ?? '') === 'pricing' ? 'active' : '' }}">
        <a href="/pricing">Pricing</a>
      </li>
      <li class="{{ ($active_page ?? '') === 'contact' ? 'active' : '' }}">
        <a href="/contact">Contact</a>
      </li>
      @auth
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="{{ route('logout') }}">Logout</a></li>
      @endauth
      @guest
        <li><a href="/login">Login</a></li>
        <li><a href="/register">Sign Up</a></li>
      @endguest
    </ul>
  </div>
