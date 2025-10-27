<div class="row g-3">
    <div class="col-lg-8">
        <div class="jordan-map-container">
            <svg baseprofile="tiny" height="1000" stroke-linecap="round" stroke-linejoin="round" stroke-width=".5"
                version="1.2" viewbox="0 0 1000 1000" width="1000" xmlns="http://www.w3.org/2000/svg" id="jordan-map">

                <!-- Gradient Definitions -->
                <defs>
                    <linearGradient id="gradBlue" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#5E9FBF;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#88a4bc;stop-opacity:1" />
                    </linearGradient>
                    <linearGradient id="gradOrange" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#ff9933;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#ff7900;stop-opacity:1" />
                    </linearGradient>
                    <filter id="shadow">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.3" />
                    </filter>

                    <!-- Mafraq Background Pattern -->
                    <pattern id="mafraqPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/Mafraq.jpg') }}" width="1" height="1"
                            preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Irbid Background Pattern -->
                    <pattern id="irbidPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/irbid.jpg') }}" width="1" height="1"
                            preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Aqaba Background Pattern -->
                    <pattern id="aqabaPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/aqaba.jpg') }}" width="1" height="1"
                            preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Amman Background Pattern -->
                    <pattern id="ammanPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/amman.jpg') }}" width="1" height="1"
                            preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Karak Background Pattern -->
                    <pattern id="karakPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/karak.jpg') }}" width="1" height="1"
                            preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Ma'an Background Pattern -->
                    <pattern id="maanPattern" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <image href="{{ asset('assets/images/governorates/maan.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Tafilah Background Pattern -->
                    <pattern id="tafilahPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/tafilah.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Madaba Background Pattern -->
                    <pattern id="madabaPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/madaba.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Balqa Background Pattern -->
                    <pattern id="balqaPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/balqa.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Zarqa Background Pattern -->
                    <pattern id="zarqaPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/zarqa.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Jarash Background Pattern -->
                    <pattern id="jarashPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/jarash.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>

                    <!-- Ajloun Background Pattern -->
                    <pattern id="ajlounPattern" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <image href="{{ asset('assets/images/governorates/ajloun.jpg') }}" width="1"
                            height="1" preserveAspectRatio="xMidYMid slice" opacity="0.9" />
                        <rect width="1" height="1" fill="rgba(255, 121, 0, 0.25)" />
                    </pattern>
                </defs>

                <g id="features">
                    <!-- Irbid -->
                    <path class="governorate" data-code="IR" data-name="Irbid"
                        data-sites="{{ $data['IR']['sites'] ?? 0 }}" data-lands="{{ $data['IR']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['IR']['buildings'] ?? 0 }}"
                        d="M307.1 234.8l2.3 18-1.3 5.7-1.4 0.5-1.6-0.3-3.4-2.7-2.1-0.7-2.3 0.3-2.6 2.2-0.7 1.8 0.3 1.8 0.9 1.8 1 2.2 0.6 3.2-0.3 1.7-1.4 1.9-6.2-0.2-5.8-1.7-2.4 0.8-6.4 2.7-4-5.7-3.7-2.9-0.9-1.8-0.8-2.9-1.3-0.6-1.3 0.9-1.3 1.8-2.2 1.4-3 0.4-5.4-0.3-3.1 0.6-5.6 3.1-4.5 1.3-4.7-0.3-3.2 0.2-2.7 1.2-1.8 2.3-1.3 3.4-2.8 13.7 0.3 3.4 1.1 3.5 2.6 6 0.5 1.9-0.2 1.3-0.8 2.2-12.9-1.6-1.2 0.1 2.4-10.3-2.5 0 0.3-1.3 0.5-0.8 0.8-0.6 0.9-0.5-1.5-2.6-0.7-4.1 0.1-4.2 0.8-2.6-1.7-3.3 0.2-6.5-1-2.2 0-1.5 1.3 0 0 1.5 1.2 0-0.3-2-0.6-1.7-0.7-1.3-0.9-1-0.5-0.7-0.8-2.4 1.8-0.4 0.8-0.6-0.2-0.9-1.1-1.2 0-1.3 1.3 0-0.3-1-0.3-0.2-0.3 0.1-0.4-0.4 2.5-1.7-1.2-1.5 1.3-0.5 1.2-0.8 0-1.7-0.9-0.1-0.5-0.3-0.4-0.4-0.7-0.5 0.4-1.5 0.7-1.5 1.1-1.2 1.7-0.5 0-1.4-1.8-1.8-0.4-0.9-1.3-2-0.8 0-0.9 0.1 0-1.5 2.5 0-0.5-1.4-0.7-4.6 1.2 1.5 0.8-2.4 0.5-0.8 0.2 0.9 0.1 0.5 0.3-0.2 0.8-1.2-1.1-2.7-0.6-5.7-1-1.9 0-1.7 1.3 0 0-1.4-1.6-0.9-0.7-1.6 0-1.8 0.5-0.7 1.3-0.6 1.6-1.5 2.8-3.7 3.4-2.5 0.1 0 4.4 0.6 2.9-1.6 6.2-5.5 10.2-6.5 3.2-0.8 1.2-0.6 1.1-0.2 1 0.1 0.7 0.6 0 0.1 1.7 2.2 20 4.6 1.8 1.1 3.2 3.2 0.9 0.3 2.4 0 0.7 0.4 0.3 1.5-0.3 1.4-0.6 0.9-0.6-0.1 1.6 2.1 1.7 1.6 1.9 0.6 2.7-0.5 4.3 0.4 0.9 2.5-0.6 3.7 0.2 4.2 1.7 3.7 8.4 12.7 1 2.6 0.1 0.9 0.6 0.2 2.3 0.1z">
                    </path>

                    <!-- Madaba -->
                    <path class="governorate" data-code="MA" data-name="Madaba"
                        data-sites="{{ $data['MA']['sites'] ?? 0 }}" data-lands="{{ $data['MA']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['MA']['buildings'] ?? 0 }}"
                        d="M191 471.8l0.3-12.6-0.1-0.1 0.1 0.1 0.1-0.1 0-0.2-0.1-0.3 1.1-16.2 2.9-15.8 4.2-9.7 2.2 0.3 12.8 0.7 2.6-1.2 2.3-2.3 3.1-5.5 2.2-5.9 1.1-1.5 2.2-1.5 2.3-0.7 5-0.4 2.4-1.5 7-1.8 9.4 0 2.8-0.7 2.3-1 1.3 0.2 1.3 1.3 0.4 15.8-0.4 2-1.1 2-3.2 3.9-0.9 1.6-0.2 1.9 0.3 2.2 1.4 5.6 1.4 3.4 8 8.7 1.2 2.8-0.2 2.1-0.6 1.5 0.2 1.8 1.1 1.3 1.4 1.5 0.4 1.3-0.6 1.4-2.6 3.7-1.2 2.4-3.2 9.8-0.8 1.4-1.4 1.5-1.1 0.9-2 1.9-8.3-1.9-4-0.1-6.3 0.7-5.7 1.8-2.6 0-3.5-0.7-18.6-6.3-14.1-0.5-4 1z">
                    </path>

                    <!-- Karak -->
                    <path class="governorate" data-code="KA" data-name="Karak"
                        data-sites="{{ $data['KA']['sites'] ?? 0 }}" data-lands="{{ $data['KA']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['KA']['buildings'] ?? 0 }}"
                        d="M170.6 582.7l5-4.8 2.1-4 0-6.8 1.2-6.3 8.7-17.3 0.9-6.2-1.3-5.9-2.8-5.4-2-4.4-1.8-5.6-1-5.9 2.4-5.3 2.6-4.6 0.3-4.7-1.4-1.5 3.4-6.3 3.3-8.8 0.8-4.9 0-2.2 4-1 14.1 0.5 18.6 6.3 3.5 0.7 2.6 0 5.7-1.8 6.3-0.7 4 0.1 8.3 1.9 4.5 8 1.2 4.2 0.5 2.8-0.3 2-1.5 4.3-0.7 3.1-0.5 4.2 0.7 2.6 1.8 1.7 56.3 1.3 3.1 21.3 4.3 12.1-9.5 30.3-21.7 40.7-8-8-7.8-5.9-1.5-1.6-1.9-3.3-1.5-1.6-1.9-1.2-6.8-3.1-2.3-1.7-1.7-1.6-0.8-1.6-0.5-1.3-1.3-1.1-29.7-11.9-15.9-0.8-2.6-1-2.6-1.7-2.1-1.9-2.1-0.6-2.4-0.2-3.3 0.5-2.3-0.1-1.8 0.6-1.1 1.7-0.8 4.4-1.9 5.6-1.2 8.8-1.2 2.1-2.1 1.3-3.3-0.3-2.6-0.7-9.3-6.5-1.4-1z">
                    </path>

                    <!-- Tafilah -->
                    <path class="governorate" data-code="TF" data-name="Tafilah"
                        data-sites="{{ $data['TF']['sites'] ?? 0 }}" data-lands="{{ $data['TF']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['TF']['buildings'] ?? 0 }}"
                        d="M155.1 626.6l1.5-5.2 0.8-5.4 0.7-2.5 1.2-2.6 1.4-1.7 3.2-2.8 1-2.1 0.7-4.8-0.1-4.8 0.5-4.9 2.3-4.9 2.3-2.2 1.4 1 9.3 6.5 2.6 0.7 3.3 0.3 2.1-1.3 1.2-2.1 1.2-8.8 1.9-5.6 0.8-4.4 1.1-1.7 1.8-0.6 2.3 0.1 3.3-0.5 2.4 0.2 2.1 0.6 2.1 1.9 2.6 1.7 2.6 1 15.9 0.8 29.7 11.9 1.3 1.1 0.5 1.3 0.8 1.6 1.7 1.6 2.3 1.7 6.8 3.1 1.9 1.2 1.5 1.6 1.9 3.3 1.5 1.6 7.8 5.9 8 8-14.1 23.9-6.8 2.5-5-1.9-2-0.2-2 0.4-2.3 1.6-1.8 1.9-2.6 1.6-13.2 3.2-17.3 2.3-6.4-0.5-18.4 0.6-7.6-1.2-7.4 0-4.3-0.2-2.1-0.7-2.6-1.3-2.3-3-2.2-3.9-2.4-2.2-2.9-1.5-12.7-9.1-2.8-2.1z">
                    </path>

                    <!-- Aqaba -->
                    <path class="governorate" data-code="AQ" data-name="Aqaba"
                        data-sites="{{ $data['AQ']['sites'] ?? 0 }}" data-lands="{{ $data['AQ']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['AQ']['buildings'] ?? 0 }}"
                        d="M155.1 626.6l2.8 2.1 12.7 9.1 2.9 1.5 2.4 2.2 2.2 3.9 2.3 3 2.6 1.3 2.1 0.7 4.3 0.2-1.1 13.5-3.8 17.1-8.3 26.2 0.4 24.2-1.5 6.3-10.8 21.5-2.2 10.7-0.4 5.6 0.9 4 0.9 2.1 1.5 2.5 1.7 2.2 2.2 2.2 2.6 1.5 3 1 3 0.1 2.8-0.4 2.4-0.8 2.4-0.5 4.7-0.2 5.6 1.1 3.4 1.3 3.7 2.1 3.6 3.1 3.3 3.6 2.8 4.3 9.7 22.2 12.1 49.2 9.2 67.5 0.4 1.8 0 0.1-21.9-3.9-27.6-4.7-25.8-4.5-28.9-5-22.1-3.9-20.6-3.6 2.5-1.7 1.2-19.4 1.3-5.6 4-8.7-0.1-3.4-3.9-3.9-2.5 0-0.2-0.7 0-0.6-0.2-0.3-0.9 0.1 0.8-5.8 1.3-4.7 2.5-4 1.8-5.4 0.9-12 1.4-5.3 8.6-23.4 0.8-4.3 0.2-13.1 1.4-7.2 0.7-1.9 0.9-1.6 0.7-1.9 0.1-2.6 2.2-8.5 7.9-11.9 3-7.2 0-6.8-3.8-13.2 0.1-6.1 1.3-3.7 1.8-11.2 1.1-2.7 1.2-2.2 1-2.3 0.5-3-0.6-3.1-2.6-4.3-1-2.3 0-5.1 3.3-8.8 0.9-5.1 8-26.4 10.8-22.2z">
                    </path>

                    <!-- Balqa -->
                    <path class="governorate" data-code="BA" data-name="Balqa"
                        data-sites="{{ $data['BA']['sites'] ?? 0 }}" data-lands="{{ $data['BA']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['BA']['buildings'] ?? 0 }}"
                        d="M199.5 416.9l4.6-10.8 5.9-6.6-3.9-11.8 0-1.6 2-2.8-4.5-17.5 0.5-1.7 1.2-0.7 1.2-0.4 0.9-1 0.1-1.7-1.1-2.4-0.3-1.9-0.1-2.9-0.4-2.4-2-5.1-0.4-10.1 1.1-3.8 3.1-2.6-2-2.7 0.1-2.5 3.2-5.4-1-1.4 0-1.2 0.8-1 1.5-0.7 0-1.7-0.8-2.6 0.9-3.5 1.2-0.1 12.9 1.6 3 3.7 3.1 1.3 4.9 1 0.2 1.9 1.9 0.7 2.5 0.2 16.2-0.4 3.6 0.7 2.6 1.1 2.1 2.1 1.4 1.9 6.8 5.1 1.1 5.6 0.9 1.2 1.5 1.4 1.3 0.6 3.6 2.1-5.4 1.8-5.8 5.7-2.9 1.7-7.1 2.1-2.8 2.3-8.3 10-4.7 3.3-10.8 5.6-2.8 2.5-1.3 2.4 0.4 2 1.3 1.6 2 1 2.5 0.7 4.2 0.7 1.1 1.2 0.3 1.8-2.3 6.7-0.5 2.3-0.3 3.9-2.4 1.5-5 0.4-2.3 0.7-2.2 1.5-1.1 1.5-2.2 5.9-3.1 5.5-2.3 2.3-2.6 1.2-12.8-0.7-2.2-0.3z">
                    </path>

                    <!-- Mafraq -->
                    <path class="governorate" data-code="MF" data-name="Mafraq"
                        data-sites="{{ $data['MF']['sites'] ?? 0 }}" data-lands="{{ $data['MF']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['MF']['buildings'] ?? 0 }}"
                        d="M295.1 272.2l1.4-1.9 0.3-1.7-0.6-3.2-1-2.2-0.9-1.8-0.3-1.8 0.7-1.8 2.6-2.2 2.3-0.3 2.1 0.7 3.4 2.7 1.6 0.3 1.4-0.5 1.3-5.7-2.3-18 2.6 0.1 6.9-1 1.2 0.1 1.9 0.8 1.1 0.2 0.9-0.4 2.1-2 1-0.3 2 1.1 6.1 6.1 11.9 8.3 16.4 15.5 2.7 1.6 3.7 1.1 10.4 1.1 3 1.9 6.8 0.8 25.4 3.1 6.7 5.1 3.3-1.9 4 0.1 11.9 3.2 2.6 0.1 2.3-0.8 29.9-20.5 28.4-18.6 0.1 0 20.5-13.2 31.8-20.5 14.7-9.4 17.1-11 31.9-20.5 31.8-20.5 23.6-15.3 32.4-20.4 15.7-9.9 39.8-25.1 45.5-28.3 8.6 31.7 7.7 28.4 6.4 23.6 8.5 31.4 8.8 32.4 12.4 46.1-14.3 4.5-0.2 0.1-0.1 0.2 0.1 0.2 0.2 0.4 9.1 31.6 1.4 3.3 1.8 1.1 35.2-9.8 3.8 2.3 2.8 6.7 3.9 14.8-4.8 7-22.3 19.1 0 1.7-1.9 0.6-3.6 2.7-22 21-6.5 2.8-21.1 6.1-41.7 12.2-41.8 12.1-41.7 12.1-35.1 10.2-6.7 1.9-35.1 10.6-0.1-0.1-0.3-0.1-230.5-80.1-6.6-3.9-5.9-5.5-3.2-1.8-10.1-1-3.4 0.3-2.4 1.1-2 1.5-2.3 1.1-2.8 0.1-2.9-0.9-3.3-2.4-7.4-3.7-7.6-0.7-2.3 0.3-1.9 0.5-3.9 1.5-2.5 0.3-2.9-0.8-4.5-3.5-2.3-1.4-1.9-0.3-2.5 2.6-1.9 1-2.2 0.5-2-0.4-1.9-1.3-0.9-1.5-0.1-2.1 0.5-1.6 0.8-1.3 0.3-1.5-0.6-1.5-1.6-1.7-3.8-1.3-1.9-4.5-2.4-4.1-1.3-1.1-1.5-1-0.9-1.5 0.1-2.2 1.8-3.4 8.4-9.6 2.7-5.4z">
                    </path>

                    <!-- Ma'an -->
                    <path class="governorate" data-code="MN" data-name="Ma'an"
                        data-sites="{{ $data['MN']['sites'] ?? 0 }}" data-lands="{{ $data['MN']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['MN']['buildings'] ?? 0 }}"
                        d="M296.3 616.4l21.7-40.7 9.5-30.3-4.3-12.1-3.1-21.3 198.7 0.3 23.8 25.9 24.1 26.2 0.6 0.7 0.6 0.7 0.6 0.7 0.6 0.7 20.3 24.1 20.4 24 20.4 24 20.4 24 9.2 10.8 0 0.2 0 0.1-0.2 0-1.2 0.8-13.6 7.6-22.5 12.7-20.2 11.4-4.3 3.6-2.4 3.8-5.4 13.4-6.7 16.3-6.1 14.9-8.3 20.2-4 3.6-21.9 4.5-24.8 5.2-26.5 5.6-26.8 5.6-16.5 3.4-16.1 3.3-5.1 2.6-4.4 4.8-10.3 17.5-8.5 14.5-11.6 19.7-11.9 20.2-14.4 11.9-21.5 17.9-19.7 16.3-20.1 16.7-4.8 1.9-5.1 0.2-19.3-3.3-21.4-3.7-10.6-1.8 0-0.1-0.4-1.8-9.2-67.5-12.1-49.2-9.7-22.2-2.8-4.3-3.3-3.6-3.6-3.1-3.7-2.1-3.4-1.3-5.6-1.1-4.7 0.2-2.4 0.5-2.4 0.8-2.8 0.4-3-0.1-3-1-2.6-1.5-2.2-2.2-1.7-2.2-1.5-2.5-0.9-2.1-0.9-4 0.4-5.6 2.2-10.7 10.8-21.5 1.5-6.3-0.4-24.2 8.3-26.2 3.8-17.1 1.1-13.5 7.4 0 7.6 1.2 18.4-0.6 6.4 0.5 17.3-2.3 13.2-3.2 2.6-1.6 1.8-1.9 2.3-1.6 2-0.4 2 0.2 5 1.9 6.8-2.5 14.1-23.9z">
                    </path>

                    <!-- Amman -->
                    <path class="governorate" data-code="AM" data-name="Amman"
                        data-sites="{{ $data['AM']['sites'] ?? 0 }}" data-lands="{{ $data['AM']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['AM']['buildings'] ?? 0 }}"
                        d="M258.1 477.8l2-1.9 1.1-0.9 1.4-1.5 0.8-1.4 3.2-9.8 1.2-2.4 2.6-3.7 0.6-1.4-0.4-1.3-1.4-1.5-1.1-1.3-0.2-1.8 0.6-1.5 0.2-2.1-1.2-2.8-8-8.7-1.4-3.4-1.4-5.6-0.3-2.2 0.2-1.9 0.9-1.6 3.2-3.9 1.1-2 0.4-2-0.4-15.8-1.3-1.3-1.3-0.2-2.3 1-2.8 0.7-9.4 0-7 1.8 0.3-3.9 0.5-2.3 2.3-6.7-0.3-1.8-1.1-1.2-4.2-0.7-2.5-0.7-2-1-1.3-1.6-0.4-2 1.3-2.4 2.8-2.5 10.8-5.6 4.7-3.3 8.3-10 2.8-2.3 7.1-2.1 2.9-1.7 5.8-5.7 5.4-1.8 4.3 5.6 4.5 4.2 6.2 7.6 3.6 2.6 2.5 0.8 3.6-2.1 2.3-0.5 3-0.2 9.1-3 12 0.1 3.8-0.7 2.4-1.5 3.5-4.3 2.2-1.9 4-0.9 19.6 1.7 3.9 1.8 3.9 3.2 10.5 11.6 5.3 7 0.4 3.5-1.7 2.6-5.3 3.3-4.1 3.4-1.4 2-0.3 2 1.2 2.4 5 5.8 8.3 7.7 8.1 5.6 4.3 1.6 3.5-0.1 8.8-5.4 3.2-0.9 3.4 0.8 52 53.3 0.6 0.3 0.1 0.1-7.1 2.1 24.1 26.3 24.2 26.3 0.4 0.4-198.7-0.3-56.3-1.3-1.8-1.7-0.7-2.6 0.5-4.2 0.7-3.1 1.5-4.3 0.3-2-0.5-2.8-1.2-4.2-4.5-8z">
                    </path>

                    <!-- Zarqa -->
                    <path class="governorate" data-code="ZA" data-name="Zarqa"
                        data-sites="{{ $data['ZA']['sites'] ?? 0 }}" data-lands="{{ $data['ZA']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['ZA']['buildings'] ?? 0 }}"
                        d="M280.9 338.1l-3.6-2.1-1.3-0.6-1.5-1.4-0.9-1.2-1.1-5.6 7-9.4 2.1-1.7 3-1.7 2.4-1.2 1.6-1.6 0.9-1.9 0.6-4.7 3.8 1.3 1.6 1.7 0.6 1.5-0.3 1.5-0.8 1.3-0.5 1.6 0.1 2.1 0.9 1.5 1.9 1.3 2 0.4 2.2-0.5 1.9-1 2.5-2.6 1.9 0.3 2.3 1.4 4.5 3.5 2.9 0.8 2.5-0.3 3.9-1.5 1.9-0.5 2.3-0.3 7.6 0.7 7.4 3.7 3.3 2.4 2.9 0.9 2.8-0.1 2.3-1.1 2-1.5 2.4-1.1 3.4-0.3 10.1 1 3.2 1.8 5.9 5.5 6.6 3.9 230.5 80.1 0.3 0.1 0.1 0.1-10.9 3.3-46 13.8-46 13.8-38.9 11.7-0.1-0.1-0.6-0.3-52-53.3-3.4-0.8-3.2 0.9-8.8 5.4-3.5 0.1-4.3-1.6-8.1-5.6-8.3-7.7-5-5.8-1.2-2.4 0.3-2 1.4-2 4.1-3.4 5.3-3.3 1.7-2.6-0.4-3.5-5.3-7-10.5-11.6-3.9-3.2-3.9-1.8-19.6-1.7-4 0.9-2.2 1.9-3.5 4.3-2.4 1.5-3.8 0.7-12-0.1-9.1 3-3 0.2-2.3 0.5-3.6 2.1-2.5-0.8-3.6-2.6-6.2-7.6-4.5-4.2-4.3-5.6z">
                    </path>

                    <!-- Ajloun -->
                    <path class="governorate" data-code="AJ" data-name="Ajloun"
                        data-sites="{{ $data['AJ']['sites'] ?? 0 }}" data-lands="{{ $data['AJ']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['AJ']['buildings'] ?? 0 }}"
                        d="M224.2 307.9l0.8-2.2 0.2-1.3-0.5-1.9-2.6-6-1.1-3.5-0.3-3.4 2.8-13.7 1.3-3.4 1.8-2.3 2.7-1.2 3.2-0.2 4.7 0.3 4.5-1.3 5.6-3.1 3.1-0.6 5.4 0.3 3-0.4 2.2-1.4 1.3-1.8 1.3-0.9 1.3 0.6 0.8 2.9 0.9 1.8 3.7 2.9 4 5.7-4 2.2-2 0.8-2.9 2-2.9 2.9-3.1 5-1.3 3.6-0.6 3.1 0.2 2-0.4 2-1.7 1.9-12.7 5.8-7.7 8.8-4.9-1-3.1-1.3-3-3.7z">
                    </path>

                    <!-- Jarash -->
                    <path class="governorate" data-code="JA" data-name="Jarash"
                        data-sites="{{ $data['JA']['sites'] ?? 0 }}" data-lands="{{ $data['JA']['lands'] ?? 0 }}"
                        data-buildings="{{ $data['JA']['buildings'] ?? 0 }}"
                        d="M274.3 273.8l6.4-2.7 2.4-0.8 5.8 1.7 6.2 0.2-2.7 5.4-8.4 9.6-1.8 3.4-0.1 2.2 0.9 1.5 1.5 1 1.3 1.1 2.4 4.1 1.9 4.5-0.6 4.7-0.9 1.9-1.6 1.6-2.4 1.2-3 1.7-2.1 1.7-7 9.4-6.8-5.1-1.4-1.9-2.1-2.1-2.6-1.1-3.6-0.7-16.2 0.4-2.5-0.2-1.9-0.7-0.2-1.9 7.7-8.8 12.7-5.8 1.7-1.9 0.4-2-0.2-2 0.6-3.1 1.3-3.6 3.1-5 2.9-2.9 2.9-2 2-0.8 4-2.2z">
                    </path>
                </g>

                <!-- Text Labels for Governorates -->
                <g id="labels">
                    <text class="governorate-label" data-code="IR" x="252" y="225">Irbid</text>
                    <text class="governorate-count" data-code="IR" x="252" y="240"></text>

                    <text class="governorate-label" data-code="MA" x="232" y="444">Madaba</text>
                    <text class="governorate-count" data-code="MA" x="232" y="459"></text>

                    <text class="governorate-label" data-code="KA" x="238" y="535">Karak</text>
                    <text class="governorate-count" data-code="KA" x="238" y="550"></text>

                    <text class="governorate-label" data-code="TF" x="223" y="612">Tafilah</text>
                    <text class="governorate-count" data-code="TF" x="223" y="627"></text>

                    <text class="governorate-label" data-code="AQ" x="167" y="860">Aqaba</text>
                    <text class="governorate-count" data-code="AQ" x="167" y="875"></text>

                    <text class="governorate-label" data-code="BA" x="230" y="344">Balqa</text>
                    <text class="governorate-count" data-code="BA" x="230" y="359"></text>

                    <text class="governorate-label" data-code="MF" x="670" y="266">Mafraq</text>
                    <text class="governorate-count" data-code="MF" x="670" y="281"></text>

                    <text class="governorate-label" data-code="MN" x="416" y="671">Ma'an</text>
                    <text class="governorate-count" data-code="MN" x="416" y="686"></text>

                    <text class="governorate-label" data-code="AM" x="353" y="448">Amman</text>
                    <text class="governorate-count" data-code="AM" x="353" y="463"></text>

                    <text class="governorate-label" data-code="ZA" x="489" y="412">Zarqa</text>
                    <text class="governorate-count" data-code="ZA" x="489" y="427"></text>

                    <text class="governorate-label" data-code="AJ" x="243" y="286">Ajloun</text>
                    <text class="governorate-count" data-code="AJ" x="243" y="301"></text>

                    <text class="governorate-label" data-code="JA" x="272" y="304">Jarash</text>
                    <text class="governorate-count" data-code="JA" x="272" y="319"></text>
                </g>
            </svg>

            <div class="tooltip" id="map-tooltip"></div>

            <div class="distribution-toggle">
                <button class="dist-btn active" data-type="buildings" id="btn-buildings"
                    title="Buildings Distribution">
                    <i class="bi bi-building"></i>
                    <span class="dist-label">Build</span>
                </button>
                <button class="dist-btn" data-type="sites" id="btn-sites" title="Sites Distribution">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span class="dist-label">Sites</span>
                </button>
                <button class="dist-btn" data-type="lands" id="btn-lands" title="Lands Distribution">
                    <i class="bi bi-map"></i>
                    <span class="dist-label">Lands</span>
                </button>
            </div>

            <div class="orange-watermark">
                <img src="{{ asset('assets/images/orange-logo.svg') }}" alt="Orange Logo">
            </div>

            <div class="map-legend-overlay">
                <p class="mb-1 small text-muted">
                    <i class="bi bi-cursor-fill me-1"></i>
                    Hover over any governorate for details
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="map-stats-panel">
            <h6 class="fw-bold mb-3 text-dark">
                <i class="bi bi-graph-up text-orange me-2"></i>Overview by Region
            </h6>

            @php
                $regions = [
                    1 => ['name' => 'Capital', 'governorates' => ['AM'], 'color' => '#ff7900'],
                    2 => ['name' => 'North', 'governorates' => ['IR', 'MF', 'AJ', 'JA'], 'color' => '#3b82f6'],
                    3 => ['name' => 'Middle', 'governorates' => ['BA', 'ZA', 'MA'], 'color' => '#22c55e'],
                    4 => ['name' => 'South', 'governorates' => ['AQ', 'KA', 'TF', 'MN'], 'color' => '#8b5cf6'],
                ];

                $regionStats = [];
                foreach ($regions as $regionId => $regionInfo) {
                    $sites = 0;
                    $lands = 0;
                    $buildings = 0;
                    foreach ($regionInfo['governorates'] as $gov) {
                        $sites += $data[$gov]['sites'] ?? 0;
                        $lands += $data[$gov]['lands'] ?? 0;
                        $buildings += $data[$gov]['buildings'] ?? 0;
                    }
                    $regionStats[$regionId] = [
                        'name' => $regionInfo['name'],
                        'color' => $regionInfo['color'],
                        'sites' => $sites,
                        'lands' => $lands,
                        'buildings' => $buildings,
                        'total' => $sites + $lands + $buildings,
                    ];
                }
            @endphp

            @foreach ($regionStats as $regionId => $stat)
                <div class="region-stat-card mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="region-color-dot" style="background: {{ $stat['color'] }};"></div>
                            <h6 class="mb-0 fw-bold small">{{ $stat['name'] }}</h6>
                        </div>
                        <span class="badge bg-light text-dark">{{ $stat['total'] }}</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <i class="bi bi-geo-alt-fill text-orange"></i>
                                <div class="stat-value">{{ $stat['sites'] }}</div>
                                <div class="stat-label">Sites</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <i class="bi bi-map text-success"></i>
                                <div class="stat-value">{{ $stat['lands'] }}</div>
                                <div class="stat-label">Lands</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <i class="bi bi-building text-primary"></i>
                                <div class="stat-value">{{ $stat['buildings'] }}</div>
                                <div class="stat-label">Buildings</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .jordan-map-container {
        position: relative;
        width: 100%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 10px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    #jordan-map {
        width: 100%;
        height: auto;
        max-height: 600px;
    }

    .map-stats-panel {
        background: white;
        border-radius: 12px;
        padding: 15px;
        height: 100%;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .region-stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .region-stat-card:hover {
        box-shadow: 0 4px 12px rgba(255, 121, 0, 0.1);
        border-color: #ff7900;
        transform: translateY(-2px);
    }

    .region-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }

    .stat-mini-card {
        background: white;
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .stat-mini-card:hover {
        background: #f8f9fa;
        border-color: #ff7900;
    }

    .stat-mini-card i {
        font-size: 16px;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: #212529;
        margin: 2px 0;
    }

    .stat-label {
        font-size: 10px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .map-legend-overlay {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 121, 0, 0.3);
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .map-legend-overlay p {
        margin: 0;
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    .map-legend-overlay i {
        color: #ff7900;
    }

    .orange-watermark {
        position: absolute;
        bottom: 15px;
        left: 15px;
        z-index: 5;
        pointer-events: none;
        opacity: 0.2;
        transition: opacity 0.3s ease;
    }

    .jordan-map-container:hover .orange-watermark {
        opacity: 0.6;
    }

    .orange-watermark img {
        width: 80px;
        height: auto;
        filter: drop-shadow(1px 1px 2px rgba(255, 255, 255, 0.8));
    }

    .distribution-toggle {
        position: absolute;
        bottom: 60px;
        right: 15px;
        display: flex;
        gap: 6px;
        z-index: 10;
    }

    .dist-btn {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 2px solid #e9ecef;
        border-radius: 6px;
        padding: 6px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        min-width: 55px;
    }

    .dist-btn:hover {
        border-color: #ff7900;
        box-shadow: 0 4px 12px rgba(255, 121, 0, 0.2);
        transform: translateY(-2px);
    }

    .dist-btn.active {
        background: linear-gradient(135deg, #ff9933, #ff7900);
        border-color: #ff7900;
        box-shadow: 0 4px 12px rgba(255, 121, 0, 0.4);
    }

    .dist-btn i {
        font-size: 14px;
        color: #6c757d;
    }

    .dist-btn.active i {
        color: white;
    }

    .dist-label {
        font-size: 9px;
        font-weight: 600;
        color: #212529;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        line-height: 1;
    }

    .dist-btn.active .dist-label {
        color: white;
    }

    .governorate-count {
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: bold;
        fill: #000000 !important;
        text-anchor: middle;
        pointer-events: none;
        text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.9);
    }

    .governorate {
        stroke: #ffffff !important;
        stroke-width: 2 !important;
        stroke-linejoin: round !important;
        stroke-linecap: round !important;
        transition: all 0.3s ease;
        cursor: pointer;
        filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.15));
        opacity: 0.9;
    }

    .governorate:hover {
        fill: url(#gradOrange) !important;
        stroke: #000000 !important;
        stroke-width: 3 !important;
        filter: drop-shadow(0 4px 8px rgba(255, 121, 0, 0.4));
        opacity: 1;
        transform: scale(1.02);
        transform-origin: center;
    }

    /* Mafraq Background Image on Hover */
    .governorate[data-code="MF"]:hover {
        fill: url(#mafraqPattern) !important;
    }

    /* Irbid Background Image on Hover */
    .governorate[data-code="IR"]:hover {
        fill: url(#irbidPattern) !important;
    }

    /* Aqaba Background Image on Hover */
    .governorate[data-code="AQ"]:hover {
        fill: url(#aqabaPattern) !important;
    }

    /* Amman Background Image on Hover */
    .governorate[data-code="AM"]:hover {
        fill: url(#ammanPattern) !important;
    }

    /* Karak Background Image on Hover */
    .governorate[data-code="KA"]:hover {
        fill: url(#karakPattern) !important;
    }

    /* Ma'an Background Image on Hover */
    .governorate[data-code="MN"]:hover {
        fill: url(#maanPattern) !important;
    }

    /* Tafilah Background Image on Hover */
    .governorate[data-code="TF"]:hover {
        fill: url(#tafilahPattern) !important;
    }

    /* Madaba Background Image on Hover */
    .governorate[data-code="MA"]:hover {
        fill: url(#madabaPattern) !important;
    }

    /* Balqa Background Image on Hover */
    .governorate[data-code="BA"]:hover {
        fill: url(#balqaPattern) !important;
    }

    /* Zarqa Background Image on Hover */
    .governorate[data-code="ZA"]:hover {
        fill: url(#zarqaPattern) !important;
    }

    /* Jarash Background Image on Hover */
    .governorate[data-code="JA"]:hover {
        fill: url(#jarashPattern) !important;
    }

    /* Ajloun Background Image on Hover */
    .governorate[data-code="AJ"]:hover {
        fill: url(#ajlounPattern) !important;
    }

    .governorate-label {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: bold;
        fill: white !important;
        text-anchor: middle;
        pointer-events: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.8));
    }

    .tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1000;
        max-width: 220px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .tooltip.visible {
        opacity: 1;
    }

    .tooltip strong {
        color: #ff7900;
        font-size: 14px;
        display: block;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(255, 121, 0, 0.3);
        padding-bottom: 4px;
    }

    .tooltip-row {
        display: flex;
        justify-content: space-between;
        margin: 4px 0;
    }

    .tooltip-label {
        color: #ccc;
    }

    .tooltip-value {
        font-weight: bold;
        color: #fff;
    }

    @media (max-width: 768px) {
        .governorate-label {
            font-size: 11px;
        }

        .jordan-map-container {
            padding: 10px;
        }

        .tooltip {
            font-size: 12px;
            padding: 10px 12px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const governorates = document.querySelectorAll('.governorate');
        const tooltip = document.getElementById('map-tooltip');
        let currentDistribution = 'buildings'; // Default active

        // Calculate color based on count
        function getColorByCount(count, maxCount) {
            const colors = [
                '#FFE5CC', // Very light orange
                '#FFCC99', // Light orange
                '#FFB366', // Medium orange
                '#FF9933', // Dark orange
                '#FF7900', // Very dark orange (main brand color)
            ];

            if (count === 0) return '#f0f0f0';

            const percentage = count / maxCount;
            if (percentage <= 0.2) return colors[0];
            if (percentage <= 0.4) return colors[1];
            if (percentage <= 0.6) return colors[2];
            if (percentage <= 0.8) return colors[3];
            return colors[4];
        }

        // Apply colors based on distribution type
        function applyDistribution(type) {
            currentDistribution = type;

            // Find max for the selected type
            let maxCount = 0;
            governorates.forEach(gov => {
                const count = parseInt(gov.getAttribute(`data-${type}`)) || 0;
                if (count > maxCount) maxCount = count;
            });

            // Apply colors and update counts
            governorates.forEach(gov => {
                const govCode = gov.getAttribute('data-code');
                const count = parseInt(gov.getAttribute(`data-${type}`)) || 0;
                const color = getColorByCount(count, maxCount);
                gov.style.fill = color;

                // Update the count text element
                const countElement = document.querySelector(
                    `.governorate-count[data-code="${govCode}"]`);
                if (countElement) {
                    countElement.textContent = count;
                }
            });
        }

        // Initial distribution
        applyDistribution('buildings');

        // Distribution toggle buttons
        const distButtons = document.querySelectorAll('.dist-btn');
        distButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.getAttribute('data-type');

                // Update active state
                distButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Apply new distribution
                applyDistribution(type);
            });
        });

        governorates.forEach(gov => {
            gov.addEventListener('mouseenter', function(e) {
                this.parentNode.appendChild(this);
                showTooltip(e, this);
            });

            gov.addEventListener('mouseleave', function() {
                hideTooltip();
            });

            gov.addEventListener('mousemove', function(e) {
                moveTooltip(e);
            });

            gov.addEventListener('click', function() {
                this.parentNode.appendChild(this);
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                // You can add navigation or other actions here
                console.log(`Clicked: ${name} (${code})`);
            });
        });

        function showTooltip(e, element) {
            const name = element.getAttribute('data-name');
            const sites = element.getAttribute('data-sites');
            const lands = element.getAttribute('data-lands');
            const buildings = element.getAttribute('data-buildings');

            tooltip.innerHTML = `
                <strong>${name}</strong>
                <div class="tooltip-row">
                    <span class="tooltip-label"><i class="bi bi-geo-alt-fill"></i> Sites:</span>
                    <span class="tooltip-value">${sites}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label"><i class="bi bi-map"></i> Lands:</span>
                    <span class="tooltip-value">${lands}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label"><i class="bi bi-building"></i> Buildings:</span>
                    <span class="tooltip-value">${buildings}</span>
                </div>
            `;

            tooltip.classList.add('visible');
            moveTooltip(e);
        }

        function hideTooltip() {
            tooltip.classList.remove('visible');
        }

        function moveTooltip(e) {
            const container = document.querySelector('.jordan-map-container');
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            tooltip.style.left = (x + 15) + 'px';
            tooltip.style.top = (y - 10) + 'px';
        }

        // Animation on load
        governorates.forEach((gov, index) => {
            setTimeout(() => {
                gov.style.opacity = '0';
                gov.style.transform = 'scale(0.9)';
                gov.style.transition = 'all 0.5s ease';

                setTimeout(() => {
                    gov.style.opacity = '1';
                    gov.style.transform = 'scale(1)';
                }, 100);
            }, index * 40);
        });
    });
</script>
