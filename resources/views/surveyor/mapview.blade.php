{{-- resources/views/surveyor/ward-map.blade.php --}}
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: 'Segoe UI', 'Poppins', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        /* MAP CONTAINER */
        #map {
            width: 100%;
            height: calc(90vh - 60px);
            border-radius: 0;
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        /* PAGE HEADER - Desktop */
        .page-title {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 16px 25px;
            border-radius: 16px;
            margin-bottom: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .page-title h3 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .page-title h3 i {
            margin-right: 12px;
            color: #3b82f6;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-header h4 i {
            font-size: 20px;
            color: #3b82f6;
        }

        /* Mobile Bottom Navigation Bar */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1200;
            padding: 8px 16px;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .mobile-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 16px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .mobile-nav-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .mobile-nav-btn:active::before {
            width: 100%;
            height: 100%;
        }

        .mobile-nav-btn i {
            font-size: 22px;
            color: #64748b;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .mobile-nav-btn span {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .mobile-nav-btn.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-2px);
        }

        .mobile-nav-btn.active i,
        .mobile-nav-btn.active span {
            color: white;
        }

        .mobile-nav-btn:active {
            transform: scale(0.95);
        }

        /* FLOATING BUTTONS - Desktop */
        #layerToggleBtn,
        #searchToggleBtn,
        #editToggleBtn,
        #liveToggleBtn,
        #routeBtn {
            position: absolute;
            right: 20px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 16px;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            backdrop-filter: blur(10px);
        }

        #layerToggleBtn:hover,
        #searchToggleBtn:hover,
        #editToggleBtn:hover,
        #liveToggleBtn:hover,
        #routeBtn:hover {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.5);
        }

        #layerToggleBtn:active,
        #searchToggleBtn:active,
        #editToggleBtn:active,
        #liveToggleBtn:active,
        #routeBtn:active {
            transform: scale(0.95);
        }

        #layerToggleBtn {
            top: 130px;
        }

        #searchToggleBtn {
            top: 195px;
        }

        #liveToggleBtn {
            top: 260px;
        }

        #routeBtn {
            top: 325px;
        }

        #editToggleBtn {
            top: 390px;
        }

        /* Edit Panel */
        .edit-Lable {
            position: absolute;
            top: 390px;
            right: 90px;
            z-index: 1100;
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .edit-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .edit-Lable select {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .edit-Lable select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Search Label - Desktop */
        .search-Lable {
            position: absolute;
            top: 195px;
            right: 90px;
            z-index: 1100;
            width: 350px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .search-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .search-Lable input {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            font-size: 14px;
            flex: 1;
            outline: none;
            transition: all 0.3s ease;
            background: white;
        }

        .search-Lable input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-Lable button {
            border-radius: 12px;
            padding: 12px 24px;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .search-Lable button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Search Suggestions */
        .search-suggestions {
            display: none;
            background: white;
            border-radius: 16px;
            max-height: 320px;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-top: 5px;
        }

        .search-suggestions.show {
            display: block;
            animation: fadeInDown 0.3s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            transform: translateX(4px);
        }

        .suggestion-item.selected {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        }

        .suggestion-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border-radius: 10px;
            color: #2563eb;
            font-size: 16px;
        }

        .suggestion-content {
            flex: 1;
        }

        .suggestion-title {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }

        .suggestion-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .suggestion-type {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            background: #e2e8f0;
            color: #475569;
            font-weight: 600;
        }

        /* Layer Switcher Panel - Desktop */
        .layer-switcher {
            position: absolute;
            top: 125px;
            right: 90px;
            z-index: 1100;
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .layer-switcher.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .layer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .layer-header div {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .layer-header i {
            color: #2563eb;
            font-size: 20px;
        }

        #closeLayerPanel {
            border: none;
            background: #eff6ff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            color: #1e40af;
            transition: all 0.3s ease;
        }

        #closeLayerPanel:hover {
            background: #dbeafe;
            transform: rotate(90deg);
        }

        .layer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .layer-item:hover {
            background: #eff6ff;
            transform: translateX(4px);
        }

        .layer-item input {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #2563eb;
            position: relative;
            transition: all 0.3s ease;
        }

        .layer-item input:checked+.checkmark {
            background: #2563eb;
            animation: pulse 0.3s ease;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .layer-item input:checked+.checkmark::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 13px;
            top: -1px;
            left: 3px;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            z-index: 1300;
            animation: slideUp 0.3s ease;
            pointer-events: none;
            font-weight: 500;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .toast-notification.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .toast-notification.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .toast-notification.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Route Info Panel */
        .route-info-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 1100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .route-info-panel.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .route-info-panel h5 {
            margin: 0 0 12px 0;
            font-weight: 700;
            font-size: 18px;
            color: #1e293b;
        }

        .route-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .route-stat {
            flex: 1;
            text-align: center;
        }

        .route-stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #2563eb;
        }

        .route-stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .close-route-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f1f5f9;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .close-route-btn:hover {
            background: #fee2e2;
            color: #dc2626;
            transform: rotate(90deg);
        }

        .start-navigation-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .start-navigation-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1400;
            display: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Highlight Layer */
        .highlight-layer {
            z-index: 1000;
        }

        /* Shop Forms Styling */
        .shop-item {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .shop-item:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .remove-shop-btn {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-shop-btn:hover {
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            transform: scale(1.05);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 24px;
            overflow: hidden;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-body {
            padding: 20px 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        /* Card Styling for Forms */
        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            padding: 14px 20px;
            font-weight: 600;
            border: none;
        }

        .card-body {
            padding: 20px;
        }

        /* Form Control Styling */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
            color: #475569;
        }

        /* Error Message Styling */
        .error-message {
            font-size: 11px;
            margin-top: 5px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .is-invalid {
            border-color: #dc2626 !important;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }

            .mobile-header {
                display: block;
            }

            .mobile-bottom-nav {
                display: flex;
            }

            #map {
                height: calc(90vh - 56px - 72px);
                margin-bottom: 0;
            }

            /* Hide floating buttons on mobile */
            #layerToggleBtn,
            #searchToggleBtn,
            #editToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                display: none;
            }

            /* Panels on mobile - appear above bottom nav */
            .layer-switcher,
            .search-Lable,
            .edit-Lable {
                position: fixed;
                top: auto;
                bottom: 80px;
                left: 12px;
                right: 12px;
                width: auto;
                max-width: calc(100% - 24px);
                border-radius: 20px;
                padding: 16px;
                z-index: 1250;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                animation: slideUp 0.3s ease;
            }

            .search-Lable {
                bottom: 80px;
                top: auto;
                right: 12px;
                left: 12px;
                width: auto;
            }

            .edit-Lable {
                bottom: 80px;
                top: auto;
                right: 12px;
                left: 12px;
                width: auto;
            }

            .layer-switcher {
                bottom: 80px;
                top: auto;
                right: 12px;
                left: 12px;
            }

            .route-info-panel {
                left: 12px;
                right: 12px;
                bottom: 80px;
                padding: 16px;
                max-width: none;
            }

            .search-suggestions {
                max-height: 250px;
            }

            .suggestion-item {
                padding: 10px 14px;
            }

            .suggestion-title {
                font-size: 13px;
            }

            .suggestion-subtitle {
                font-size: 11px;
            }

            .route-stats {
                gap: 15px;
            }

            .route-stat-value {
                font-size: 18px;
            }

            /* Modal adjustments for mobile */
            .modal-dialog {
                margin: 10px;
            }

            .modal-body {
                padding: 16px;
                max-height: 60vh;
            }

            .card-header {
                padding: 12px 16px;
            }

            .card-body {
                padding: 16px;
            }

            /* Toast on mobile */
            .toast-notification {
                bottom: 90px;
                font-size: 12px;
                padding: 10px 20px;
            }

            /* Shop items on mobile */
            .shop-item {
                padding: 16px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .mobile-nav-btn {
                padding: 6px 10px;
            }

            .mobile-nav-btn i {
                font-size: 18px;
            }

            .mobile-nav-btn span {
                font-size: 9px;
            }

            .search-Lable input {
                padding: 8px 12px;
                font-size: 13px;
            }

            .search-Lable button {
                padding: 8px 16px;
                font-size: 12px;
            }

            .edit-Lable select {
                padding: 8px;
                font-size: 12px;
            }

            .route-info-panel {
                padding: 14px;
            }

            .route-stat-value {
                font-size: 16px;
            }

            .route-stat-label {
                font-size: 10px;
            }

            .start-navigation-btn {
                padding: 12px;
                font-size: 13px;
            }

            .card-header h6 {
                font-size: 14px;
            }

            .form-label {
                font-size: 12px;
            }

            .form-control,
            .form-select {
                padding: 8px 12px;
                font-size: 13px;
            }
        }

        /* Tablet Landscape */
        @media (min-width: 769px) and (max-width: 1024px) {

            #layerToggleBtn,
            #searchToggleBtn,
            #editToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                width: 50px;
                height: 50px;
                right: 15px;
            }

            .layer-switcher {
                right: 80px;
                width: 260px;
            }

            .search-Lable {
                right: 80px;
                width: 320px;
            }

            .edit-Lable {
                right: 80px;
                width: 260px;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Button Hover Effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Animation for panels */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Active state for buttons */
        .btn-active {
            transform: scale(0.95);
        }

        /* Pulse animation for notifications */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
            }
        }
    </style>
@endsection

@section('content')
    <!-- Mobile Header -->
    <div class="mobile-header">
        <h4>
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward {{ $ward->ward_no }} Map
        </h4>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <button class="mobile-nav-btn" id="mobileLayerBtn">
            <i class="fas fa-layer-group"></i>
            <span>Layers</span>
        </button>
        <button class="mobile-nav-btn" id="mobileSearchBtn">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </button>
        <button class="mobile-nav-btn" id="mobileLocationBtn">
            <i class="fas fa-location-dot"></i>
            <span>Location</span>
        </button>
        <button class="mobile-nav-btn" id="mobileRouteBtn">
            <i class="fas fa-route"></i>
            <span>Route</span>
        </button>
        <button class="mobile-nav-btn" id="mobileEditBtn">
            <i class="fas fa-pen-to-square"></i>
            <span>Edit</span>
        </button>
    </div>

    <!-- Desktop Header -->
    <div class="container-fluid py-3 desktop-only">
        <div class="page-title">
            <h3>
                <i class="fas fa-map-marked-alt me-2"></i>
                Ward Map View - Ward {{ $ward->ward_no }}
            </h3>
        </div>
    </div>

    <div id="map"></div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner"></div>

    <!-- Route Info Panel -->
    <div id="routeInfoPanel" class="route-info-panel closed">
        <button class="close-route-btn" id="closeRouteBtn">&times;</button>
        <h5><i class="fas fa-route me-2"></i>Route to Destination</h5>
        <div id="destinationName" style="font-size: 14px; color: #64748b; margin-bottom: 10px;"></div>
        <div class="route-stats">
            <div class="route-stat">
                <div class="route-stat-value" id="routeDistance">0 km</div>
                <div class="route-stat-label">Distance</div>
            </div>
            <div class="route-stat">
                <div class="route-stat-value" id="routeDuration">0 min</div>
                <div class="route-stat-label">Est. Time</div>
            </div>
        </div>
        <button class="start-navigation-btn" id="startNavigationBtn">
            <i class="fas fa-directions me-2"></i>Start Navigation
        </button>
    </div>

    <!-- Floating Action Buttons (Desktop only) -->
    <div id="layerToggleBtn" title="Toggle Layers"><i class="fas fa-layer-group"></i></div>
    <div id="searchToggleBtn" title="Search"><i class="fas fa-search"></i></div>
    <div id="liveToggleBtn" title="My Location"><i class="fas fa-location-dot"></i></div>
    <div id="routeBtn" title="Get Route"><i class="fas fa-route"></i></div>
    <div id="editToggleBtn" title="Edit Tools"><i class="fas fa-pen-to-square"></i></div>

    <!-- Search Panel -->
    <div id="searchLabel" class="search-Lable closed">
        <div class="search-input-wrapper">
            <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID or Road Name..."
                autocomplete="off">
            <button id="searchGisidBtn"><i class="fas fa-search"></i> Search</button>
        </div>
        <div id="searchSuggestions" class="search-suggestions"></div>
    </div>

    <!-- Layer Switcher Panel -->
    <div id="layerSwitcher" class="layer-switcher closed">
        <div class="layer-header">
            <div><i class="fas fa-layer-group"></i><span>Map Layers</span></div>
            <button id="closeLayerPanel"><i class="fas fa-times"></i></button>
        </div>
        <label class="layer-item"><input type="checkbox" id="osmToggle" checked><span class="checkmark"></span><span>OSM
                Map</span></label>
        <label class="layer-item"><input type="checkbox" id="satelliteToggle"><span
                class="checkmark"></span><span>Satellite</span></label>
        <label class="layer-item"><input type="checkbox" id="droneToggle" checked><span class="checkmark"></span><span>Drone
                Image</span></label>
        <label class="layer-item"><input type="checkbox" id="boundaryToggle" checked><span
                class="checkmark"></span><span>Ward Boundary</span></label>
        <label class="layer-item"><input type="checkbox" id="polygonToggle" checked><span
                class="checkmark"></span><span>Buildings</span></label>
        <label class="layer-item"><input type="checkbox" id="lineToggle" checked><span
                class="checkmark"></span><span>Roads</span></label>
        <label class="layer-item"><input type="checkbox" id="pointToggle" checked><span
                class="checkmark"></span><span>Points</span></label>
    </div>

    <!-- Edit Panel -->
    <div id="editLabel" class="edit-Lable closed">
        <select class="form-select" id="editToolSelect">
            <option value="none">Select Tool</option>
            <option value="Polygon">Draw Polygon</option>
            <option value="Line">Draw Line</option>
            <option value="Point">Draw Point</option>
            <option value="Modify">Modify Feature</option>
            <option value="Delete">Delete Feature</option>
        </select>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash-alt me-2"></i>Delete Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Warning: This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter GIS ID to Delete</label>
                            <input type="text" class="form-control" id="deleteGisIdInput" name="gisid"
                                placeholder="e.g., A1001 or 1001" required>
                        </div>
                        <div id="featurePreview" class="mt-3 p-3 border rounded" style="display: none;">
                            <h6>Feature Details:</h6>
                            <p id="previewText"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Feature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   <script>
        $(document).ready(function() {
            // ==================== DATA FROM SERVER ====================
            let polygonDatas = @json($polygonDatas ?? []);
            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);
            let wardData = {
                ward_no: @json($ward->ward_no ?? ''),
                drone_image: @json($ward->drone_image ?? null),
                extent_left: @json($ward->extent_left ?? null),
                extent_bottom: @json($ward->extent_bottom ?? null),
                extent_right: @json($ward->extent_right ?? null),
                extent_top: @json($ward->extent_top ?? null),
                boundary: @json($ward->boundary ?? null)
            };

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== LOCATION VARIABLES ====================
            let currentLocationLayer = null,
                accuracyLayer = null,
                currentPosition = null,
                currentPositionLonLat = null;
            let locationTracking = false,
                watchId = null;

            // ==================== ROUTE VARIABLES ====================
            let currentRoute = null;
            let routeSource = null;
            let routeLayer = null;
            let selectedBuilding = null;
            let currentLocationMarker = null;

            // ==================== SEARCH VARIABLES ====================
            let allBuildings = [];

            // ==================== HELPER FUNCTIONS ====================
            function showLoading(show) {
                if (show) {
                    if ($('#mapLoading').length === 0) {
                        $('body').append('<div id="mapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i> Loading map...</div>');
                    }
                    $('#mapLoading').show();
                } else {
                    $('#mapLoading').hide();
                }
            }

            function showFlashMessage(message, type = 'info') {
                const alertClass = {
                    'success': '#28a745',
                    'error': '#dc3545',
                    'warning': '#ffc107',
                    'info': '#17a2b8'
                }[type] || '#17a2b8';

                const flashHtml = `<div class="alert alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; background: ${alertClass}; color: white; padding: 12px 20px; border-radius: 10px; min-width: 250px; z-index: 10000;">${message}<button type="button" class="btn-close btn-close-white" style="float: right; margin-left: 10px;" data-bs-dismiss="alert"></button></div>`;
                $('body').append(flashHtml);
                setTimeout(() => $(flashHtml).fadeOut(300, function() { $(this).remove(); }), 4000);
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel, #routeInfo').removeClass('open');
            }

            // ==================== FORMATTING FUNCTIONS ====================
            function formatDistance(meters) {
                if (!meters || isNaN(meters)) return '0 m';
                if (meters < 1000) return Math.round(meters) + ' m';
                return (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                if (!seconds || isNaN(seconds)) return '0 min';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + ' min';
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return hours + ' h ' + mins + ' min';
            }

            // Calculate straight line distance between two coordinates
            function calculateStraightLineDistance(coord1, coord2) {
                const [lon1, lat1] = coord1;
                const [lon2, lat2] = coord2;
                const R = 6371000;
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            // ==================== ROUTE FUNCTIONS ====================

            async function getRouteFromOSRM(startCoord, endCoord) {
                // Validate coordinates
                if (!startCoord || !endCoord || startCoord.length < 2 || endCoord.length < 2) {
                    throw new Error('Invalid coordinates');
                }

                const url = `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson`;
                console.log('Fetching route from OSRM:', url);

                const response = await fetch(url);
                const data = await response.json();

                if (data.code !== 'Ok' || !data.routes || !data.routes.length) {
                    throw new Error('No route found');
                }
                return data.routes[0];
            }

            function drawRouteOnMap(geometry) {
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                }
                if (routeSource) {
                    routeSource.clear();
                }

                routeSource = new ol.source.Vector();
                routeLayer = new ol.layer.Vector({
                    source: routeSource,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#2563eb',
                            width: 5,
                            lineDash: [10, 10]
                        })
                    }),
                    zIndex: 1000
                });

                if (geometry && geometry.type === 'LineString' && geometry.coordinates && geometry.coordinates.length >= 2) {
                    const coordinates = geometry.coordinates
                        .filter(coord => coord && coord.length >= 2 && !(Math.abs(coord[0]) < 0.0001 && Math.abs(coord[1]) < 0.0001))
                        .map(coord => ol.proj.fromLonLat(coord));

                    if (coordinates.length >= 2) {
                        routeSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coordinates)
                        }));
                        map.addLayer(routeLayer);

                        const extent = routeSource.getExtent();
                        if (extent && extent[0] !== Infinity) {
                            map.getView().fit(extent, {
                                padding: [50, 50, 50, 50],
                                duration: 1000
                            });
                        }
                    }
                }
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker || !currentPositionLonLat) {
                    showFlashMessage('Please enable your location first', 'warning');
                    startLocationTracking();
                    return;
                }

                if (!feature) {
                    showFlashMessage('Please select a destination first', 'error');
                    return;
                }

                $('#loadingSpinner').show();

                try {
                    const currentCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry().getCoordinates();
                    const currentLonLat = ol.proj.toLonLat(currentCoords);

                    const geometry = feature.getGeometry();
                    let targetCoords;
                    if (geometry.getType() === 'Point') {
                        targetCoords = geometry.getCoordinates();
                    } else {
                        const extent = geometry.getExtent();
                        targetCoords = [(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2];
                    }

                    const targetLonLat = ol.proj.toLonLat(targetCoords);
                    currentPositionLonLat = currentLonLat;

                    let route;
                    try {
                        route = await getRouteFromOSRM(currentLonLat, targetLonLat);
                    } catch (e) {
                        console.warn('OSRM route failed, using straight line:', e.message);
                        const distance = calculateStraightLineDistance(currentLonLat, targetLonLat);
                        route = {
                            distance: distance,
                            duration: (distance / 1000 / 30) * 60,
                            geometry: {
                                type: "LineString",
                                coordinates: [currentLonLat, targetLonLat]
                            }
                        };
                    }

                    drawRouteOnMap(route.geometry);

                    $('#routeSummary').html(`
                        <div><i class="fas fa-map-marker-alt"></i> <strong>Destination:</strong> GIS ID: ${feature.get('gisid') || 'Selected Location'}</div>
                        <div><i class="fas fa-road"></i> <strong>Distance:</strong> ${formatDistance(route.distance)}</div>
                        <div><i class="fas fa-clock"></i> <strong>Estimated Time:</strong> ${formatDuration(route.duration)}</div>
                    `);

                    $('#routeInfo').addClass('open');
                    currentRoute = route;
                    currentRoute.endCoord = targetLonLat;

                    showFlashMessage('Route calculated successfully', 'success');

                } catch (error) {
                    console.error('Route error:', error);
                    showFlashMessage('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function getRouteToBuilding(gisid, targetCoords) {
                if (!targetCoords || targetCoords.length < 2 || isNaN(targetCoords[0]) || isNaN(targetCoords[1])) {
                    showFlashMessage('Invalid building coordinates. Please try another building.', 'error');
                    return;
                }

                const tempFeature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat(targetCoords)),
                    gisid: gisid
                });
                calculateAndDisplayRoute(tempFeature);
            }

            function clearRoute() {
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                if (routeSource) {
                    routeSource.clear();
                    routeSource = null;
                }
                currentRoute = null;
                $('#routeInfo').removeClass('open');
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showFlashMessage("Geolocation not supported", 'error');
                    return;
                }

                $('#loadingSpinner').show();
                $('#locationBtn').addClass('active');
                locationTracking = true;

                if ($('#centerMyLocationBtn').length === 0) {
                    $('body').append('<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>');
                    $('#centerMyLocationBtn').on('click', centerToMyLocation);
                }

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                        currentPositionLonLat = [pos.coords.longitude, pos.coords.latitude];
                        showFlashMessage('Location acquired successfully', 'success');
                        $('#loadingSpinner').hide();
                    },
                    function(err) {
                        $('#loadingSpinner').hide();
                        let errorMsg = "Unable to get location";
                        if (err.code === err.PERMISSION_DENIED) {
                            errorMsg = "Please enable location permissions";
                        } else if (err.code === err.TIMEOUT) {
                            errorMsg = "Location request timed out";
                        }
                        showFlashMessage(errorMsg, 'error');
                        locationTracking = false;
                        $('#locationBtn').removeClass('active');
                        $('#centerMyLocationBtn').remove();
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );

                watchId = navigator.geolocation.watchPosition(
                    function(pos) {
                        updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                        currentPositionLonLat = [pos.coords.longitude, pos.coords.latitude];
                    },
                    function(err) {},
                    { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
                );
            }

            function stopLocationTracking() {
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                locationTracking = false;
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentLocationLayer = null;
                accuracyLayer = null;
                currentLocationMarker = null;
                currentPositionLonLat = null;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];
                currentPositionLonLat = [lon, lat];

                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                if (currentLocationMarker) map.removeLayer(currentLocationMarker);

                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, accuracy)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ff4444', width: 2 }),
                        fill: new ol.style.Fill({ color: 'rgba(255,68,68,0.15)' })
                    })
                });
                map.addLayer(accuracyLayer);

                currentLocationLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({ color: '#ff4444' }),
                            stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
                        })
                    })
                });
                map.addLayer(currentLocationLayer);

                currentLocationMarker = new ol.layer.Vector({
                    source: new ol.source.Vector(),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({ color: '#ff4444' }),
                            stroke: new ol.style.Stroke({ color: '#ffffff', width: 3 })
                        })
                    })
                });
                currentLocationMarker.getSource().addFeature(new ol.Feature({
                    geometry: new ol.geom.Point(coords)
                }));
                map.addLayer(currentLocationMarker);

                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            function centerToMyLocation() {
                if (currentPositionLonLat) {
                    let coords = ol.proj.fromLonLat(currentPositionLonLat);
                    map.getView().animate({ center: coords, zoom: 19, duration: 1000 });
                    showFlashMessage('Centered on your location', 'info');
                } else {
                    showFlashMessage('Location not available. Please enable location tracking first.', 'warning');
                    startLocationTracking();
                }
            }

            // ==================== BUILD SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = [];
                $.each(polygonDatas, function(i, building) {
                    let info = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        building_type: building.building_type,
                        road_name: building.road_name,
                        zone: building.zone,
                        number_floor: building.number_floor,
                        coordinates: null,
                        assessments: []
                    };

                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0, cy = 0;
                                    $.each(coords[0], function(k, c) {
                                        cx += c[0];
                                        cy += c[1];
                                    });
                                    info.coordinates = [cx / coords[0].length, cy / coords[0].length];
                                }
                            } catch(e) {}
                            return false;
                        }
                    });

                    if (building.pointdata) {
                        $.each(building.pointdata, function(j, a) {
                            info.assessments.push({
                                assessment_no: a.assessment,
                                owner_name: a.owner_name || a.present_owner_name,
                                phone: a.phone_number
                            });
                        });
                    }
                    allBuildings.push(info);
                });
            }

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                if (!text || !text.trim()) {
                    $('#searchResults').html('<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }

                let term = text.toLowerCase().trim();
                let results = [];

                $.each(allBuildings, function(i, b) {
                    let match = false, type = '', val = '';

                    if (b.gisid && b.gisid.toLowerCase().includes(term)) {
                        match = true; type = 'GIS ID'; val = b.gisid;
                    } else if (b.building_usage && b.building_usage.toLowerCase().includes(term)) {
                        match = true; type = 'Building Usage'; val = b.building_usage;
                    } else if (b.road_name && b.road_name.toLowerCase().includes(term)) {
                        match = true; type = 'Road Name'; val = b.road_name;
                    } else {
                        $.each(b.assessments, function(j, a) {
                            if (a.assessment_no && a.assessment_no.toLowerCase().includes(term)) {
                                match = true; type = 'Assessment No'; val = a.assessment_no;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                match = true; type = 'Owner Name'; val = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                match = true; type = 'Phone'; val = a.phone;
                                return false;
                            }
                        });
                    }

                    if (match) {
                        results.push({
                            gisid: b.gisid,
                            matchType: type,
                            matchValue: val,
                            building: b,
                            coordinates: b.coordinates
                        });
                    }
                });

                let $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html('<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    let lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    let lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${r.gisid}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${r.matchType}: ${r.matchValue}</div>
                        <div class="result-owner"><i class="fas fa-location-dot"></i> ${r.building.road_name || 'No road'} | ${r.building.zone || 'No zone'}</div>
                        <button class="direction-btn"><i class="fas fa-directions"></i> Get Directions</button>
                    </div>`);
                });

                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn')) {
                        zoomToBuilding($(this).data('gisid'));
                        closeAllPanels();
                    }
                });

                $('.direction-btn').off('click').on('click', function(e) {
                    e.stopPropagation();
                    let p = $(this).closest('.search-result-item');
                    let lon = parseFloat(p.data('lon'));
                    let lat = parseFloat(p.data('lat'));
                    if (lon && lat && !isNaN(lon) && !isNaN(lat)) {
                        selectedBuilding = {
                            gisid: p.data('gisid'),
                            coords: [lon, lat]
                        };
                        getRouteToBuilding(p.data('gisid'), [lon, lat]);
                        closeAllPanels();
                    } else {
                        showFlashMessage("Coordinates not available for this building", 'error');
                    }
                });
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer) return;
                let features = polygonLayer.getSource().getFeatures();
                let f = null;
                for (let i = 0; i < features.length; i++) {
                    if (features[i].get('gisid') == gisid) {
                        f = features[i];
                        break;
                    }
                }
                if (f) {
                    let e = f.getGeometry().getExtent();
                    map.getView().fit(e, { padding: [50, 50, 50, 50], duration: 800 });
                    showPopup(gisid, ol.extent.getCenter(e));
                } else {
                    showFlashMessage("Building not found on map", 'error');
                }
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', { class: 'ol-popup', style: 'display:none' })[0];
                $('body').append(popupElement);
                return new ol.Overlay({
                    element: popupElement,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -10]
                });
            }

            window.closePopup = function() {
                $('.ol-popup').hide();
            };

            window.switchTab = function(t) {
                $('.popup-tab-content, .popup-tab').removeClass('active');
                $('#tab-' + t).addClass('active');
                $('.popup-tab[data-tab="' + t + '"]').addClass('active');
                currentActiveTab = t;
            };

            function showPopup(gisid, coord) {
                let pd = polygonDatas.find(p => p.gisid == gisid);
                if (!pd) return;

                let assessments = pd.pointdata || [];
                let shops = [];
                $.each(assessments, function(i, a) {
                    if (a.shops) {
                        $.each(a.shops, function(j, s) {
                            shops.push({ ...s, assessmentNumber: a.assessment || 'Bill ' + (i + 1) });
                        });
                    }
                });

                let buildingHtml = `<div class="building-details-content">${[
                    ['fingerprint', 'GIS ID', pd.gisid],
                    ['building', 'Building Usage', pd.building_usage],
                    ['home', 'Building Type', pd.building_type],
                    ['layer-group', 'Floors', pd.number_floor],
                    ['receipt', 'Total Bills', pd.number_bill],
                    ['store', 'Total Shops', pd.total_shops],
                    ['road', 'Road Name', pd.road_name],
                    ['map-pin', 'Zone', pd.zone]
                ].map(([i,l,v]) => `<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v || 'N/A'}</div></div>`).join('')}</div>`;

                let assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) =>
                        `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i+1)}</span>
                                <span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span>
                            </div>
                            <div class="assessment-body">
                                ${[['Owner', a.owner_name || a.present_owner_name], ['Phone', a.phone_number], ['Floor', a.floor], ['Usage', a.bill_usage], ['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                            </div>
                        </div>`
                    ).join('');

                let shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' :
                    shops.map(s => `
                        <div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name || 'Shop'}</div>
                            ${[['Category', s.shop_category], ['Owner', s.shop_owner_name], ['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                        </div>
                    `).join('');

                let html = `
                    <div class="popup-header">
                        <h4><i class="fas fa-building"></i> Building Details</h4>
                        <button class="popup-close" onclick="closePopup()">&times;</button>
                    </div>
                    <div class="popup-tabs">
                        <button class="popup-tab ${currentActiveTab == 'building' ? 'active' : ''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                        <button class="popup-tab ${currentActiveTab == 'assessments' ? 'active' : ''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button>
                        <button class="popup-tab ${currentActiveTab == 'shops' ? 'active' : ''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${shops.length})</button>
                    </div>
                    <div id="tab-building" class="popup-tab-content ${currentActiveTab == 'building' ? 'active' : ''}">${buildingHtml}</div>
                    <div id="tab-assessments" class="popup-tab-content ${currentActiveTab == 'assessments' ? 'active' : ''}"><div style="padding:12px">${assessmentsHtml}</div></div>
                    <div id="tab-shops" class="popup-tab-content ${currentActiveTab == 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>
                    <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button>
                    </div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) {
                    popupOverlay.setPosition(coord);
                }

                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    getRouteToBuilding(pd.gisid, [coord[0], coord[1]]);
                });

                $('.assessment-card').off('click').on('click', function() {
                    let id = $(this).data('id');
                    let num = $(this).data('assessment');
                    $(this).after(`
                        <div class="assessment-form-container">
                            <button class="close-form-btn" style="float:right; background:none; border:none; color:#ff4444; font-size:20px;">&times;</button>
                            <h4 style="color:#ffc107; margin-bottom:15px;">QC Form - ${num}</h4>
                            <form class="qc-form">
                                <input type="hidden" name="assessment_id" value="${id}">
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Square Feet:</label>
                                    <input type="number" name="qc_sqfeet" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Usage:</label>
                                    <select name="qc_usage" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                        <option value="">Select</option>
                                        <option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Industrial">Industrial</option>
                                    </select>
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">Tax Amount (₹):</label>
                                    <input type="number" name="tax_amount" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button type="submit" style="flex:1; background:#28a745; color:white; border:none; padding:10px; border-radius:8px;">Save</button>
                                    <button type="button" class="cancel-form-btn" style="flex:1; background:#dc3545; color:white; border:none; padding:10px; border-radius:8px;">Cancel</button>
                                </div>
                            </form>
                        </div>
                    `);

                    $('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        let hasValues = $(this).find('input[name="qc_sqfeet"]').val() &&
                            $(this).find('select[name="qc_usage"]').val() &&
                            $(this).find('input[name="tax_amount"]').val();
                        let $badge = $(this).closest('.assessment-card').find('.badge');
                        if (hasValues) {
                            $badge.removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check-circle"></i> QC Complete');
                        } else {
                            $badge.removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-clock"></i> QC Pending');
                        }
                        showFlashMessage('QC Saved! Status: ' + (hasValues ? 'QC Complete' : 'QC Pending'), 'info');
                        $('.assessment-form-container').remove();
                    });

                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== POLYGON STYLE FUNCTION ====================
            function polygonStyleFunction(feature) {
                let gisid = feature.get('gisid');
                let sqfeet = feature.get('sqfeet');
                let geometry = feature.getGeometry();
                let center;

                try {
                    center = geometry.getInteriorPoint();
                    if (!center) {
                        let extent = geometry.getExtent();
                        center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                    }
                } catch(e) {
                    let extent = geometry.getExtent();
                    center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                }

                let isVisible = feature.get('visible');
                if (isVisible === false) return null;

                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ff4444', width: 2 }),
                        fill: new ol.style.Fill({ color: 'rgba(255,68,68,0.15)' })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}\n${sqfeet || 0} sqft`,
                            font: 'bold 11px Arial',
                            fill: new ol.style.Fill({ color: '#fff' }),
                            stroke: new ol.style.Stroke({ color: '#000', width: 2 }),
                            backgroundFill: new ol.style.Fill({ color: 'rgba(0,0,0,0.7)' }),
                            padding: [4, 8, 4, 8]
                        })
                    })
                ];
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);

                let ps = new ol.source.Vector();
                $.each(polygons, function(i, p) {
                    try {
                        let c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (c && c.length) {
                            ps.addFeature(new ol.Feature({
                                geometry: new ol.geom.Polygon(c),
                                gisid: p.gisid,
                                sqfeet: p.sqfeet || "0",
                                visible: true
                            }));
                        }
                    } catch(e) {
                        console.error("Error parsing polygon:", e);
                    }
                });

                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFunction,
                    visible: true
                });

                let ls = new ol.source.Vector();
                $.each(lines, function(i, l) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (c && c.length) {
                            if (c.length === 1 && Array.isArray(c[0][0])) {
                                c = c[0];
                            }
                            ls.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(c),
                                gisid: l.gisid
                            }));
                        }
                    } catch(e) {
                        console.error("Error parsing line:", e);
                    }
                });

                lineLayer = new ol.layer.Vector({
                    source: ls,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ffc107', width: 3 })
                    }),
                    visible: true
                });

                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);

                map.on('click', function(e) {
                    let feature = map.forEachFeatureAtPixel(e.pixel, function(f) { return f; });
                    if (feature && feature.get('gisid')) {
                        showPopup(feature.get('gisid'), e.coordinate);
                    } else if (popupElement) {
                        $(popupElement).hide();
                    }
                });

                map.on('pointermove', function(e) {
                    let hasFeature = map.forEachFeatureAtPixel(e.pixel, function(f) { return f; });
                    $('#map').css('cursor', hasFeature ? 'pointer' : '');
                });

                showLoading(false);
            }

            // ==================== MAP INITIALIZATION ====================
            function initMap() {
                showLoading(true);

                osmLayer = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });

                satelliteLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    }),
                    visible: false
                });

                let droneImg = wardData.drone_image;
                let hasDrone = false;

                if (droneImg && droneImg !== 'null' && droneImg !== '') {
                    try {
                        let imageUrl = droneImg;
                        if (!imageUrl.startsWith('http') && !imageUrl.startsWith('//')) {
                            imageUrl = '/' + imageUrl.replace(/^\/+/, '');
                        }

                        imageLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: imageUrl,
                                imageExtent: [
                                    parseFloat(wardData.extent_left),
                                    parseFloat(wardData.extent_bottom),
                                    parseFloat(wardData.extent_right),
                                    parseFloat(wardData.extent_top)
                                ],
                                projection: 'EPSG:3857'
                            }),
                            visible: true,
                            opacity: 0.8
                        });
                        hasDrone = true;
                    } catch(e) {
                        console.error("Error loading drone image:", e);
                    }
                }

                let bound = wardData.boundary;
                let boundExt = null;

                if (bound && bound.length && bound[0].length) {
                    try {
                        let bc = bound[0].map(c => ol.proj.fromLonLat(c));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([bc])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({ color: '#ff0000', width: 3, lineDash: [10, 5] }),
                                fill: new ol.style.Fill({ color: 'rgba(255,0,0,0.05)' })
                            }),
                            visible: true
                        });
                        let lons = bound[0].map(p => p[0]);
                        let lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math.max(...lats)]);
                    } catch(e) {
                        console.error("Error parsing boundary:", e);
                    }
                }

                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                let zoom = 18;

                if (bound && bound[0] && bound[0].length) {
                    try {
                        let lons = bound[0].map(p => p[0]);
                        let lats = bound[0].map(p => p[1]);
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]);
                        zoom = 18;
                    } catch(e) {}
                }

                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: center,
                        zoom: zoom
                    })
                });

                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);

                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);

                setTimeout(() => {
                    if (boundExt) {
                        map.getView().fit(boundExt, { padding: [50, 50, 50, 50], duration: 1000 });
                    }
                }, 500);

                // Add panels to DOM
                $('body').append(`
                    <div class="layer-switcher panel" id="layerSwitcher">
                        <button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-layer-group"></i> Layers</h5>
                        <div class="layer-group">
                            <div class="group-title">Base Maps</div>
                            <label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label>
                            <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label>
                        </div>
                        <div class="layer-group">
                            <div class="group-title">Overlays</div>
                            <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
                            <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
                            <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>
                            ${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}
                        </div>
                    </div>
                `);

                $('body').append(`
                    <div class="map-legend panel" id="mapLegend">
                        <button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-info-circle"></i> Legend</h5>
                        <div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div>
                        <div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div>
                        <div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div>
                    </div>
                `);

                $('body').append(`
                    <div class="search-panel panel" id="searchPanel">
                        <button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-search"></i> Search Building</h5>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment...">
                            <button id="doSearchBtn"><i class="fas fa-search"></i> Go</button>
                        </div>
                        <div id="searchResults" class="search-results"></div>
                    </div>
                `);

                $('body').append(`
                    <div class="filter-panel panel" id="filterPanel">
                        <button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-filter"></i> Filter Buildings</h5>
                        <div class="filter-group">
                            <label>QC Status</label>
                            <select id="filterType">
                                <option value="all">All Buildings</option>
                                <option value="completed">QC Complete</option>
                                <option value="pending">QC Pending</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Min Floors</label>
                            <input type="number" id="filterMinFloors" placeholder="Min">
                        </div>
                        <div class="filter-group">
                            <label>Max Floors</label>
                            <input type="number" id="filterMaxFloors" placeholder="Max">
                        </div>
                        <div class="filter-actions">
                            <button class="apply-btn" id="applyFilterBtn">Apply</button>
                            <button class="reset-btn" id="resetFilterBtn">Reset</button>
                        </div>
                        <div class="filter-count" id="filterCount"></div>
                    </div>
                `);

                $('body').append(`
                    <div class="zoom-controls">
                        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                    </div>
                `);

                // Event listeners
                $('input[name="baseLayer"]').on('change', function() {
                    currentBaseLayer = $(this).val();
                    osmLayer.setVisible(currentBaseLayer === 'osm');
                    satelliteLayer.setVisible(currentBaseLayer === 'satellite');
                });

                $('#toggleBuildings').on('change', function() {
                    if (polygonLayer) polygonLayer.setVisible($(this).is(':checked'));
                });

                $('#toggleRoads').on('change', function() {
                    if (lineLayer) lineLayer.setVisible($(this).is(':checked'));
                });

                $('#toggleBoundary').on('change', function() {
                    if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked'));
                });

                if (hasDrone) {
                    $('#toggleDrone').on('change', function() {
                        if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                    });
                }

                $('#doSearchBtn').on('click', function() {
                    searchBuildings($('#searchInput').val());
                });

                $('#searchInput').on('keypress', function(e) {
                    if (e.which === 13) {
                        searchBuildings($(this).val());
                    }
                });

                $('#applyFilterBtn').on('click', function() {
                    let type = $('#filterType').val();
                    let minF = $('#filterMinFloors').val();
                    let maxF = $('#filterMaxFloors').val();
                    let src = polygonLayer.getSource();
                    let fts = src.getFeatures();
                    let cnt = 0;

                    $.each(fts, function(i, f) {
                        let g = f.get('gisid');
                        let b = polygonDatas.find(p => p.gisid == g);
                        let show = true;

                        if (type === 'completed' && b) {
                            let has = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        has = true;
                                        return false;
                                    }
                                });
                            }
                            if (!has) show = false;
                        } else if (type === 'pending' && b) {
                            let has = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        has = true;
                                        return false;
                                    }
                                });
                            }
                            if (has) show = false;
                        }

                        if (show && b && (minF || maxF)) {
                            let fl = parseInt(b.number_floor) || 0;
                            if (minF && fl < parseInt(minF)) show = false;
                            if (maxF && fl > parseInt(maxF)) show = false;
                        }

                        f.set('visible', show);
                        if (show) cnt++;
                    });

                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(`Showing ${cnt} of ${fts.length} buildings`);
                    closeAllPanels();
                });

                $('#resetFilterBtn').on('click', function() {
                    $('#filterType').val('all');
                    $('#filterMinFloors, #filterMaxFloors').val('');
                    let src = polygonLayer.getSource();
                    $.each(src.getFeatures(), function(i, f) {
                        f.set('visible', true);
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(`Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    closeAllPanels();
                });

                $('#zoomInBtn').on('click', function() {
                    map.getView().setZoom(map.getView().getZoom() + 1);
                });

                $('#zoomOutBtn').on('click', function() {
                    map.getView().setZoom(map.getView().getZoom() - 1);
                });

                // Button handlers
                $('#menuBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#layerSwitcher').addClass('open');
                });

                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#mapLegend').addClass('open');
                });

                $('#openSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#searchPanel').addClass('open');
                        setTimeout(() => $('#searchInput').focus(), 300);
                    }
                });

                $('#filterBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#filterPanel').addClass('open');
                });

                $('#locationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                        clearRoute();
                    } else {
                        startLocationTracking();
                    }
                });

                $('#routeBtn').on('click', function() {
                    if (selectedBuilding) {
                        getRouteToBuilding(selectedBuilding.gisid, selectedBuilding.coords);
                    } else {
                        showFlashMessage('Please search and select a building first', 'warning');
                        $('#openSearchBtn').click();
                    }
                });

                $('#closeRouteInfo').on('click', function() {
                    clearRoute();
                });

                $('#startNavigationBtn').on('click', function() {
                    if (currentRoute && currentRoute.endCoord) {
                        const [lon, lat] = currentRoute.endCoord;
                        window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}`, '_blank');
                    } else if (currentRoute && currentRoute.geometry && currentRoute.geometry.coordinates) {
                        const endCoords = currentRoute.geometry.coordinates.slice(-1)[0];
                        window.open(`https://www.google.com/maps/dir/?api=1&destination=${endCoords[1]},${endCoords[0]}`, '_blank');
                    } else {
                        showFlashMessage('No route available for navigation', 'warning');
                    }
                });

                // Close panels when clicking outside
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length &&
                        !$(e.target).closest('.action-btn').length &&
                        !$(e.target).closest('#centerMyLocationBtn').length) {
                        closeAllPanels();
                    }
                });

                refreshLayers();
            }

            // Start the application
            initMap();
            buildSearchIndex();

            $(window).on('resize', function() {
                setTimeout(() => {
                    if (map) map.updateSize();
                }, 100);
            });
        });
    </script>
@endsection
