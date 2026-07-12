<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name', config('app.name')))</title>
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ setting_image('site_favicon') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,500&display=swap"
        rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f5f2ea;
            --ink: #141414;
            --mute: #55524a;
            --stone: #ddd6c7;
            --accent: #96742e;
            --accent-soft: #ece2c8;
            --card: #ffffff;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--ink);
            background: var(--bg);
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        .zf-serif {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .zf-mono {
            font-family: 'Montserrat', sans-serif;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .tape-track {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            z-index: 300;
            background: var(--stone);
        }

        .tape-fill {
            height: 100%;
            width: 0%;
            background: var(--accent);
            transition: width 0.05s linear;
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }

        /* ===== HEADER ===== */
        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            border-bottom: 1px solid var(--stone);
            position: sticky;
            top: 4px;
            background: rgba(245, 242, 234, 0.94);
            backdrop-filter: blur(10px);
            z-index: 200;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 52px;
            height: 52px;
            border-radius: 14px 26px 26px 14px;
            background: linear-gradient(150deg, var(--ink), #2a2a2a);
            color: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 21px;
            flex-shrink: 0;
            position: relative;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 14px rgba(20, 20, 20, 0.18);
        }

        .brand-mark::after {
            content: '';
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 1.5px solid rgba(150, 116, 46, 0.55);
            border-radius: 9px 20px 20px 9px;
            pointer-events: none;
        }

        .brand:hover .brand-mark {
            transform: rotate(-4deg) scale(1.05);
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 14px 26px 26px 14px;
        }

        .brand-name-wrap {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-name-wrap .brand-title {
            font-weight: 800;
            font-size: 21px;
            letter-spacing: -0.01em;
            color: var(--ink);
        }

        .brand-name-wrap .tagline-mini {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--accent);
            margin-top: 2px;
        }

        .nav-desktop {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* SMART ECOMMERCE HOVER MECHANICS */
        .nav-item-wrap {
            position: relative;
            padding: 10px 0; /* Extends hover hitbox to prevent accidental closure */
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            border-radius: 8px;
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
            transition: background 0.2s ease;
        }

        /* Hover trigger for desktop screens */
        @media (min-width: 861px) {
            .nav-item-wrap:hover .mega-dropdown {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0);
                pointer-events: auto;
            }
            .nav-item-wrap:hover .nav-link {
                background: var(--card);
            }
            .nav-item-wrap:hover .nav-link svg {
                transform: rotate(180deg);
            }
        }

        .nav-link svg {
            width: 13px;
            height: 13px;
            transition: transform 0.2s ease;
        }

        /* Smart Mega Dropdown Structure */
        .mega-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            width: 760px;
            max-height: 520px;
            overflow-y: auto;
            background: var(--card);
            border: 1px solid var(--stone);
            border-radius: 16px;
            box-shadow: 0 30px 60px rgba(20, 20, 20, 0.15);
            padding: 24px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
            z-index: 250;
        }

        .mega-cols {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px 24px;
            margin-bottom: 20px;
        }

        .mega-parent-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mega-parent-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 14px;
            padding: 8px 4px;
            border-bottom: 2px solid var(--stone);
            margin-bottom: 6px;
            color: var(--ink);
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .mega-parent-link:hover {
            color: var(--accent);
            border-color: var(--accent);
        }

        .mega-parent-link .count {
            font-size: 11px;
            font-weight: 600;
            color: var(--mute);
            background: var(--bg);
            padding: 2px 6px;
            border-radius: 20px;
        }

        .mega-child-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--mute);
            transition: all 0.2s ease;
        }

        .mega-child-link:hover {
            background: var(--bg);
            color: var(--ink);
            padding-left: 14px;
        }

        .mega-child-link .count {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--mute);
            font-variant-numeric: tabular-nums;
        }

        .mega-view-all {
            display: block;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            background: var(--ink);
            color: #fff;
            font-size: 13.5px;
            font-weight: 700;
            transition: background 0.2s ease;
        }

        .mega-view-all:hover {
            background: var(--accent);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-link {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 14px 9px 10px;
            border-radius: 999px;
            background: var(--card);
            border: 1.5px solid transparent;
            position: relative;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            transition: border-color 0.15s ease;
        }

        .action-link:hover {
            border-color: var(--stone);
        }

        .action-link svg {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
        }

        .action-link .action-label {
            white-space: nowrap;
        }

        .action-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px var(--bg);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px 6px 6px;
            background: var(--card);
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }

        .user-chip img {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-chip .avatar-fallback {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--ink);
            color: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--ink);
            color: #fff;
        }

        .btn-outline {
            background: var(--card);
            border: 1.5px solid var(--stone);
            color: var(--ink);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            border: 1.5px solid var(--stone);
            background: transparent;
            color: var(--ink);
            cursor: pointer;
        }

        .btn-ghost:hover {
            border-color: var(--ink);
        }

        .btn:disabled,
        .btn[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .hamburger-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--card);
            border-radius: 50%;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .hamburger-btn svg {
            width: 18px;
            height: 18px;
        }

        /* ===== MOBILE DRAWER ===== */
        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(20, 20, 20, 0.45);
            z-index: 400;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .mobile-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: min(340px, 86vw);
            background: var(--bg);
            z-index: 401;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(.2, .8, .2, 1);
            overflow-y: auto;
            padding: 22px;
            box-shadow: -10px 0 40px rgba(20, 20, 20, 0.15);
        }

        .mobile-drawer.open {
            transform: translateX(0);
        }

        .mobile-drawer-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .mobile-drawer-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--card);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 4px;
            font-size: 15px;
            font-weight: 600;
            border-bottom: 1px solid var(--stone);
        }

        .mobile-action-row .m-badge {
            background: var(--accent);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 999px;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .mobile-nav-link {
            display: block;
            padding: 14px 4px;
            font-size: 15px;
            font-weight: 600;
            border-bottom: 1px solid var(--stone);
        }

        .mobile-cat-group {
            margin-top: 6px;
        }

        /* SMART RESPONSIVE MOBILE ACCORDION */
        .mobile-cat-toggle-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background: none;
            border: none;
            font-family: inherit;
            text-align: left;
            cursor: pointer;
            padding: 12px 4px;
            border-bottom: 1px solid var(--stone);
        }

        .mobile-cat-parent-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
        }

        .mobile-cat-toggle-btn svg {
            width: 16px;
            height: 16px;
            color: var(--mute);
            transition: transform 0.25s ease;
        }

        .mobile-cat-toggle-btn.active svg {
            transform: rotate(180deg);
        }

        .mobile-cat-list {
            padding-left: 14px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
        }

        .mobile-cat-list a {
            display: flex;
            justify-content: space-between;
            padding: 10px 8px;
            font-size: 13px;
            color: var(--mute);
            font-weight: 500;
            border-bottom: 1px dashed rgba(0,0,0,0.05);
        }

        .mobile-cat-list a:last-child {
            border-bottom: none;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--mute);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .eyebrow::before {
            content: '';
            width: 18px;
            height: 1.5px;
            background: var(--accent);
            display: inline-block;
        }

        .sec-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .sec-head h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: clamp(24px, 3.2vw, 32px);
            color: var(--ink);
        }

        .sec-head p {
            color: var(--mute);
            font-size: 14px;
            max-width: 320px;
        }

        .marquee-wrap {
            border-top: 1px solid var(--stone);
            border-bottom: 1px solid var(--stone);
            overflow: hidden;
            padding: 14px 0;
            margin: 8px 0 60px;
        }

        .marquee-track {
            display: flex;
            gap: 40px;
            width: max-content;
            animation: scroll-left 26s linear infinite;
        }

        .marquee-track span {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: var(--ink);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .marquee-track span::after {
            content: '◆';
            color: var(--accent);
            font-size: 10px;
        }

        @keyframes scroll-left {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .cat-card {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: flex-end;
            padding: 18px;
            color: #fff;
            transition: transform 0.35s ease;
        }

        .cat-card:hover {
            transform: translateY(-4px);
        }

        .cat-card .cat-watermark {
            position: absolute;
            right: -8px;
            top: -20px;
            font-weight: 800;
            font-size: 120px;
            opacity: 0.16;
            line-height: 1;
            user-select: none;
        }

        .cat-card .cat-name {
            font-weight: 700;
            font-size: 20px;
            position: relative;
            z-index: 1;
        }

        .cat-card .cat-count {
            font-size: 11px;
            opacity: 0.85;
            position: relative;
            z-index: 1;
            display: block;
            margin-top: 4px;
        }

        .tag-row {
            display: flex;
            gap: 22px;
            overflow-x: auto;
            padding: 26px 4px 30px;
            scroll-snap-type: x proximity;
        }

        .tag-row::-webkit-scrollbar {
            height: 6px;
        }

        .tag-row::-webkit-scrollbar-thumb {
            background: var(--stone);
            border-radius: 10px;
        }

        .tag-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 22px;
        }

        .tag-card {
            background: var(--card);
            border: 1.5px solid var(--stone);
            border-radius: 6px 20px 20px 6px;
            position: relative;
            padding-top: 10px;
            transition: transform 0.35s cubic-bezier(.2, .8, .2, 1), box-shadow 0.35s ease;
        }

        .tag-row>.tag-card {
            flex: 0 0 240px;
            scroll-snap-align: start;
            transform: rotate(-2.5deg);
        }

        .tag-row>.tag-card:nth-child(even) {
            transform: rotate(2deg);
        }

        .tag-row>.tag-card:hover {
            transform: rotate(0deg) translateY(-8px);
            box-shadow: 0 24px 44px rgba(20, 20, 20, 0.12);
        }

        .tag-grid>.tag-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(20, 20, 20, 0.10);
        }

        .tag-card::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 20px;
            width: 15px;
            height: 15px;
            background: var(--bg);
            border: 1.5px solid var(--stone);
            border-radius: 50%;
            z-index: 2;
        }

        .tag-card-img {
            width: 100%;
            height: 190px;
            border-radius: 3px 16px 0 0;
            overflow: hidden;
            background: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tag-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tag-card-img svg {
            width: 40px;
            height: 40px;
            color: var(--mute);
            opacity: 0.5;
        }

        .tag-card-body {
            padding: 14px 16px 18px;
        }

        .tag-badge {
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--mute);
            display: block;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .tag-card h4 {
            font-weight: 700;
            font-size: 16px;
            color: var(--ink);
            margin-bottom: 6px;
            line-height: 1.3;
            min-height: 42px;
        }

        .tag-price-row {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .tag-price {
            font-weight: 700;
            font-size: 14px;
            color: var(--ink);
        }

        .tag-price-old {
            font-size: 12px;
            color: var(--mute);
            text-decoration: line-through;
        }

        .sold-out-strip {
            position: absolute;
            top: 10px;
            right: -6px;
            background: var(--ink);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            padding: 3px 10px;
            border-radius: 3px;
            z-index: 3;
        }

        .trust-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
        }

        .trust-card {
            border: 1px solid var(--stone);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: var(--card);
        }

        .trust-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .trust-icon svg {
            width: 18px;
            height: 18px;
            color: var(--ink);
        }

        .trust-card strong {
            display: block;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .trust-card span {
            font-size: 12.5px;
            color: var(--mute);
            line-height: 1.5;
        }

        .cta-band {
            background: var(--ink);
            border-radius: 24px;
            padding: 52px 40px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .cta-band::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(600px circle at 20% 20%, rgba(150, 116, 46, 0.35), transparent 60%);
        }

        .cta-band h2 {
            font-weight: 800;
            font-size: clamp(24px, 3.6vw, 34px);
            margin-bottom: 12px;
            position: relative;
        }

        .cta-band p {
            color: #d8d5cc;
            font-size: 14px;
            margin-bottom: 24px;
            position: relative;
        }

        .cta-band .btn-primary {
            position: relative;
            background: var(--accent);
        }

        .crumb {
            font-size: 13px;
            color: var(--mute);
            margin-bottom: 20px;
        }

        .crumb a:hover {
            color: var(--accent);
        }

        .crumb span {
            margin: 0 6px;
        }

        .page-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .site-footer {
            background: var(--ink);
            color: #d8d5cc;
            margin-top: 70px;
        }

        .footer-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 56px 20px 36px;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 36px;
        }

        .footer-col h4 {
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9a978d;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .footer-brand-mark {
            height: 40px;
            width: 40px;
            border-radius: 11px 20px 20px 11px;
            background: linear-gradient(150deg, var(--accent), #6f5620);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 16px;
        }

        .footer-brand strong {
            font-size: 18px;
            color: #fff;
            font-weight: 800;
        }

        .footer-tagline {
            font-size: 13px;
            color: #b3b0a5;
            line-height: 1.6;
            margin-bottom: 18px;
            max-width: 260px;
        }

        .footer-social {
            display: flex;
            gap: 10px;
        }

        .footer-social a {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .footer-social a:hover {
            background: var(--accent);
        }

        .footer-social svg {
            width: 15px;
            height: 15px;
            color: #fff;
        }

        .footer-link {
            display: block;
            font-size: 13.5px;
            color: #d8d5cc;
            padding: 6px 0;
        }

        .footer-link:hover {
            color: var(--accent);
        }

        .footer-contact-item {
            display: flex;
            gap: 9px;
            align-items: flex-start;
            font-size: 13px;
            color: #d8d5cc;
            padding: 6px 0;
            line-height: 1.5;
        }

        .footer-contact-item svg {
            width: 15px;
            height: 15px;
            color: var(--accent);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .footer-pay-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }

        .pay-badge {
            font-size: 10.5px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #9a978d;
        }

        .footer-credit a {
            color: #d8d5cc;
            font-weight: 700;
        }

        .footer-credit a:hover {
            color: var(--accent);
        }

        @media (max-width: 950px) {
            .mega-dropdown {
                width: 640px;
            }
            .mega-cols {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .footer-main {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 860px) {
            .nav-desktop,
            .action-link .action-label,
            .header-actions .user-chip,
            .header-actions form {
                display: none;
            }

            .action-link {
                padding: 10px;
                border-radius: 50%;
            }

            .hamburger-btn {
                display: flex;
            }
        }

        @media (max-width: 600px) {
            .site-header {
                padding: 12px 16px;
            }

            .brand-mark {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }

            .brand-name-wrap .brand-title {
                font-size: 17px;
            }

            .page-wrap {
                padding: 20px 16px;
            }

            .footer-main {
                grid-template-columns: 1fr;
                padding: 40px 20px 24px;
            }

            .footer-bottom-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="tape-track" id="tapeTrack">
        <div class="tape-fill" id="tapeFill"></div>
    </div>

    <header class="site-header">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">
                @if(setting('site_logo'))
                    <img src="{{ setting_image('site_logo') }}" alt="{{ setting('site_name') }}">
                @else
                    {{ strtoupper(substr(setting('site_name', config('app.name')), 0, 1)) }}
                @endif
            </span>
            <span class="brand-name-wrap">
                <span class="brand-title">{{ setting('site_name', config('app.name')) }}</span>
                @if(setting('site_tagline'))
                    <span class="tagline-mini">{{ setting('site_tagline') }}</span>
                @endif
            </span>
        </a>

        <nav class="nav-desktop">
            <div class="nav-item-wrap">
                <button type="button" class="nav-link" id="shopNavTrigger">
                    Shop
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="mega-dropdown" id="megaDropdown">
                    <div class="mega-cols">
                        @forelse ($navCategories ?? [] as $navCat)
                            @php
                                $childCount = $navCat->children->sum('products_count');
                                $totalCount = ($navCat->products_count ?? 0) + $childCount;
                            @endphp
                            <div class="mega-parent-block">
                                <a href="{{ route('shop.index', ['category' => $navCat->id]) }}" class="mega-parent-link">
                                    <span>{{ $navCat->name }}</span>
                                    <span class="count">{{ $totalCount }}</span>
                                </a>
                                @foreach ($navCat->children as $child)
                                    <a href="{{ route('shop.index', ['category' => $child->id]) }}" class="mega-child-link">
                                        {{ $child->name }}
                                        <span class="count">{{ $child->products_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @empty
                            <span style="font-size:13px; color:var(--mute); grid-column: 1/-1;">Categories coming soon.</span>
                        @endforelse
                    </div>
                    <a href="{{ route('shop.index') }}" class="mega-view-all">View All Products →</a>
                </div>
            </div>
            <a href="{{ route('shop.index') }}" class="nav-link">New Arrivals</a>
        </nav>

        <div class="header-actions">
            <a href="{{ route('cart.index') }}" class="action-link" title="Cart">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="action-label">Cart</span>
                <span class="action-badge cart-badge"
                    style="{{ ($cartCount ?? 0) > 0 ? 'display:flex;' : '' }}">{{ ($cartCount ?? 0) > 9 ? '9+' : ($cartCount ?? 0) }}</span>
            </a>

            @auth
                <a href="{{ route('orders.index') }}" class="action-link" title="My Orders">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="action-label">Orders</span>
                    <span class="action-badge orders-badge"></span>
                </a>
            @endauth

            @auth
                <div class="user-chip">
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" referrerpolicy="no-referrer"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="avatar-fallback" style="display:none;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    {{ explode(' ', auth()->user()->name)[0] }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            @else
                @unless(request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endunless
            @endauth

            <button type="button" class="hamburger-btn" onclick="toggleMobileNav()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileNav()"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-head">
            <strong class="zf-serif" style="font-size:17px;">Menu</strong>
            <button class="mobile-drawer-close" onclick="toggleMobileNav()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <a href="{{ route('home') }}" class="mobile-nav-link">Home</a>
        <a href="{{ route('shop.index') }}" class="mobile-nav-link">Shop All</a>

        <a href="{{ route('cart.index') }}" class="mobile-action-row">
            Cart
            <span class="m-badge cart-badge-mobile"
                style="{{ ($cartCount ?? 0) > 0 ? 'display:flex;' : '' }}">{{ ($cartCount ?? 0) > 9 ? '9+' : ($cartCount ?? 0) }}</span>
        </a>

        @auth
            <a href="{{ route('orders.index') }}" class="mobile-action-row">
                My Orders
                <span class="m-badge orders-badge-mobile"></span>
            </a>
        @endauth

        @if(($navCategories ?? collect())->isNotEmpty())
            <div class="mobile-cat-group">
                @foreach ($navCategories as $navCat)
                    @php $childCount = $navCat->children->sum('products_count'); @endphp
                    
                    <button class="mobile-cat-toggle-btn" onclick="toggleMobileAccordion(this)">
                        <span class="mobile-cat-parent-title">
                            {{ $navCat->name }}
                            <span class="zf-mono" style="font-size:11px; font-weight: normal; color:var(--mute);">({{ ($navCat->products_count ?? 0) + $childCount }})</span>
                        </span>
                        @if($navCat->children->isNotEmpty())
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        @endif
                    </button>
                    
                    @if($navCat->children->isNotEmpty())
                        <div class="mobile-cat-list">
                            <a href="{{ route('shop.index', ['category' => $navCat->id]) }}" style="font-weight:700; color:var(--ink);">
                                View All {{ $navCat->name }}
                            </a>
                            @foreach ($navCat->children as $child)
                                <a href="{{ route('shop.index', ['category' => $child->id]) }}">
                                    {{ $child->name }} 
                                    <span class="zf-mono" style="color:var(--mute);">{{ $child->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div style="margin-top:20px;">
            @auth
                <div class="user-chip" style="margin-bottom:12px;">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="">
                    @else
                        <span class="avatar-fallback">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                    {{ auth()->user()->name }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary" style="width:100%;">Login</a>
            @endauth
        </div>
    </div>

    <div class="page-wrap">
        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </div>

    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-col">
                <div class="footer-brand">
                    @if(setting('site_logo'))
                        <img src="{{ setting_image('site_logo') }}" alt="{{ setting('site_name') }}"
                            style="height:40px;width:40px;object-fit:contain;border-radius:11px;">
                    @else
                        <span class="footer-brand-mark">{{ strtoupper(substr(setting('site_name', config('app.name')), 0, 1)) }}</span>
                    @endif
                    <strong>{{ setting('site_name', config('app.name')) }}</strong>
                </div>
                <p class="footer-tagline">
                    {{ setting('site_tagline', 'A considered edit of everyday fashion, fitted to real life.') }}
                </p>
                @if(setting('facebook_url') || setting('instagram_url') || setting('whatsapp_number'))
                    <div class="footer-social">
                        @if(setting('facebook_url'))
                            <a href="{{ setting('facebook_url') }}" target="_blank" rel="noopener" title="Facebook">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0022 12z" />
                                </svg>
                            </a>
                        @endif
                        @if(setting('instagram_url'))
                            <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" title="Instagram">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="2" width="20" height="20" rx="5" />
                                    <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />
                                    <path d="M17.5 6.5h.01" />
                                </svg>
                            </a>
                        @endif
                        @if(setting('whatsapp_number'))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number')) }}"
                                target="_blank" rel="noopener" title="WhatsApp">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 00-8.5 15.2L2 22l4.9-1.5A10 10 0 1012 2zm5.7 14.2c-.2.6-1.3 1.2-1.8 1.3-.5.1-1 .2-3.2-.7-2.7-1.1-4.4-3.8-4.6-4-.1-.2-1-1.3-1-2.5s.6-1.8.9-2.1c.2-.2.5-.3.7-.3h.5c.2 0 .4 0 .6.4.2.5.7 1.7.8 1.8.1.2.1.3 0 .5-.1.2-.2.3-.3.5-.2.2-.3.3-.1.6.2.3.9 1.4 1.9 2.3 1.3 1.1 2.4 1.5 2.7 1.6.3.1.5.1.7-.1.2-.2.8-.9 1-1.2.2-.3.4-.2.7-.1.3.1 1.7.8 2 .9.3.2.5.2.6.3.1.2.1.9-.1 1.5z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="footer-col">
                <h4>Shop</h4>
                <a href="{{ route('shop.index') }}" class="footer-link">All Products</a>
                @foreach (($navCategories ?? collect())->take(5) as $navCat)
                    <a href="{{ route('shop.index', ['category' => $navCat->id]) }}"
                        class="footer-link">{{ $navCat->name }}</a>
                @endforeach
            </div>

            <div class="footer-col">
                <h4>Get in Touch</h4>
                @if(setting('contact_phone'))
                    <div class="footer-contact-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <a href="tel:{{ setting('contact_phone') }}">{{ setting('contact_phone') }}</a>
                    </div>
                @endif
                @if(setting('contact_email'))
                    <div class="footer-contact-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a>
                    </div>
                @endif
                @if(setting('contact_address'))
                    <div class="footer-contact-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ setting('contact_address') }}</span>
                    </div>
                @endif
            </div>

            <div class="footer-col">
                <h4>We Accept</h4>
                <div class="footer-pay-badges">
                    @if(setting('bkash_number'))<span class="pay-badge">bKash</span>@endif
                    @if(setting('nagad_number'))<span class="pay-badge">Nagad</span>@endif
                    @if(setting('cod_enabled') == '1')<span class="pay-badge">COD</span>@endif
                    @if(setting('bank_details'))<span class="pay-badge">Bank Transfer</span>@endif
                </div>
                <p style="font-size:12px; color:#b3b0a5; margin-top:12px; line-height:1.6;">Every order is manually
                    verified before dispatch — no automated payment gateway, no hidden fees.</p>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <span>&copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.</span>
                <span class="footer-credit">Developed by <a href="https://www.facebook.com/ainzone42" target="_blank"
                        rel="noopener">Md Aminul Islam Nur</a></span>
            </div>
        </div>
    </footer>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const IS_AUTH = @json(auth()->check());
        const ORDERS_COUNT_URL = @json(auth()->check() ? route('orders.count') : null);

        const tapeFill = document.getElementById('tapeFill');
        function updateTape() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            tapeFill.style.width = pct + '%';
        }
        window.addEventListener('scroll', updateTape, { passive: true });
        updateTape();

        document.addEventListener('DOMContentLoaded', () => {
            const revealEls = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window) {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) { entry.target.classList.add('in-view'); io.unobserve(entry.target); }
                    });
                }, { threshold: 0.12 });
                revealEls.forEach(el => io.observe(el));
            } else {
                revealEls.forEach(el => el.classList.add('in-view'));
            }

            if (IS_AUTH && ORDERS_COUNT_URL) refreshOrdersBadge();
        });

        function updateCartBadgeUI(count) {
            document.querySelectorAll('.cart-badge').forEach(el => {
                el.textContent = count > 9 ? '9+' : count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
            document.querySelectorAll('.cart-badge-mobile').forEach(el => {
                el.textContent = count > 9 ? '9+' : count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
        }
        document.addEventListener('cart:updated', (e) => updateCartBadgeUI(e.detail.count));

        async function refreshOrdersBadge() {
            try {
                const res = await fetch(ORDERS_COUNT_URL, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                updateOrdersBadgeUI(json.count ?? 0);
            } catch { }
        }
        function updateOrdersBadgeUI(count) {
            document.querySelectorAll('.orders-badge').forEach(el => {
                el.textContent = count > 9 ? '9+' : count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
            document.querySelectorAll('.orders-badge-mobile').forEach(el => {
                el.textContent = count > 9 ? '9+' : count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
        }
        document.addEventListener('orders:updated', refreshOrdersBadge);

        function toggleMobileNav() {
            document.getElementById('mobileDrawer').classList.toggle('open');
            document.getElementById('mobileOverlay').classList.toggle('open');
            document.body.style.overflow = document.getElementById('mobileDrawer').classList.contains('open') ? 'hidden' : '';
        }

        /* SMART SMARTPHONE ACCORDION LOGIC */
        function toggleMobileAccordion(btn) {
            btn.classList.toggle('active');
            const panel = btn.nextElementSibling;
            if (panel && panel.classList.contains('mobile-cat-list')) {
                if (panel.style.maxHeight && panel.style.maxHeight !== '0px') {
                    panel.style.maxHeight = '0px';
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('mobileDrawer')?.classList.remove('open');
                document.getElementById('mobileOverlay')?.classList.remove('open');
                document.body.style.overflow = '';
            }
        });

        function showToast(message, type = 'success') {
            let toast = document.getElementById('zfToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'zfToast';
                toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#141414;color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:500;box-shadow:0 8px 24px rgba(20,20,20,0.2);transform:translateY(100px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1);z-index:9999;max-width:320px;';
                document.body.appendChild(toast);
            }
            toast.textContent = message;
            toast.style.background = type === 'error' ? '#991b1b' : '#141414';
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
            clearTimeout(toast._t);
            toast._t = setTimeout(() => { toast.style.transform = 'translateY(100px)'; toast.style.opacity = '0'; }, 3000);
        }
    </script>

    @stack('scripts')
</body>

</html>